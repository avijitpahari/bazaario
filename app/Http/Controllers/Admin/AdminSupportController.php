<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSupportController extends Controller
{
    /**
     * Render the Live Agent Chat + Admin Dashboard.
     */
    public function index(Request $request): View
    {
        $admin = Auth::guard('admin')->user() ?? Auth::user();
        $tab = $request->query('tab', 'open');
        $selectedId = $request->query('conversation_id');

        // Tab counts
        $openCount = ChatConversation::whereIn('status', ['open', 'active'])->count();
        $pendingCount = ChatConversation::where('status', 'pending')->count();
        $resolvedCount = ChatConversation::whereIn('status', ['resolved', 'closed'])->count();

        // Query conversations for the selected tab
        $query = ChatConversation::with(['user', 'latestMessage', 'assignedAgent'])
            ->orderByDesc('last_message_at');

        if ($tab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($tab === 'resolved') {
            $query->whereIn('status', ['resolved', 'closed']);
        } else {
            $query->whereIn('status', ['open', 'active']);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $conversations = $query->paginate(20);

        // Select the active conversation
        $activeConversation = null;
        if ($selectedId) {
            $activeConversation = ChatConversation::with(['user.addresses', 'user.orders', 'messages', 'assignedAgent'])
                ->find($selectedId);
        }

        if (!$activeConversation && $conversations->isNotEmpty()) {
            $activeConversation = ChatConversation::with(['user.addresses', 'user.orders', 'messages', 'assignedAgent'])
                ->find($conversations->first()->id);
        }

        // Available agents
        $agents = User::whereIn('role', ['admin', 'manager', 'support'])
            ->select('id', 'name', 'email', 'role')
            ->get();

        return view('admin.support.index', compact(
            'conversations',
            'activeConversation',
            'openCount',
            'pendingCount',
            'resolvedCount',
            'tab',
            'agents',
            'admin'
        ));
    }

    /**
     * Send an agent reply to a conversation.
     */
    public function reply(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $request->validate([
            'message' => 'nullable|string|max:3000',
            'attachment' => 'nullable|image|max:10240',
        ]);

        $conversation = ChatConversation::findOrFail($id);
        $admin = Auth::guard('admin')->user() ?? Auth::user();

        $messageText = trim((string) $request->input('message'));
        $attachmentPath = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('support_chat', 'public');
            $attachmentType = 'image';
        }

        if (empty($messageText) && empty($attachmentPath)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Message or attachment required.'], 422);
            }
            return back()->with('error', 'Please provide a message or attachment.');
        }

        $msg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'sender_type' => 'agent',
            'sender_name' => $admin ? $admin->name : 'Live Agent',
            'message' => $messageText ?: 'Sent an image attachment.',
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'is_read' => true,
            'created_at' => now(),
        ]);

        // Auto-assign to replying agent if not already assigned
        if (!$conversation->assigned_agent_id && $admin) {
            $conversation->assigned_agent_id = $admin->id;
        }

        $conversation->last_message_at = now();
        $conversation->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $msg->id,
                    'role' => $msg->role,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $msg->sender_name,
                    'message' => $msg->message,
                    'attachment_url' => $msg->attachment_url,
                    'time' => $msg->created_at->format('h:i A'),
                ],
            ]);
        }

        return redirect()->route('admin.support.index', ['conversation_id' => $conversation->id, 'tab' => $conversation->status === 'resolved' ? 'resolved' : 'open'])
            ->with('success', 'Reply sent to customer.');
    }

    /**
     * Update conversation status, assigned agent, or tags.
     */
    public function updateStatus(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $conversation = ChatConversation::findOrFail($id);

        if ($request->has('status')) {
            $status = $request->input('status');
            if (in_array($status, ['open', 'pending', 'resolved', 'closed'])) {
                $conversation->status = $status;
            }
        }

        if ($request->has('assigned_agent_id')) {
            $conversation->assigned_agent_id = $request->input('assigned_agent_id') ?: null;
        }

        if ($request->has('tag')) {
            $tags = $conversation->tags ?? [];
            $newTag = trim((string) $request->input('tag'));
            if ($newTag && !in_array($newTag, $tags)) {
                $tags[] = $newTag;
                $conversation->tags = $tags;
            }
        }

        if ($request->has('remove_tag')) {
            $removeTag = $request->input('remove_tag');
            $tags = array_values(array_filter($conversation->tags ?? [], fn($t) => $t !== $removeTag));
            $conversation->tags = $tags;
        }

        $conversation->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'conversation' => $conversation]);
        }

        return back()->with('success', 'Conversation details updated.');
    }

    /**
     * Real-time polling endpoint for agent chat messages.
     */
    public function pollMessages(Request $request, int $id): JsonResponse
    {
        $afterId = (int) $request->input('after_id', 0);

        $newMessages = ChatMessage::where('conversation_id', $id)
            ->where('id', '>', $afterId)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'role' => $msg->role,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $msg->sender_name ?: ($msg->sender_type === 'user' ? 'Customer' : 'Agent'),
                    'message' => $msg->message,
                    'attachment_url' => $msg->attachment_url,
                    'attachment_type' => $msg->attachment_type,
                    'time' => $msg->created_at ? $msg->created_at->format('h:i A') : now()->format('h:i A'),
                    'is_read' => (bool) $msg->is_read,
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $newMessages,
        ]);
    }
}
