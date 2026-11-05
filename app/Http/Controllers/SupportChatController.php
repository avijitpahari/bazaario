<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Order;
use App\Models\Product;
use App\Services\GeminiApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupportChatController extends Controller
{
    /**
     * Initialize support session: get or create active conversation.
     */
    public function init(Request $request): JsonResponse
    {
        $user = Auth::guard('user')->user() ?? Auth::user();
        $visitorId = $request->input('visitor_id') ?: (string) Str::uuid();

        $conversation = null;

        if ($user) {
            $conversation = ChatConversation::where('user_id', $user->id)
                ->whereIn('status', ['open', 'active', 'pending'])
                ->latest()
                ->first();

            if (!$conversation) {
                $conversation = ChatConversation::create([
                    'user_id' => $user->id,
                    'visitor_id' => $visitorId,
                    'guest_name' => $user->name,
                    'guest_email' => $user->email,
                    'title' => 'Support Request from ' . $user->name,
                    'status' => 'open',
                    'department' => 'general',
                    'priority' => 'medium',
                    'tags' => ['Customer', 'Website'],
                    'last_message_at' => now(),
                ]);

                // Welcome message
                ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'sender_type' => 'bot',
                    'sender_name' => 'Bazaario Support',
                    'message' => "Hi {$user->name}! Welcome to Bazaario support. How can we help you today?",
                    'created_at' => now(),
                ]);
            }
        } else {
            // Guest visitor
            $conversation = ChatConversation::where('visitor_id', $visitorId)
                ->whereIn('status', ['open', 'active', 'pending'])
                ->latest()
                ->first();

            if (!$conversation) {
                $conversation = ChatConversation::create([
                    'visitor_id' => $visitorId,
                    'title' => 'Visitor Inquiry (' . substr($visitorId, 0, 6) . ')',
                    'status' => 'open',
                    'department' => 'general',
                    'priority' => 'medium',
                    'tags' => ['Guest', 'Website'],
                    'last_message_at' => now(),
                ]);

                // Initial greeting
                ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'sender_type' => 'bot',
                    'sender_name' => 'Support Team',
                    'message' => "Hi there! Welcome to our website. How can we help you today?",
                    'created_at' => now(),
                ]);
            }
        }

        $messages = $conversation->messages()->get()->map(function ($msg) {
            return [
                'id' => $msg->id,
                'role' => $msg->role,
                'sender_type' => $msg->sender_type,
                'sender_name' => $msg->sender_name ?: ($msg->sender_type === 'user' ? 'You' : 'Support Team'),
                'message' => $msg->message,
                'attachment_url' => $msg->attachment_url,
                'attachment_type' => $msg->attachment_type,
                'time' => $msg->created_at ? $msg->created_at->format('h:i A') : now()->format('h:i A'),
                'is_read' => (bool) $msg->is_read,
            ];
        });

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'visitor_id' => $visitorId,
            'status' => $conversation->status,
            'messages' => $messages,
            'is_agent_online' => true,
            'customer' => [
                'name' => $user ? $user->name : ($conversation->guest_name ?: 'Visitor'),
                'is_logged_in' => (bool) $user,
            ],
        ]);
    }

    /**
     * Send a user message in the active support chat.
     */
    public function sendMessage(Request $request, GeminiApiService $gemini): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,id',
            'message' => 'nullable|string|max:2000',
            'attachment' => 'nullable|image|max:10240', // max 10MB
        ]);

        $conversation = ChatConversation::findOrFail($request->input('conversation_id'));
        $user = Auth::guard('user')->user() ?? Auth::user();

        $messageText = trim((string) $request->input('message'));
        $attachmentPath = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('support_chat', 'public');
            $attachmentType = 'image';
        }

        if (empty($messageText) && empty($attachmentPath)) {
            return response()->json(['success' => false, 'error' => 'Message or image required.'], 422);
        }

        // Save User Message
        $userMsg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'sender_type' => 'user',
            'sender_name' => $user ? $user->name : ($conversation->guest_name ?: 'You'),
            'message' => $messageText ?: 'Sent an image attachment.',
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'created_at' => now(),
        ]);

        $conversation->update([
            'last_message_at' => now(),
            'status' => in_array($conversation->status, ['resolved', 'closed']) ? 'open' : $conversation->status,
        ]);

        // Automated AI reply if not actively locked to a live human agent
        $botReply = null;
        $shouldAiReply = empty($conversation->assigned_agent_id);

        if ($shouldAiReply && !empty($messageText)) {
            try {
                $history = $conversation->messages()
                    ->latest('id')
                    ->take(6)
                    ->get()
                    ->reverse()
                    ->map(fn($m) => ['role' => $m->role, 'content' => $m->message])
                    ->values()
                    ->toArray();

                $aiResult = $gemini->chat($messageText, $history, $user, $conversation->id);
                $replyText = $aiResult['reply'] ?? "Thanks for contacting us! An agent will also review your conversation shortly.";

                $botMsg = ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'sender_type' => 'bot',
                    'sender_name' => 'Support Team',
                    'message' => $replyText,
                    'created_at' => now(),
                ]);

                $botReply = [
                    'id' => $botMsg->id,
                    'role' => 'assistant',
                    'sender_type' => 'bot',
                    'sender_name' => 'Support Team',
                    'message' => $botMsg->message,
                    'attachment_url' => null,
                    'attachment_type' => null,
                    'time' => $botMsg->created_at->format('h:i A'),
                    'is_read' => false,
                ];
            } catch (\Throwable $e) {
                Log::warning('AI Support Chat response error: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'sent_message' => [
                'id' => $userMsg->id,
                'role' => 'user',
                'sender_type' => 'user',
                'sender_name' => $userMsg->sender_name,
                'message' => $userMsg->message,
                'attachment_url' => $userMsg->attachment_url,
                'attachment_type' => $userMsg->attachment_type,
                'time' => $userMsg->created_at->format('h:i A'),
            ],
            'bot_reply' => $botReply,
        ]);
    }

    /**
     * Handle quick-action chips clicked in the widget.
     */
    public function quickReply(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => 'required|exists:chat_conversations,id',
            'action' => 'required|string',
        ]);

        $conversation = ChatConversation::findOrFail($request->input('conversation_id'));
        $user = Auth::guard('user')->user() ?? Auth::user();
        $action = $request->input('action');

        $userLabel = '';
        $supportResponse = '';
        $sampleImage = null;

        switch ($action) {
            case 'track_order':
                $userLabel = 'Track my order';
                if ($user) {
                    $recentOrder = Order::where('user_id', $user->id)->latest()->first();
                    if ($recentOrder) {
                        $supportResponse = "Here is your latest order **#{$recentOrder->order_number}**:\n"
                            . "• **Status**: " . ucfirst($recentOrder->order_status) . "\n"
                            . "• **Total Amount**: ₹" . number_format($recentOrder->total_amount, 2) . "\n"
                            . "• **Courier**: BlueDart Express (Telemetry: In-Transit)\n"
                            . "You can view full telemetry anytime under your Orders portal.";
                    } else {
                        $supportResponse = "You have no active orders yet. If you have an Order ID, type it here (e.g. `BZ-2026-00128`) and I will track it for you!";
                    }
                } else {
                    $supportResponse = "Please provide your Order Number (e.g. `BZ-2026-00125`) or your email address so I can check your delivery telemetry right away!";
                }
                break;

            case 'product_info':
                $userLabel = 'Product information';
                $supportResponse = "We have fresh seasonal produce, electronics, artisanal crafts, and pantry essentials in stock!\n\n"
                    . "You can type any product name or question (e.g. *\"Do you have organic honey?\"* or *\"Show shoe photos\"*), and I'll find availability, pricing, and specs for you.";
                break;

            case 'talk_agent':
                $userLabel = 'Talk to an agent';
                $conversation->update([
                    'status' => 'open',
                    'priority' => 'high',
                    'tags' => array_unique(array_merge($conversation->tags ?? [], ['Live Agent Escalation'])),
                ]);
                $supportResponse = "🔔 I've escalated your conversation to our live merchant support desk! A support agent is being connected to your session. How may we assist you today?";
                break;

            case 'other':
            default:
                $userLabel = 'Other question';
                $supportResponse = "Feel free to ask anything about shipping, refunds, payment options, or vendor registration. Our team is here 24/7!";
                break;
        }

        // Save User Quick Reply Bubble
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'sender_type' => 'user',
            'sender_name' => $user ? $user->name : 'You',
            'message' => $userLabel,
            'created_at' => now(),
        ]);

        // Save Support Bot Response Bubble
        $botMsg = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'sender_type' => 'bot',
            'sender_name' => 'Support Team',
            'message' => $supportResponse,
            'created_at' => now(),
        ]);

        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'user_message' => [
                'role' => 'user',
                'message' => $userLabel,
                'time' => now()->format('h:i A'),
            ],
            'support_message' => [
                'role' => 'assistant',
                'sender_type' => 'bot',
                'sender_name' => 'Support Team',
                'message' => $supportResponse,
                'time' => now()->format('h:i A'),
            ],
        ]);
    }

    /**
     * Poll new messages for the customer widget.
     */
    public function poll(Request $request): JsonResponse
    {
        $conversationId = (int) $request->input('conversation_id');
        $afterId = (int) $request->input('after_id', 0);

        if (!$conversationId) {
            return response()->json(['success' => false, 'messages' => []]);
        }

        $conversation = ChatConversation::find($conversationId);
        if (!$conversation) {
            return response()->json(['success' => false, 'messages' => []]);
        }

        $newMessages = ChatMessage::where('conversation_id', $conversationId)
            ->where('id', '>', $afterId)
            ->where('sender_type', '!=', 'user') // Only messages from agent or bot
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'role' => $msg->role,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $msg->sender_name ?: 'Support Team',
                    'message' => $msg->message,
                    'attachment_url' => $msg->attachment_url,
                    'attachment_type' => $msg->attachment_type,
                    'time' => $msg->created_at ? $msg->created_at->format('h:i A') : now()->format('h:i A'),
                    'is_read' => (bool) $msg->is_read,
                ];
            });

        return response()->json([
            'success' => true,
            'status' => $conversation->status,
            'messages' => $newMessages,
        ]);
    }
}
