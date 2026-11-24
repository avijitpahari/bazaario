<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\Payout;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminSupportController extends Controller
{
    /**
     * Render the Unified Support Dashboard (Live Chats + Support Tickets).
     */
    public function index(Request $request): View
    {
        $admin = Auth::guard('admin')->user() ?? Auth::user();
        $viewMode = $request->query('view', 'chats'); // chats | tickets | escalated
        $tab = $request->query('tab', 'open');
        $selectedId = $request->query('conversation_id');
        $selectedTicketId = $request->query('ticket_id');

        // ==================== LIVE CHAT COUNTS ====================
        $chatOpenCount = ChatConversation::whereIn('status', ['open', 'active'])->count();
        $chatPendingCount = ChatConversation::where('status', 'pending')->count();
        $chatResolvedCount = ChatConversation::whereIn('status', ['resolved', 'closed'])->count();

        // ==================== SUPPORT TICKET COUNTS ====================
        $ticketOpenCount = SupportTicket::whereIn('status', ['open', 'in_progress', 'waiting_customer', 'waiting_seller'])->count();
        $ticketEscalatedCount = SupportTicket::where('is_escalated', true)->count();
        $ticketResolvedCount = SupportTicket::whereIn('status', ['resolved', 'closed'])->count();
        $ticketSellerCount = SupportTicket::whereNotNull('seller_id')->count();
        $ticketCustomerCount = SupportTicket::whereNull('seller_id')->count();

        // ==================== QUERY BASED ON VIEW MODE ====================
        $conversations = collect();
        $tickets = collect();
        $activeConversation = null;
        $activeTicket = null;

        if ($viewMode === 'chats') {
            // Live Chat Conversations
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
            if ($selectedId) {
                $activeConversation = ChatConversation::with(['user.addresses', 'user.orders', 'messages', 'assignedAgent'])
                    ->find($selectedId);
            }

            if (!$activeConversation && $conversations->isNotEmpty()) {
                $activeConversation = ChatConversation::with(['user.addresses', 'user.orders', 'messages', 'assignedAgent'])
                    ->find($conversations->first()->id);
            }
        } elseif ($viewMode === 'tickets' || $viewMode === 'escalated') {
            // Support Tickets
            $ticketQuery = SupportTicket::with(['user', 'seller', 'order', 'latestMessage', 'assignedAgent'])
                ->orderByDesc('updated_at');

            // Filter by view mode
            if ($viewMode === 'escalated') {
                $ticketQuery->where('is_escalated', true);
            }

            // Filter by tab (status)
            if ($tab === 'open') {
                $ticketQuery->whereIn('status', ['open', 'in_progress', 'waiting_customer', 'waiting_seller']);
            } elseif ($tab === 'resolved') {
                $ticketQuery->whereIn('status', ['resolved', 'closed']);
            } elseif ($tab === 'seller') {
                $ticketQuery->whereNotNull('seller_id');
            } elseif ($tab === 'customer') {
                $ticketQuery->whereNull('seller_id');
            }

            // Search
            if ($search = $request->query('search')) {
                $ticketQuery->where(function ($q) use ($search) {
                    $q->where('ticket_number', 'like', "%{$search}%")
                      ->orWhere('subject', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                      })
                      ->orWhereHas('seller', function ($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by priority
            if ($priority = $request->query('priority')) {
                $ticketQuery->where('priority', $priority);
            }

            // Filter by category
            if ($category = $request->query('category')) {
                $ticketQuery->where('category', $category);
            }

            $tickets = $ticketQuery->paginate(20)->withQueryString();

            // Select the active ticket
            if ($selectedTicketId) {
                $activeTicket = SupportTicket::with(['user', 'seller', 'order', 'messages.user', 'orderItem.product', 'assignedAgent'])
                    ->find($selectedTicketId);
            }

            if (!$activeTicket && $tickets->isNotEmpty()) {
                $activeTicket = SupportTicket::with(['user', 'seller', 'order', 'messages.user', 'orderItem.product', 'assignedAgent'])
                    ->find($tickets->first()->id);
            }
        }

        // Check for related OrderReturn for active ticket
        $relatedReturn = null;
        if ($activeTicket && $activeTicket->order_id) {
            $relatedReturn = OrderReturn::where('order_id', $activeTicket->order_id)
                ->whereIn('status', ['requested', 'approved', 'pickup_scheduled', 'received', 'refund_processing', 'refunded'])
                ->with(['orderItem', 'user'])
                ->latest()
                ->first();
        }

        // Available agents
        $agents = User::whereIn('role', ['admin', 'manager', 'support', 'support_agent'])
            ->select('id', 'name', 'email', 'role', 'department', 'staff_id')
            ->get();

        // Categories for filter dropdown
        $ticketCategories = ['order_issue', 'return_refund', 'delivery_delay', 'auction', 'billing', 'escrow_payout', 'kyc_clearance', 'listing_error', 'account_issue', 'general'];

        return view('admin.support.index', compact(
            'conversations',
            'activeConversation',
            'chatOpenCount',
            'chatPendingCount',
            'chatResolvedCount',
            'tickets',
            'activeTicket',
            'relatedReturn',
            'ticketOpenCount',
            'ticketEscalatedCount',
            'ticketResolvedCount',
            'ticketSellerCount',
            'ticketCustomerCount',
            'tab',
            'viewMode',
            'agents',
            'admin',
            'ticketCategories'
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
                    'is_read' => (bool) ($msg->is_read ?? false),
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $newMessages,
        ]);
    }

    /**
     * Show Support Ticket detail for arbitration.
     * Reuses the index view logic with all required sidebar data.
     */
    public function showSupportTicket(Request $request, SupportTicket $ticket): View|RedirectResponse
    {
        $admin = Auth::guard('admin')->user() ?? Auth::user();
        abort_unless($admin && in_array($admin->role, ['admin', 'manager', 'support']), 403);

        $ticket->load([
            'user',
            'seller.sellerProfile',
            'order.user',
            'order.sellerOrders.seller.sellerProfile',
            'orderItem.product',
            'messages.user',
            'assignedAgent',
        ]);

        // Check for related OrderReturn (dispute)
        $relatedReturn = null;
        if ($ticket->order_id) {
            $relatedReturn = OrderReturn::where('order_id', $ticket->order_id)
                ->whereIn('status', ['requested', 'approved', 'pickup_scheduled', 'received', 'refund_processing', 'refunded'])
                ->with(['orderItem', 'user'])
                ->latest()
                ->first();
        }

        // ==================== LIVE CHAT COUNTS ====================
        $chatOpenCount = ChatConversation::whereIn('status', ['open', 'active'])->count();
        $chatPendingCount = ChatConversation::where('status', 'pending')->count();
        $chatResolvedCount = ChatConversation::whereIn('status', ['resolved', 'closed'])->count();

        // ==================== SUPPORT TICKET COUNTS ====================
        $ticketOpenCount = SupportTicket::whereIn('status', ['open', 'in_progress', 'waiting_customer', 'waiting_seller'])->count();
        $ticketEscalatedCount = SupportTicket::where('is_escalated', true)->count();
        $ticketResolvedCount = SupportTicket::whereIn('status', ['resolved', 'closed'])->count();
        $ticketSellerCount = SupportTicket::whereNotNull('seller_id')->count();
        $ticketCustomerCount = SupportTicket::whereNull('seller_id')->count();

        // Get all tickets for sidebar navigation
        $viewMode = $request->query('view', 'tickets');
        $tab = $request->query('tab', 'open');

        $ticketQuery = SupportTicket::with(['user', 'seller', 'order', 'latestMessage', 'assignedAgent'])
            ->orderByDesc('updated_at');

        // Filter by view mode
        if ($viewMode === 'escalated') {
            $ticketQuery->where('is_escalated', true);
        }

        // Filter by tab (status)
        if ($tab === 'open') {
            $ticketQuery->whereIn('status', ['open', 'in_progress', 'waiting_customer', 'waiting_seller']);
        } elseif ($tab === 'resolved') {
            $ticketQuery->whereIn('status', ['resolved', 'closed']);
        } elseif ($tab === 'seller') {
            $ticketQuery->whereNotNull('seller_id');
        } elseif ($tab === 'customer') {
            $ticketQuery->whereNull('seller_id');
        }

        // Search
        if ($search = $request->query('search')) {
            $ticketQuery->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('seller', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by priority
        if ($priority = $request->query('priority')) {
            $ticketQuery->where('priority', $priority);
        }

        // Filter by category
        if ($category = $request->query('category')) {
            $ticketQuery->where('category', $category);
        }

        $tickets = $ticketQuery->paginate(20)->withQueryString();

        // Available agents
        $agents = User::whereIn('role', ['admin', 'manager', 'support', 'support_agent'])
            ->select('id', 'name', 'email', 'role', 'department', 'staff_id')
            ->get();

        // Categories for filter dropdown
        $ticketCategories = ['order_issue', 'return_refund', 'delivery_delay', 'auction', 'billing', 'escrow_payout', 'kyc_clearance', 'listing_error', 'account_issue', 'general'];

        $activeTicket = $ticket;

        return view('admin.support.ticket-show', compact(
            'ticket',
            'activeTicket', // alias for ticket
            'tickets',
            'relatedReturn',
            'chatOpenCount',
            'chatPendingCount',
            'chatResolvedCount',
            'ticketOpenCount',
            'ticketEscalatedCount',
            'ticketResolvedCount',
            'ticketSellerCount',
            'ticketCustomerCount',
            'tab',
            'viewMode',
            'agents',
            'admin',
            'ticketCategories'
        ));
    }

    /**
     * Arbitrate Support Ticket Dispute (multi-party mediation).
     */
    public function arbitrateSupportTicket(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $admin = Auth::guard('admin')->user() ?? Auth::user();
        abort_unless($admin && in_array($admin->role, ['admin', 'manager', 'support']), 403);

        $validated = $request->validate([
            'decision' => ['required', 'in:approve_refund,reject_refund,release_escrow,custom'],
            'arbitration_notes' => ['nullable', 'string', 'max:2000'],
            'refund_amount' => ['nullable', 'numeric', 'min:0', 'max:10000000'],
            'custom_action' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            return DB::transaction(function () use ($validated, $ticket, $admin) {
                $decision = $validated['decision'];
                $notes = $validated['arbitration_notes'] ?? '';
                $refundAmount = $validated['refund_amount'] ?? 0;
                $customAction = $validated['custom_action'] ?? '';

                $ticket->update([
                    'status' => in_array($decision, ['approve_refund', 'release_escrow']) ? 'resolved' : 'closed',
                    'resolved_at' => now(),
                    'is_escalated' => false,
                ]);

                // Create system message with arbitration decision
                $decisionLabel = match($decision) {
                    'approve_refund' => "✅ APPROVED REFUND",
                    'reject_refund' => "❌ REJECTED REFUND",
                    'release_escrow' => "💰 RELEASED ESCROW TO SELLER",
                    'custom' => "⚖️ CUSTOM ARBITRATION: {$customAction}",
                    default => "ARBITRATION DECISION",
                };

                $messageText = "{$decisionLabel} by Admin {$admin->name}.\n";
                if ($notes) {
                    $messageText .= "Reasoning: {$notes}\n";
                }
                if ($refundAmount > 0) {
                    $messageText .= "Refund Amount: ₹" . number_format($refundAmount, 2) . "\n";
                }

                SupportTicketMessage::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $admin->id,
                    'sender_type' => 'system',
                    'message' => $messageText,
                ]);

                // Handle order/payout actions based on decision
                if ($ticket->order_id && $ticket->order) {
                    $order = $ticket->order;
                    $sellerOrder = $order->sellerOrders()->first();

                    if ($decision === 'approve_refund') {
                        // Approve refund to buyer
                        $order->update(['payment_status' => 'refunded']);

                        // Create/Update OrderReturn record
                        if ($ticket->order_item_id) {
                            OrderReturn::updateOrCreate(
                                ['order_id' => $order->id, 'order_item_id' => $ticket->order_item_id],
                                [
                                    'user_id' => $ticket->user_id,
                                    'reason' => 'Admin arbitration: ' . ($notes ?: 'Refund approved'),
                                    'description' => $messageText,
                                    'status' => 'refunded',
                                    'refund_amount' => $refundAmount ?: $order->total_amount,
                                    'requested_at' => now(),
                                    'approved_at' => now(),
                                    'completed_at' => now(),
                                ]
                            );
                        }

                        // Void pending payout
                        if ($sellerOrder) {
                            $sellerOrder->update(['status' => 'returned']);
                            Payout::where('seller_order_id', $sellerOrder->id)
                                ->where('status', 'pending')
                                ->update([
                                    'status' => 'failed',
                                    'payout_reference' => 'ARBITRATION-REFUNDED-' . date('Ymd'),
                                ]);
                        }

                    } elseif ($decision === 'reject_refund' || $decision === 'release_escrow') {
                        // Reject refund / release escrow to seller
                        if ($sellerOrder) {
                            $sellerOrder->update(['status' => 'delivered']);
                        }
                    }
                }

                // AdminFeed notification
                try {
                    \App\Models\AdminFeed::record(
                        title: "Support Ticket Arbitrated: #{$ticket->ticket_number}",
                        description: "Admin {$admin->name} arbitrated ticket '{$ticket->subject}' with decision: {$decisionLabel}.",
                        type: 'support',
                        icon: 'gavel',
                        badgeText: 'ARBITRATED',
                        badgeColor: $decision === 'approve_refund' ? 'rose' : 'emerald',
                        actionUrl: route('admin.support.tickets.show', $ticket),
                    );
                } catch (\Throwable $feedEx) {}

                return redirect()->route('admin.support.tickets.show', $ticket)
                    ->with('success', "Support ticket #{$ticket->ticket_number} has been arbitrated.");
            });
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to arbitrate ticket: " . $e->getMessage());
        }
    }

    /**
     * Post an admin / agent reply to a support ticket.
     */
    public function replySupportTicket(Request $request, SupportTicket $ticket): JsonResponse|RedirectResponse
    {
        $admin = Auth::guard('admin')->user() ?? Auth::user();
        abort_unless($admin && in_array($admin->role, ['admin', 'manager', 'support']), 403);

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:3000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf'],
            'status' => ['nullable', 'string', 'in:open,in_progress,waiting_customer,waiting_seller,resolved,closed'],
        ]);

        $messageText = trim((string) ($validated['message'] ?? ''));
        $attachmentPath = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('support_attachments', 'public');
            $attachmentType = $file->getClientOriginalExtension();
        }

        if (empty($messageText) && empty($attachmentPath)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'error' => 'Please provide a message or attachment.'], 422);
            }
            return back()->with('error', 'Please provide a message or attachment.');
        }

        $msg = SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'sender_type' => 'admin',
            'message' => $messageText ?: 'Attached document.',
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
        ]);

        $ticketUpdates = [
            'updated_at' => now(),
        ];

        if (!empty($validated['status'])) {
            $ticketUpdates['status'] = $validated['status'];
            if (in_array($validated['status'], ['resolved', 'closed'])) {
                $ticketUpdates['resolved_at'] = now();
            }
        } elseif ($ticket->status === 'open') {
            $ticketUpdates['status'] = 'in_progress';
        }

        $ticket->update($ticketUpdates);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $msg->id,
                    'sender_type' => 'admin',
                    'sender_name' => $admin->name ?? 'Support Desk',
                    'message' => $msg->message,
                    'attachment_url' => $msg->attachment_url,
                    'attachment_type' => $msg->attachment_type,
                    'time' => $msg->created_at->format('h:i A'),
                ],
                'ticket_status' => $ticket->status,
            ]);
        }

        return redirect()->route('admin.support.tickets.show', $ticket)
            ->with('success', 'Admin reply posted to ticket #' . $ticket->ticket_number);
    }

    /**
     * Quick status or priority update for support tickets.
     */
    public function updateTicketStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $admin = Auth::guard('admin')->user() ?? Auth::user();
        abort_unless($admin && in_array($admin->role, ['admin', 'manager', 'support']), 403);

        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:open,in_progress,waiting_customer,waiting_seller,resolved,closed,escalated'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,urgent'],
            'assigned_agent_id' => ['nullable', 'exists:users,id'],
        ]);

        $updates = ['updated_at' => now()];

        if (!empty($validated['status'])) {
            $updates['status'] = $validated['status'];
            if (in_array($validated['status'], ['resolved', 'closed'])) {
                $updates['resolved_at'] = now();
            }
            if ($validated['status'] === 'escalated') {
                $updates['is_escalated'] = true;
                $updates['escalated_at'] = now();
            }
        }

        if (!empty($validated['priority'])) {
            $updates['priority'] = $validated['priority'];
        }

        if (array_key_exists('assigned_agent_id', $validated)) {
            $updates['assigned_agent_id'] = $validated['assigned_agent_id'];
        }

        $ticket->update($updates);

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $admin->id,
            'sender_type' => 'system',
            'message' => "Admin {$admin->name} updated ticket properties: Status: " . ucfirst(str_replace('_', ' ', $ticket->status)) . " | Priority: " . ucfirst($ticket->priority),
        ]);

        return redirect()->route('admin.support.tickets.show', $ticket)
            ->with('success', "Ticket #{$ticket->ticket_number} updated successfully.");
    }

    /**
     * Poll for new Support Ticket messages (real-time updates).
     */
    public function pollTicketMessages(Request $request, SupportTicket $ticket): JsonResponse
    {
        $afterId = (int) $request->input('after_id', 0);

        $newMessages = SupportTicketMessage::where('ticket_id', $ticket->id)
            ->where('id', '>', $afterId)
            ->with(['user.sellerProfile'])
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'sender_type' => $msg->sender_type,
                    'sender_name' => $msg->user?->sellerProfile?->shop_name ?? ($msg->user?->name ?? ($msg->sender_type === 'system' ? 'System' : 'Support Desk')),
                    'message' => $msg->message,
                    'attachment_url' => $msg->attachment_url,
                    'attachment_type' => $msg->attachment_type,
                    'time' => $msg->created_at ? $msg->created_at->format('h:i A') : now()->format('h:i A'),
                    'is_read' => (bool) ($msg->is_read ?? false),
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $newMessages,
        ]);
    }
}
