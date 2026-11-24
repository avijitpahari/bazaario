<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SupportTicket;
use App\Models\ChatConversation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminSupportAgentController extends Controller
{
    /**
     * Display a listing of support agents and their performance metrics.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $department = $request->query('department');
        $status = $request->query('status');

        $query = User::whereIn('role', ['support_agent', 'support'])
            ->withCount([
                'assignedTickets',
                'assignedTickets as active_tickets_count' => function ($q) {
                    $q->whereIn('status', ['open', 'in_progress', 'waiting_customer', 'waiting_seller', 'escalated']);
                },
                'assignedTickets as resolved_tickets_count' => function ($q) {
                    $q->whereIn('status', ['resolved', 'closed']);
                },
                'assignedConversations',
            ]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if (!empty($department)) {
            $query->where('department', $department);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $agents = $query->latest('id')->paginate(15)->withQueryString();

        // Summary KPI Metrics
        $totalAgents = User::whereIn('role', ['support_agent', 'support'])->count();
        $onlineAgents = User::whereIn('role', ['support_agent', 'support'])->where('is_online', true)->count();
        $activeStaffTickets = SupportTicket::whereNotNull('assigned_agent_id')
            ->whereIn('status', ['open', 'in_progress', 'waiting_customer', 'waiting_seller', 'escalated'])
            ->count();
        $totalStaffResolved = SupportTicket::whereNotNull('assigned_agent_id')
            ->whereIn('status', ['resolved', 'closed'])
            ->count();

        $departments = User::whereIn('role', ['support_agent', 'support'])
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        return view('admin.support.agents.index', compact(
            'agents',
            'totalAgents',
            'onlineAgents',
            'activeStaffTickets',
            'totalStaffResolved',
            'departments',
            'search',
            'department',
            'status'
        ));
    }

    /**
     * Show form to create a new support agent.
     */
    public function create(): View
    {
        // Suggest next Staff ID
        $count = User::whereIn('role', ['support_agent', 'support'])->count() + 1;
        $suggestedStaffId = 'BZ-AGT-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);

        $defaultDepartments = [
            'Order Inquiries & Disputes',
            'Returns & Escrow Refunds',
            'Seller KYC & Storefronts',
            'Wholesale Auctions Desk',
            'General Concierge & Triage',
        ];

        return view('admin.support.agents.create', compact('suggestedStaffId', 'defaultDepartments'));
    }

    /**
     * Store a newly created support agent in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'department' => ['nullable', 'string', 'max:100'],
            'staff_id' => ['nullable', 'string', 'max:50', 'unique:users,staff_id'],
            'status' => ['required', 'in:active,inactive'],
            'bio' => ['nullable', 'string', 'max:500'],
        ]);

        $staffId = !empty($validated['staff_id'])
            ? trim($validated['staff_id'])
            : 'BZ-AGT-' . strtoupper(Str::random(5));

        $agent = User::create([
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'support_agent',
            'staff_id' => $staffId,
            'department' => $validated['department'] ?? 'General Concierge',
            'status' => $validated['status'],
            'bio' => $validated['bio'] ?? null,
            'email_verified_at' => now(),
            'preferred_language' => 'en',
            'is_online' => false,
        ]);

        return redirect()->route('admin.support.agents.index')
            ->with('success', "Support agent \"{$agent->name}\" (#{$agent->staff_id}) created successfully. They can now log in via the main login page.");
    }

    /**
     * Show form to edit an existing support agent.
     */
    public function edit(User $agent): View
    {
        abort_unless($agent->isSupportAgent(), 404, 'User is not a support agent.');

        $defaultDepartments = [
            'Order Inquiries & Disputes',
            'Returns & Escrow Refunds',
            'Seller KYC & Storefronts',
            'Wholesale Auctions Desk',
            'General Concierge & Triage',
        ];

        return view('admin.support.agents.edit', compact('agent', 'defaultDepartments'));
    }

    /**
     * Update existing support agent details.
     */
    public function update(Request $request, User $agent): RedirectResponse
    {
        abort_unless($agent->isSupportAgent(), 404, 'User is not a support agent.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($agent->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'department' => ['nullable', 'string', 'max:100'],
            'staff_id' => ['nullable', 'string', 'max:50', Rule::unique('users', 'staff_id')->ignore($agent->id)],
            'status' => ['required', 'in:active,inactive,suspended'],
            'bio' => ['nullable', 'string', 'max:500'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'] ?? null,
            'department' => $validated['department'] ?? $agent->department,
            'staff_id' => !empty($validated['staff_id']) ? trim($validated['staff_id']) : $agent->staff_id,
            'status' => $validated['status'],
            'bio' => $validated['bio'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $agent->update($updateData);

        return redirect()->route('admin.support.agents.index')
            ->with('success', "Support agent \"{$agent->name}\" updated successfully.");
    }

    /**
     * Toggle agent active/inactive status.
     */
    public function toggleStatus(User $agent): RedirectResponse
    {
        abort_unless($agent->isSupportAgent(), 404, 'User is not a support agent.');

        $newStatus = $agent->status === 'active' ? 'inactive' : 'active';
        $agent->update([
            'status' => $newStatus,
            'is_online' => $newStatus === 'active' ? $agent->is_online : false,
        ]);

        return back()->with('success', "Agent {$agent->name} status set to " . ucfirst($newStatus) . ".");
    }

    /**
     * Remove support agent and unassign their pending tickets safely.
     */
    public function destroy(User $agent): RedirectResponse
    {
        abort_unless($agent->isSupportAgent(), 404, 'User is not a support agent.');

        // Unassign active tickets so customer queries are not stranded
        SupportTicket::where('assigned_agent_id', $agent->id)->update([
            'assigned_agent_id' => null,
        ]);

        ChatConversation::where('assigned_agent_id', $agent->id)->update([
            'assigned_agent_id' => null,
        ]);

        $name = $agent->name;
        $agent->delete();

        return redirect()->route('admin.support.agents.index')
            ->with('success', "Support agent \"{$name}\" has been deleted. Any assigned tickets were moved back to the unassigned pool.");
    }
}
