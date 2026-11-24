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

                $lowerText = strtolower($messageText);
                $fallbackReply = "Thanks for reaching out! Our support desk has received your message and an agent will follow up shortly.";
                if (str_contains($lowerText, 'return') || str_contains($lowerText, 'refund')) {
                    $fallbackReply = "🔄 **Returns & Refunds**: Bazaario offers a 7-day return window on craft & pantry goods, and a 48-hour freshness guarantee on farm produce. You can initiate returns in your [Returns Portal](" . route('user.returns') . ") or [Open a Ticket](" . route('user.support.tickets.create') . ").";
                } elseif (str_contains($lowerText, 'track') || str_contains($lowerText, 'order')) {
                    $fallbackReply = "📦 **Order Tracking**: You can inspect real-time courier telemetry and milestones in your [Orders Portal](" . route('user.orders.index') . ").";
                } elseif (str_contains($lowerText, 'ticket') || str_contains($lowerText, 'complaint')) {
                    $fallbackReply = "🎫 **Support Tickets**: For formal dispute intervention, you can [Open a Support Ticket](" . route('user.support.tickets.create') . ") with unboxing photos attached.";
                } elseif (str_contains($lowerText, 'auction') || str_contains($lowerText, 'bid')) {
                    $fallbackReply = "🔨 **Wholesale Auctions**: Check live spot floor bidding and schedule lots in our [Auctions Terminal](" . route('auctions.index') . ").";
                }

                $botMsg = ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'role' => 'assistant',
                    'sender_type' => 'bot',
                    'sender_name' => 'Support Team',
                    'message' => $fallbackReply,
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
                        $supportResponse = "📦 **Order Telemetry for #{$recentOrder->order_number}**:\n"
                            . "• **Status**: " . ucfirst($recentOrder->order_status) . "\n"
                            . "• **Total Amount**: ₹" . number_format($recentOrder->total_amount, 2) . "\n"
                            . "• **Courier**: BlueDart Hyperlocal Fleet (Telemetry: In-Transit)\n"
                            . "• **Delivery Slot**: Morning 8 AM - 12 PM\n\n"
                            . "You can view full real-time milestone telemetry in your [Orders Portal](" . route('user.orders.show', $recentOrder->id) . ").";
                    } else {
                        $supportResponse = "You have no active orders yet. If you have an Order Number, type it here (e.g. `BZ-ORD-101`) and I will track it for you!";
                    }
                } else {
                    $supportResponse = "Please provide your Order Number (e.g. `BZ-ORD-101`) or log in so I can check your delivery telemetry right away!";
                }
                break;

            case 'faq_returns':
                $userLabel = 'How do returns & refunds work?';
                $supportResponse = "🔄 **Bazaario 7-Day Return & Escrow Refund Policy**\n\n"
                    . "• **Eligible Items**: Artisanal crafts, packaged pantry items, and durable goods have a full 7-day return window. Perishable agricultural items have a 48-hour freshness guarantee.\n"
                    . "• **Doorstep Inspection & Pickup**: Free courier pickup arranged directly from your delivery address.\n"
                    . "• **Escrow Safeguard**: Your payment is safely held in Escrow and is never released to the seller until delivery is inspected.\n"
                    . "• **Instant Refund**: Approved refunds are transferred directly to your original payment method (UPI / Bank / Card) within 24 hours.\n\n"
                    . "👉 [Go to Returns Portal](" . route('user.returns') . ") or [Create a Support Ticket](" . route('user.support.tickets.create') . ")";
                break;

            case 'faq_auctions':
                $userLabel = 'How do Wholesale Auctions work?';
                $supportResponse = "🔨 **Bazaario Live Wholesale Spot Auctions**\n\n"
                    . "• **APMC Spot Floor**: Farmers and verified master artisans list bulk consignments on live scheduled bidding floors.\n"
                    . "• **Floor & Reserve Prices**: Opening bids start at the seller's floor price. The secret reserve floor ensures produce is never sold below fair production costs.\n"
                    . "• **Anti-Snipe Guard**: Any bid placed in the final 2 minutes automatically triggers a +2 minute soft-close extension.\n"
                    . "• **Hammer Allocation**: The winning bidder completes escrow checkout for immediate dispatch.\n\n"
                    . "👉 [Browse Live Auctions](" . route('auctions.index') . ")";
                break;

            case 'faq_payments':
                $userLabel = 'What payment methods & escrow protections exist?';
                $supportResponse = "🛡️ **100% Escrow Buyer Protection & Payments**\n\n"
                    . "• **Payment Options**: UPI (Google Pay, PhonePe, Paytm, BHIM), Credit/Debit Cards, NetBanking, and Cash on Delivery (COD).\n"
                    . "• **Escrow Vault**: All buyer funds are locked in an APMC-grade escrow vault. Merchants are paid only after delivery is successfully verified.\n"
                    . "• **Zero Hidden Fees**: No transaction surcharges or hidden platform fees on buyer payments.";
                break;

            case 'create_ticket':
                $userLabel = 'Open a Support Ticket';
                $supportResponse = "🎫 **Open a Dedicated Support Ticket**\n\n"
                    . "Need formal assistance with an order, damaged item, or dispute?\n"
                    . "You can open an official support ticket linked to your order with photos and documents attached.\n\n"
                    . "👉 [Click here to Create Support Ticket](" . route('user.support.tickets.create') . ")\n\n"
                    . "Our dedicated dispute arbitration team and verified merchants respond within 2 to 4 hours.";
                break;

            case 'product_info':
                $userLabel = 'Product information & sourcing';
                $supportResponse = "🌿 We offer certified GI-tagged produce, organic farm harvest, artisanal handlooms, and terracotta crafts directly from verified Indian producers!\n\n"
                    . "Type any item name (e.g. *\"Alphonso mangoes\"*, *\"Kashmiri apples\"*, *\"Handmade silk saree\"*) to check live availability, farm origin, and pricing.";
                break;

            case 'talk_agent':
                $userLabel = 'Talk to a live agent';
                $conversation->update([
                    'status' => 'open',
                    'priority' => 'high',
                    'tags' => array_unique(array_merge($conversation->tags ?? [], ['Live Agent Escalation'])),
                ]);
                $supportResponse = "🔔 **Connected to Live Support Desk**\n\n"
                    . "A dedicated customer care specialist has been alerted and assigned to your session. Please describe your question or issue, and we will assist you immediately!";
                break;

            case 'other':
            default:
                $userLabel = 'Other question';
                $supportResponse = "Feel free to ask anything about order tracking, refunds, payment options, wholesale auctions, or store registration. Our team is here 24/7!\n\n"
                    . "You can also [Open a Support Ticket](" . route('user.support.tickets.create') . ") for formal dispute resolution.";
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
