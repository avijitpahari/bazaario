<!-- Customer Info Card -->
<div>
    <h3 class="font-mono text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Customer Info</h3>
    <div class="flex flex-col items-center text-center p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
        <div class="w-16 h-16 rounded-full bg-slate-200 text-slate-800 font-bold text-lg flex items-center justify-center ring-2 ring-blue-500/30 mb-2">
            {{ strtoupper(substr($activeConversation->customer_display_name, 0, 2)) }}
        </div>
        <h4 class="font-heading font-bold text-sm text-slate-900">{{ $activeConversation->customer_display_name }}</h4>
        <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $activeConversation->customer_email ?: 'No email' }}</p>

        <div class="w-full grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-slate-200 text-left text-xs">
            <div>
                <span class="text-[10px] text-slate-400 block font-mono">Location</span>
                <span class="font-medium text-slate-700 truncate block">West Bengal, IN</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 block font-mono">Account</span>
                <span class="font-medium text-slate-700 block">{{ $activeConversation->user ? 'Verified User' : 'Guest Visitor' }}</span>
            </div>
        </div>

        @if($activeConversation->user)
            <a href="{{ route('admin.customers.show', $activeConversation->user->id) }}" class="mt-4 w-full py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-heading text-xs font-semibold rounded-xl text-center transition-colors">
                View Profile
            </a>
        @endif
    </div>
</div>

<!-- Conversation Details -->
<div class="space-y-3">
    <h3 class="font-mono text-[11px] font-bold text-slate-400 uppercase tracking-wider">Conversation Details</h3>
    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2.5 text-xs">
        <div class="flex items-center justify-between">
            <span class="text-slate-500">Status</span>
            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $activeConversation->status === 'open' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                {{ $activeConversation->status }}
            </span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-slate-500">Source</span>
            <span class="font-medium text-slate-800">Website Chat Widget</span>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-slate-500">Assigned To</span>
            <form action="{{ route('admin.support.status', $activeConversation->id) }}" method="POST">
                @csrf
                <select name="assigned_agent_id" onchange="this.form.submit()" class="text-xs bg-white border border-slate-200 rounded px-1.5 py-0.5 text-slate-800">
                    <option value="">Unassigned</option>
                    @foreach($agents as $ag)
                        <option value="{{ $ag->id }}" {{ $activeConversation->assigned_agent_id === $ag->id ? 'selected' : '' }}>
                            {{ $ag->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-slate-500">Started</span>
            <span class="font-mono text-slate-600 text-[11px]">{{ $activeConversation->created_at ? $activeConversation->created_at->format('M d, h:i A') : 'Today' }}</span>
        </div>
    </div>
</div>

<!-- Conversation Tags -->
<div>
    <h3 class="font-mono text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Tags</h3>
    <div class="flex flex-wrap gap-1.5 mb-2">
        @foreach($activeConversation->tags ?? ['Product', 'Website'] as $tag)
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium border border-blue-100">
                <span>{{ $tag }}</span>
                <form action="{{ route('admin.support.status', $activeConversation->id) }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="remove_tag" value="{{ $tag }}">
                    <button type="submit" class="hover:text-rose-600 ml-0.5 text-[12px]">×</button>
                </form>
            </span>
        @endforeach
    </div>

    <!-- Add Tag Form -->
    <form action="{{ route('admin.support.status', $activeConversation->id) }}" method="POST" class="flex gap-1.5">
        @csrf
        <input type="text" name="tag" placeholder="+ Add Tag" class="flex-1 text-xs px-2.5 py-1.5 bg-slate-50 rounded-lg border border-slate-200 focus:bg-white focus:outline-none">
        <button type="submit" class="px-2.5 py-1.5 bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg hover:bg-slate-300">Add</button>
    </form>
</div>

<!-- Active Online Agents -->
<div>
    <div class="flex items-center justify-between mb-2">
        <h3 class="font-mono text-[11px] font-bold text-slate-400 uppercase tracking-wider">Online Agents</h3>
        <span class="text-[10px] font-mono text-emerald-600 font-bold">{{ count($agents) }} Online</span>
    </div>
    <div class="space-y-2">
        @foreach($agents as $ag)
            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 font-bold text-[10px] flex items-center justify-center">
                        {{ strtoupper(substr($ag->name, 0, 1)) }}
                    </div>
                    <span class="font-medium text-slate-800">{{ $ag->name }}</span>
                </div>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
        @endforeach
    </div>
</div>