<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentChatController extends Controller
{
    /**
     * Live Chat Workstation Console for Support Agents.
     */
    public function index(Request $request): View
    {
        $agent = Auth::guard('user')->user() ?? Auth::user();
        $selectedId = $request->query('conversation_id');

        $conversations = ChatConversation::with(['user', 'assignedAgent', 'messages' => fn($q) => $q->latest('id')->take(1)])
            ->orderByRaw("CASE status WHEN 'open' THEN 1 WHEN 'pending' THEN 2 ELSE 3 END")
            ->latest('last_message_at')
            ->paginate(20)
            ->withQueryString();

        $activeConversation = null;
        if ($selectedId) {
            $activeConversation = ChatConversation::with(['messages', 'user', 'assignedAgent'])->find($selectedId);
        } elseif ($conversations->isNotEmpty()) {
            $activeConversation = ChatConversation::with(['messages', 'user', 'assignedAgent'])->find($conversations->first()->id);
        }

        return view('agent.chats.index', compact('conversations', 'activeConversation', 'agent'));
    }

    /**
     * Send agent reply in live chat conversation.
     */
    public function reply(Request $request, ChatConversation $conversation): JsonResponse|RedirectResponse
    {
        $agent = Auth::guard('user')->user() ?? Auth::user();

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'image', 'max:10240'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support_chat', 'public');
        }

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'sender_type' => 'agent',
            'sender_name' => $agent->name . ' (Support)',
            'message' => trim($validated['message']),
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentPath ? 'image' : null,
            'is_read' => true,
        ]);

        $conversation->update([
            'assigned_agent_id' => $agent->id,
            'last_message_at' => now(),
            'status' => 'open',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'role' => 'assistant',
                    'sender_type' => 'agent',
                    'sender_name' => $message->sender_name,
                    'message' => $message->message,
                    'attachment_url' => $message->attachment_url,
                    'time' => $message->created_at->format('h:i A'),
                ],
            ]);
        }

        return back()->with('success', 'Message sent.');
    }

    /**
     * Poll new messages for the conversation.
     */
    public function poll(Request $request, ChatConversation $conversation): JsonResponse
    {
        $afterId = (int) $request->query('after_id', 0);

        $messages = $conversation->messages()
            ->where('id', '>', $afterId)
            ->oldest('id')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'role' => $m->role,
                'sender_type' => $m->sender_type,
                'sender_name' => $m->sender_name,
                'message' => $m->message,
                'attachment_url' => $m->attachment_url,
                'time' => $m->created_at->format('h:i A'),
            ]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Update conversation status (resolve/close).
     */
    public function updateStatus(Request $request, ChatConversation $conversation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,pending,resolved,closed'],
        ]);

        $conversation->update(['status' => $validated['status']]);

        return back()->with('success', 'Conversation status updated to ' . ucfirst($validated['status']) . '.');
    }
}
