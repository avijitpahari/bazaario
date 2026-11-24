<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\SellerOrder;
use App\Models\SellerProfile;
use Illuminate\View\View;

class SellerSupportController extends Controller
{
    /**
     * Seller Customer Care Desk: Listing customer inquiries and tickets for this store.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();

        if (!$user || $user->role !== 'seller') {
            return redirect()->route('login');
        }

        $sellerId = $user->id;
        $statusFilter = $request->query('status');

        $query = SupportTicket::where('seller_id', $sellerId)
            ->with(['user', 'order', 'latestMessage'])
            ->orderByDesc('updated_at');

        if ($statusFilter === 'open') {
            $query->whereIn('status', ['open', 'in_progress', 'waiting_seller']);
        } elseif ($statusFilter === 'escalated') {
            $query->where('status', 'escalated');
        } elseif ($statusFilter === 'resolved') {
            $query->whereIn('status', ['resolved', 'closed']);
        }

        $tickets = $query->paginate(12);

        // Counts
        $totalCount = SupportTicket::where('seller_id', $sellerId)->count();
        $openCount = SupportTicket::where('seller_id', $sellerId)->whereIn('status', ['open', 'in_progress', 'waiting_seller'])->count();
        $escalatedCount = SupportTicket::where('seller_id', $sellerId)->where('status', 'escalated')->count();
        $resolvedCount = SupportTicket::where('seller_id', $sellerId)->whereIn('status', ['resolved', 'closed'])->count();

        return view('seller.support.index', compact(
            'tickets',
            'totalCount',
            'openCount',
            'escalatedCount',
            'resolvedCount',
            'statusFilter'
        ));
    }

    /**
     * Display inquiry conversation thread for seller.
     */
    public function showTicket(SupportTicket $ticket): View|RedirectResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();
        abort_unless($user && $user->role === 'seller', 403);
        abort_unless($ticket->seller_id === $user->id, 403, 'Unauthorized access to this inquiry.');

        $ticket->load([
            'user',
            'messages.user',
            'order.sellerOrders.items.product',
        ]);

        return view('seller.support.show', compact('ticket'));
    }

    /**
     * Post seller reply to customer ticket.
     */
    public function replyTicket(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();
        abort_unless($user && $user->role === 'seller', 403);
        abort_unless($ticket->seller_id === $user->id, 403, 'Unauthorized access to this inquiry.');

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        $attachmentPath = null;
        $attachmentType = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('support_attachments', 'public');
            $attachmentType = $file->getClientOriginalExtension();
        }

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'sender_type' => 'seller',
            'message' => $validated['message'],
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
        ]);

        $ticket->update([
            'status' => 'waiting_customer',
            'updated_at' => now(),
        ]);

        return redirect()->route('seller.support.tickets.show', $ticket)
            ->with('success', 'Your reply has been sent to the customer.');
    }

    /**
     * Update ticket status from seller side (e.g. In Progress, Resolved).
     */
    public function updateStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();
        abort_unless($user && $user->role === 'seller', 403);
        abort_unless($ticket->seller_id === $user->id, 403, 'Unauthorized access to this inquiry.');

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:open,in_progress,waiting_customer,resolved'],
        ]);

        $ticket->update([
            'status' => $validated['status'],
            'resolved_at' => $validated['status'] === 'resolved' ? now() : $ticket->resolved_at,
            'updated_at' => now(),
        ]);

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'sender_type' => 'system',
            'message' => "Merchant updated status to: " . ucfirst(str_replace('_', ' ', $validated['status'])),
        ]);

        return redirect()->route('seller.support.tickets.show', $ticket)
            ->with('success', 'Ticket status updated.');
    }

    /**
     * Show ticket creation form for seller.
     */
    public function createTicket(Request $request): View|RedirectResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();
        if (!$user || $user->role !== 'seller') {
            return redirect()->route('login');
        }

        $sellerId = $user->id;

        // Recent orders for linking
        $recentOrders = SellerOrder::where('seller_id', $sellerId)
            ->with(['order.user'])
            ->latest()
            ->take(15)
            ->get();

        $selectedOrderId = $request->query('order_id');
        $selectedOrder = $selectedOrderId ? SellerOrder::where('seller_id', $sellerId)->with('order')->find($selectedOrderId) : null;

        return view('seller.support.create', compact('recentOrders', 'selectedOrder'));
    }

    /**
     * Store newly created seller support ticket.
     */
    public function storeTicket(Request $request): RedirectResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();
        abort_unless($user && $user->role === 'seller', 403);

        $sellerId = $user->id;
        $profile = SellerProfile::where('user_id', $sellerId)->first();

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:escrow_payout,kyc_clearance,listing_error,account_issue,general'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'message' => ['required', 'string', 'max:3000'],
            'order_id' => ['nullable', 'exists:seller_orders,id'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        // Determine customer if order linked
        $customerId = null;
        $orderId = null;
        if (!empty($validated['order_id'])) {
            $sellerOrder = SellerOrder::where('seller_id', $sellerId)->with('order.user')->find($validated['order_id']);
            if ($sellerOrder) {
                $orderId = $sellerOrder->order_id;
                if ($sellerOrder->order) {
                    $customerId = $sellerOrder->order->user_id;
                }
            }
        }

        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => $customerId ?? $sellerId, // If no customer, assign to seller as initiator
            'seller_id' => $sellerId,
            'order_id' => $orderId,
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        $attachmentPath = null;
        $attachmentType = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('support_attachments', 'public');
            $attachmentType = $file->getClientOriginalExtension();
        }

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $sellerId,
            'sender_type' => 'seller',
            'message' => $validated['message'],
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
        ]);

        // Notify admin
        try {
            \App\Models\AdminFeed::record(
                title: "New Seller Assistance Ticket: #{$ticket->ticket_number}",
                description: "Seller " . (($profile?->shop_name) ?? 'Store') . " opened ticket '{$ticket->subject}' (" . ucfirst($ticket->priority) . " priority). Category: {$ticket->category}.",
                type: 'support',
                icon: 'support_agent',
                badgeText: strtoupper($ticket->priority),
                badgeColor: $ticket->priority === 'urgent' ? 'rose' : ($ticket->priority === 'high' ? 'amber' : 'blue'),
                actionUrl: route('admin.support.tickets.show', $ticket),
            );
        } catch (\Throwable $feedEx) {}

        return redirect()->route('seller.support.tickets.show', $ticket)
            ->with('success', "Ticket #{$ticket->ticket_number} created successfully. Admin team has been notified.");
    }

    /**
     * Escalate ticket to admin dispute arbitration.
     */
    public function escalateTicket(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();
        abort_unless($user && $user->role === 'seller', 403);
        abort_unless($ticket->seller_id === $user->id, 403, 'Unauthorized access to this inquiry.');

        $validated = $request->validate([
            'escalation_reason' => ['required', 'string', 'max:1000'],
        ]);

        $ticket->update([
            'status' => 'escalated',
            'is_escalated' => true,
            'escalated_at' => now(),
            'escalation_reason' => $validated['escalation_reason'],
            'updated_at' => now(),
        ]);

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'sender_type' => 'system',
            'message' => "🚨 TICKET ESCALATED: Seller escalated this issue for admin dispute review. Reason: {$validated['escalation_reason']}",
        ]);

        $profile = SellerProfile::where('user_id', $user->id)->first();

        try {
            \App\Models\AdminFeed::record(
                title: "Ticket Escalated: #{$ticket->ticket_number}",
                description: "Seller " . (($profile?->shop_name) ?? $user->name ?? 'Store') . " escalated ticket '{$ticket->subject}' for admin arbitration.",
                type: 'support',
                icon: 'gavel',
                badgeText: 'ESCALATED',
                badgeColor: 'rose',
                actionUrl: route('admin.support.tickets.show', $ticket),
            );
        } catch (\Throwable $feedEx) {}

        return redirect()->route('seller.support.tickets.show', $ticket)
            ->with('warning', 'Ticket has been escalated to admin dispute arbitration.');
    }
}
