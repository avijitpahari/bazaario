<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\AdminFeed;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AgentTicketController extends Controller
{
    /**
     * Display a listing of support tickets filtered by assignment & status.
     */
    public function index(Request $request): View
    {
        $agent = Auth::guard('user')->user() ?? Auth::user();
        $view = $request->query('view', 'my_tickets'); // my_tickets | unassigned | escalated | all | resolved
        $search = trim((string) $request->query('search', ''));
        $category = $request->query('category');
        $priority = $request->query('priority');

        $query = SupportTicket::with(['user', 'seller.sellerProfile', 'order', 'assignedAgent', 'latestMessage']);

        // View Filtering
        switch ($view) {
            case 'my_tickets':
                $query->where('assigned_agent_id', $agent->id)
                    ->whereNotIn('status', ['resolved', 'closed']);
                break;
            case 'unassigned':
                $query->whereNull('assigned_agent_id')
                    ->whereNotIn('status', ['resolved', 'closed']);
                break;
            case 'escalated':
                $query->where('is_escalated', true)
                    ->whereNotIn('status', ['resolved', 'closed']);
                break;
            case 'resolved':
                $query->whereIn('status', ['resolved', 'closed']);
                break;
            case 'all':
            default:
                // All active tickets
                $query->whereNotIn('status', ['resolved', 'closed']);
                break;
        }

        // Search Filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('seller', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        if (!empty($category)) {
            $query->where('category', $category);
        }

        if (!empty($priority)) {
            $query->where('priority', $priority);
        }

        // Priority sort
        $query->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->latest('updated_at');

        $tickets = $query->paginate(15)->withQueryString();

        // Tab badge counters
        $myTicketsCount = SupportTicket::where('assigned_agent_id', $agent->id)->whereNotIn('status', ['resolved', 'closed'])->count();
        $unassignedCount = SupportTicket::whereNull('assigned_agent_id')->whereNotIn('status', ['resolved', 'closed'])->count();
        $escalatedCount = SupportTicket::where('is_escalated', true)->whereNotIn('status', ['resolved', 'closed'])->count();
        $allActiveCount = SupportTicket::whereNotIn('status', ['resolved', 'closed'])->count();
        $resolvedCount = SupportTicket::whereIn('status', ['resolved', 'closed'])->count();

        $ticketCategories = [
            'order_issue' => 'Order Issue',
            'return_refund' => 'Return & Refund',
            'delivery_delay' => 'Delivery Delay',
            'auction' => 'Auction & Bidding',
            'billing' => 'Payment & Billing',
            'escrow_payout' => 'Escrow Payout',
            'kyc_clearance' => 'KYC Clearance',
            'listing_error' => 'Listing Error',
            'account_issue' => 'Account Issue',
            'general' => 'General Inquiry',
        ];

        return view('agent.tickets.index', compact(
            'tickets',
            'agent',
            'view',
            'search',
            'category',
            'priority',
            'myTicketsCount',
            'unassignedCount',
            'escalatedCount',
            'allActiveCount',
            'resolvedCount',
            'ticketCategories'
        ));
    }

    /**
     * Show detailed support ticket conversation and workstation.
     */
    public function show(SupportTicket $ticket): View
    {
        $agent = Auth::guard('user')->user() ?? Auth::user();

        $ticket->load([
            'user',
            'seller.sellerProfile',
            'order.items.product',
            'assignedAgent',
            'messages.user',
        ]);

        // Canned responses for fast resolution
        $cannedReplies = [
            [
                'title' => 'Courier Telemetry Tracking',
                'text' => "Hello, I have initiated a high-priority telemetry dispatch check with our BlueDart courier desk. The driver is scheduled to arrive at your destination window today.",
            ],
            [
                'title' => 'Request Package Photos',
                'text' => "Thank you for reaching out. To proceed with your claim under Bazaario Buyer Protection, please reply with clear photos of the shipping label and the received goods.",
            ],
            [
                'title' => 'Escrow Protection Hold',
                'text' => "We have placed a temporary escrow release hold on this order. Merchant payout will remain safeguarded in our APMC vault until this matter is resolved.",
            ],
            [
                'title' => 'Replacement / Return Pickup Arranged',
                'text' => "A doorstep return pickup has been booked for your delivery address. Our courier associate will collect the package within 24-48 business hours at zero cost to you.",
            ],
        ];

        return view('agent.tickets.show', compact('ticket', 'agent', 'cannedReplies'));
    }

    /**
     * Claim ticket and assign to current support agent.
     */
    public function claim(SupportTicket $ticket): RedirectResponse
    {
        $agent = Auth::guard('user')->user() ?? Auth::user();

        $ticket->update([
            'assigned_agent_id' => $agent->id,
            'status' => in_array($ticket->status, ['open', 'waiting_seller']) ? 'in_progress' : $ticket->status,
        ]);

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $agent->id,
            'sender_type' => 'system',
            'message' => "Support Specialist {$agent->name} has been assigned to investigate and resolve this ticket.",
        ]);

        return back()->with('success', "Ticket #{$ticket->ticket_number} assigned to you.");
    }

    /**
     * Post a response to the support ticket.
     */
    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $agent = Auth::guard('user')->user() ?? Auth::user();

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'status' => ['nullable', 'string', 'in:in_progress,waiting_customer,waiting_seller,resolved,closed'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('support_attachments', 'public');
        }

        // Create message
        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $agent->id,
            'sender_type' => 'agent',
            'message' => trim($validated['message']),
            'attachment_path' => $attachmentPath,
        ]);

        // Auto assign if unassigned
        $updates = [];
        if (empty($ticket->assigned_agent_id)) {
            $updates['assigned_agent_id'] = $agent->id;
        }

        $newStatus = $validated['status'] ?? 'waiting_customer';
        $updates['status'] = $newStatus;

        if (in_array($newStatus, ['resolved', 'closed'])) {
            $updates['resolved_at'] = now();
            if ($newStatus === 'closed') {
                $updates['closed_at'] = now();
            }
        }

        $ticket->update($updates);

        return back()->with('success', 'Your response has been sent to the ticket participants.');
    }

    /**
     * Update ticket status and priority from workstation.
     */
    public function updateStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $agent = Auth::guard('user')->user() ?? Auth::user();

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:open,in_progress,waiting_customer,waiting_seller,resolved,closed'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'assigned_agent_id' => ['nullable', 'exists:users,id'],
        ]);

        $updates = [
            'status' => $validated['status'],
            'priority' => $validated['priority'],
        ];

        if (array_key_exists('assigned_agent_id', $validated)) {
            $updates['assigned_agent_id'] = $validated['assigned_agent_id'];
        }

        if (in_array($validated['status'], ['resolved', 'closed'])) {
            $updates['resolved_at'] = now();
            if ($validated['status'] === 'closed') {
                $updates['closed_at'] = now();
            }
        }

        $ticket->update($updates);

        return back()->with('success', 'Ticket properties updated successfully.');
    }

    /**
     * Escalate ticket to Admin Arbitration.
     */
    public function escalate(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $agent = Auth::guard('user')->user() ?? Auth::user();

        $validated = $request->validate([
            'escalation_reason' => ['required', 'string', 'max:1000'],
        ]);

        $ticket->update([
            'is_escalated' => true,
            'escalated_at' => now(),
            'status' => 'escalated',
            'escalation_reason' => $validated['escalation_reason'],
        ]);

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $agent->id,
            'sender_type' => 'system',
            'message' => "Ticket escalated by Support Specialist {$agent->name} for Executive Admin Dispute Arbitration. Reason: {$validated['escalation_reason']}",
        ]);

        // Record in Admin Feed
        try {
            AdminFeed::record(
                type: 'support_ticket_escalated',
                title: "Ticket #{$ticket->ticket_number} Escalated by Specialist {$agent->name}",
                subtitle: $ticket->subject,
                badge: 'Dispute Arbitration',
                url: route('admin.support.tickets.show', $ticket->id),
                severity: 'warning'
            );
        } catch (\Throwable $e) {}

        return back()->with('warning', 'Ticket has been escalated to Executive Admin Dispute Arbitration.');
    }
}
