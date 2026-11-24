<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\SupportTicket;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentDashboardController extends Controller
{
    /**
     * Display the Support Specialist Workstation Dashboard.
     */
    public function index(): View
    {
        $agent = Auth::guard('user')->user() ?? Auth::user();

        // ── KPIs for this Support Agent ──────────────────────────────────────────
        $myOpenTicketsCount = SupportTicket::where('assigned_agent_id', $agent->id)
            ->whereIn('status', ['open', 'in_progress', 'waiting_customer', 'waiting_seller', 'escalated'])
            ->count();

        $unassignedQueueCount = SupportTicket::whereNull('assigned_agent_id')
            ->whereIn('status', ['open', 'in_progress', 'waiting_customer', 'waiting_seller', 'escalated'])
            ->count();

        $myEscalatedCount = SupportTicket::where('assigned_agent_id', $agent->id)
            ->where('is_escalated', true)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();

        $myResolvedCount = SupportTicket::where('assigned_agent_id', $agent->id)
            ->whereIn('status', ['resolved', 'closed'])
            ->count();

        $liveActiveChatsCount = ChatConversation::whereIn('status', ['open', 'pending'])
            ->count();

        // ── My Active Assigned Tickets (top 5 priority) ──────────────────────────
        $myTickets = SupportTicket::where('assigned_agent_id', $agent->id)
            ->whereIn('status', ['open', 'in_progress', 'waiting_customer', 'waiting_seller', 'escalated'])
            ->with(['user', 'seller.sellerProfile', 'order', 'latestMessage'])
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->latest('updated_at')
            ->take(6)
            ->get();

        // ── Fresh Unassigned Queue (top 5 to claim) ──────────────────────────────
        $unassignedTickets = SupportTicket::whereNull('assigned_agent_id')
            ->whereIn('status', ['open', 'in_progress', 'waiting_customer', 'waiting_seller', 'escalated'])
            ->with(['user', 'seller.sellerProfile', 'order'])
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->latest()
            ->take(6)
            ->get();

        // ── Live Chat Conversations needing attention ────────────────────────────
        $liveChats = ChatConversation::whereIn('status', ['open', 'pending'])
            ->with(['messages' => fn($q) => $q->latest('id')->take(1)])
            ->latest('last_message_at')
            ->take(5)
            ->get();

        return view('agent.dashboard', compact(
            'agent',
            'myOpenTicketsCount',
            'unassignedQueueCount',
            'myEscalatedCount',
            'myResolvedCount',
            'liveActiveChatsCount',
            'myTickets',
            'unassignedTickets',
            'liveChats'
        ));
    }

    /**
     * Toggle Agent Online / Offline Availability.
     */
    public function toggleStatus(Request $request): RedirectResponse|JsonResponse
    {
        $agent = Auth::guard('user')->user() ?? Auth::user();
        $agent->is_online = !$agent->is_online;
        $agent->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_online' => $agent->is_online,
                'status_label' => $agent->is_online ? 'Available Online' : 'Away / Offline',
            ]);
        }

        return back()->with('success', 'Your workstation status has been set to ' . ($agent->is_online ? 'Available' : 'Away / Offline') . '.');
    }
}
