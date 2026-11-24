@extends('layouts.admin')

@section('title', 'Edit Support Specialist — ' . \App\Models\SiteSetting::getSiteName())

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Top Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-xs text-slate-500 font-mono">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-amber-600 transition-colors">Admin</a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('admin.support.index') }}" class="hover:text-amber-600 transition-colors">Support</a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('admin.support.agents.index') }}" class="hover:text-amber-600 transition-colors">Staff &amp; Agents</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-900 font-bold">Edit {{ $agent->name }}</span>
        </div>

        <a href="{{ route('admin.support.agents.index') }}" 
           class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors flex items-center gap-2 border border-slate-200 shadow-2xs self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Back to Staff Roster</span>
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 sm:p-6">
        <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200/80">
                STAFF PROFILE
            </span>
            <span class="text-xs text-slate-400 font-mono">•</span>
            <span class="text-xs text-slate-500 font-mono font-medium">DESK GOVERNANCE</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-[#0F172A] tracking-tight mt-1.5 flex items-center gap-2.5">
            <span class="material-symbols-outlined text-[#F5A623] text-3xl">manage_accounts</span>
            <span>Edit Support Specialist Profile</span>
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
            Updating <strong class="text-slate-800">{{ $agent->name }}</strong> • Staff ID: <code class="px-2 py-0.5 rounded bg-slate-100 text-amber-800 font-mono text-xs font-bold border border-slate-200">{{ $agent->staff_id ?? 'BZ-AGT-' . str_pad($agent->id, 3, '0', STR_PAD_LEFT) }}</code>
        </p>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
            <div class="font-bold text-rose-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-base">error</span>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 pl-2 text-rose-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Form Card -->
    <form action="{{ route('admin.support.agents.update', $agent) }}" method="POST" class="p-6 sm:p-8 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Full Name -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                    Specialist Full Name <span class="text-amber-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $agent->name) }}" 
                       required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
            </div>

            <!-- Email -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                    Official Login Email <span class="text-amber-500">*</span>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $agent->email) }}" 
                       required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
            </div>

            <!-- Phone -->
            <div class="space-y-1.5">
                <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                    Contact Phone Number
                </label>
                <input type="text" 
                       id="phone" 
                       name="phone" 
                       value="{{ old('phone', $agent->phone) }}" 
                       placeholder="e.g. +91 98765 43210" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
            </div>

            <!-- Staff ID -->
            <div class="space-y-1.5">
                <label for="staff_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                    Assigned Staff ID
                </label>
                <input type="text" 
                       id="staff_id" 
                       name="staff_id" 
                       value="{{ old('staff_id', $agent->staff_id) }}" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-amber-700 font-mono font-bold focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
            </div>

            <!-- Department Assignment -->
            <div class="space-y-1.5">
                <label for="department" class="block text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                    Support Unit / Department <span class="text-amber-500">*</span>
                </label>
                <input type="text" 
                       id="department" 
                       name="department" 
                       list="department_list"
                       value="{{ old('department', $agent->department) }}" 
                       required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                <datalist id="department_list">
                    @foreach($defaultDepartments as $d)
                        <option value="{{ $d }}">
                    @endforeach
                </datalist>
            </div>

            <!-- Status -->
            <div class="space-y-1.5">
                <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                    Account Status <span class="text-amber-500">*</span>
                </label>
                <select id="status" 
                        name="status" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                    <option value="active" {{ old('status', $agent->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $agent->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ old('status', $agent->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
        </div>

        <!-- Optional Password Reset Section -->
        <div class="p-5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-4">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider font-mono">
                <span class="material-symbols-outlined text-amber-500 text-base">key</span>
                <span>Reset Password (Leave blank to keep current)</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs text-slate-600 font-medium">
                        New Password
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Enter new password (optional)" 
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                </div>

                <div class="space-y-1.5">
                    <label for="password_confirmation" class="block text-xs text-slate-600 font-medium">
                        Confirm New Password
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           placeholder="Confirm new password" 
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                </div>
            </div>
        </div>

        <!-- Bio / Specialist Notes -->
        <div class="space-y-1.5">
            <label for="bio" class="block text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                Internal Specialist Notes / Skill Scope
            </label>
            <textarea id="bio" 
                      name="bio" 
                      rows="2" 
                      placeholder="e.g. Handles APMC wholesale auction disputes, courier claims, and return verification."
                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">{{ old('bio', $agent->bio) }}</textarea>
        </div>

        <!-- Submit & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-5 border-t border-slate-100">
            <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-slate-400 text-sm">schedule</span>
                <span>Created {{ $agent->created_at ? $agent->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.support.agents.index') }}" 
                   class="px-5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold shadow-2xs transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-[#F5A623] hover:bg-amber-400 text-slate-950 font-bold text-xs transition-all shadow-xs hover:shadow flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Save Specialist Updates</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
