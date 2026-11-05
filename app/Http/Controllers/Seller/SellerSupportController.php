<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
}
