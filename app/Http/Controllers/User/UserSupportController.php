<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserSupportController extends Controller
{
    /**
     * Customer Support Hub: Overview of active tickets, returns, and FAQs.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $statusFilter = $request->query('status');

        $ticketQuery = SupportTicket::where('user_id', $userId)
            ->with(['seller.sellerProfile', 'order', 'latestMessage'])
            ->orderByDesc('updated_at');

        if ($statusFilter === 'open') {
            $ticketQuery->whereIn('status', ['open', 'in_progress', 'waiting_seller', 'escalated']);
        } elseif ($statusFilter === 'resolved') {
            $ticketQuery->whereIn('status', ['resolved', 'closed']);
        }

        $tickets = $ticketQuery->paginate(8);

        // Counts
        $totalTickets = SupportTicket::where('user_id', $userId)->count();
        $openTickets = SupportTicket::where('user_id', $userId)->whereIn('status', ['open', 'in_progress', 'waiting_seller', 'escalated'])->count();
        $resolvedTickets = SupportTicket::where('user_id', $userId)->whereIn('status', ['resolved', 'closed'])->count();

        // Recent Return Requests
        $returns = OrderReturn::where('user_id', $userId)
            ->with(['order', 'orderItem'])
            ->latest('requested_at')
            ->take(5)
            ->get();

        // Categorized Quick FAQs for Instant Auto-Reply
        $faqs = \App\Services\SupportFaqService::all();

        return view('user.support.index', compact(
            'tickets',
            'returns',
            'totalTickets',
            'openTickets',
            'resolvedTickets',
            'statusFilter',
            'faqs'
        ));
    }

    /**
     * Show ticket creation form.
     */
    public function createTicket(Request $request): View
    {
        $userId = Auth::id();
        $recentOrders = Order::where('user_id', $userId)
            ->with(['sellerOrders.seller.sellerProfile'])
            ->latest()
            ->take(15)
            ->get();

        $selectedOrderId = $request->query('order_id');
        $selectedOrder = $selectedOrderId ? Order::where('user_id', $userId)->find($selectedOrderId) : null;
        $faqs = \App\Services\SupportFaqService::all();

        return view('user.support.create', compact('recentOrders', 'selectedOrder', 'faqs'));
    }

    /**
     * Store newly created support ticket and initial message.
     */
    public function storeTicket(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:order_issue,return_refund,delivery_delay,auction,billing,general'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'message' => ['required', 'string', 'max:3000'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        $sellerId = null;
        if (!empty($validated['order_id'])) {
            $order = Order::where('user_id', $userId)->with('sellerOrders')->find($validated['order_id']);
            if ($order && $order->sellerOrders->isNotEmpty()) {
                $sellerId = $order->sellerOrders->first()->seller_id;
            }
        }

        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => $userId,
            'seller_id' => $sellerId,
            'order_id' => $validated['order_id'] ?? null,
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
            'user_id' => $userId,
            'sender_type' => 'customer',
            'message' => $validated['message'],
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
        ]);

        try {
            \App\Models\AdminFeed::record(
                title: "New Support Ticket: #{$ticket->ticket_number}",
                description: "Customer " . (Auth::user()?->name ?? 'User') . " opened ticket '{$ticket->subject}' (" . ucfirst($ticket->priority) . " priority).",
                type: 'support',
                icon: 'support_agent',
                badgeText: strtoupper($ticket->priority),
                badgeColor: $ticket->priority === 'urgent' ? 'rose' : ($ticket->priority === 'high' ? 'amber' : 'blue'),
                actionUrl: route('admin.support.index')
            );
        } catch (\Throwable $feedEx) {}

        return redirect()->route('user.support.tickets.show', $ticket)
            ->with('success', "Ticket #{$ticket->ticket_number} created successfully. Our team and vendor have been notified.");
    }

    /**
     * Display ticket conversation thread.
     */
    public function showTicket(SupportTicket $ticket): View
    {
        abort_unless($ticket->user_id === Auth::id(), 403, 'Unauthorized access to this support ticket.');

        $ticket->load([
            'messages.user',
            'seller.sellerProfile',
            'order.sellerOrders.items.product',
        ]);

        return view('user.support.show', compact('ticket'));
    }

    /**
     * Post a reply to an existing support ticket.
     */
    public function replyTicket(Request $request, SupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->user_id === Auth::id(), 403, 'Unauthorized access to this support ticket.');

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf'],
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
            'user_id' => Auth::id(),
            'sender_type' => 'customer',
            'message' => $validated['message'],
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
        ]);

        // If ticket was waiting on customer, resolved, or closed, reopen and set to in_progress
        $isReopening = in_array($ticket->status, ['resolved', 'closed']);
        $newStatus = ($isReopening || $ticket->status === 'waiting_customer') ? 'in_progress' : $ticket->status;
        $ticket->update([
            'status' => $newStatus,
            'resolved_at' => $isReopening ? null : $ticket->resolved_at,
            'closed_at' => $isReopening ? null : $ticket->closed_at,
            'updated_at' => now(),
        ]);

        return redirect()->route('user.support.tickets.show', $ticket)
            ->with('success', 'Your reply has been sent.');
    }

    /**
     * Escalate ticket to formal dispute arbitration.
     */
    public function escalateTicket(Request $request, SupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->user_id === Auth::id(), 403, 'Unauthorized access to this support ticket.');

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
            'user_id' => Auth::id(),
            'sender_type' => 'system',
            'message' => "🚨 TICKET ESCALATED: Customer escalated this issue for formal dispute review. Reason: {$validated['escalation_reason']}",
        ]);

        return redirect()->route('user.support.tickets.show', $ticket)
            ->with('warning', 'Ticket has been escalated to senior dispute arbitration.');
    }

    /**
     * Mark ticket as closed/resolved by customer.
     */
    public function closeTicket(SupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->user_id === Auth::id(), 403, 'Unauthorized access to this support ticket.');

        $ticket->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'closed_at' => now(),
            'updated_at' => now(),
        ]);

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'sender_type' => 'system',
            'message' => 'Ticket marked as resolved/closed by customer.',
        ]);

        return redirect()->route('user.support.tickets.show', $ticket)
            ->with('success', 'Ticket marked as resolved.');
    }

    /**
     * Reopen a closed or resolved ticket.
     */
    public function reopenTicket(SupportTicket $ticket): RedirectResponse
    {
        abort_unless($ticket->user_id === Auth::id(), 403, 'Unauthorized access to this support ticket.');

        $ticket->update([
            'status' => 'open',
            'resolved_at' => null,
            'closed_at' => null,
            'updated_at' => now(),
        ]);

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'sender_type' => 'system',
            'message' => 'Ticket was reopened by customer for further assistance.',
        ]);

        return redirect()->route('user.support.tickets.show', $ticket)
            ->with('info', 'Ticket has been reopened. Our customer care team and seller have been notified.');
    }
}
