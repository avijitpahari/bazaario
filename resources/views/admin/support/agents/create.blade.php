@extends('layouts.admin')

@section('title', 'Add Support Specialist — ' . \App\Models\SiteSetting::getSiteName())

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
            <span class="text-slate-900 font-bold">Add Specialist</span>
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
                PROVISIONING
            </span>
            <span class="text-xs text-slate-400 font-mono">•</span>
            <span class="text-xs text-slate-500 font-mono font-medium">NEW SPECIALIST DESK</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-[#0F172A] tracking-tight mt-1.5 flex items-center gap-2.5">
            <span class="material-symbols-outlined text-[#F5A623] text-3xl">person_add</span>
            <span>Provision New Support Agent</span>
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
            Support agents log in via the primary user login page and are granted access exclusively to the Support Specialist Workstation desk.
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

    <!-- Provision Form Card -->
    <form action="{{ route('admin.support.agents.store') }}" method="POST" class="p-6 sm:p-8 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Full Name -->
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                    Specialist Full Name <span class="text-amber-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required
                       placeholder="e.g. Ramesh Sharma" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
            </div>

            <!-- Email (Sign-in Username) -->
            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                    Official Login Email <span class="text-amber-500">*</span>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required
                       placeholder="e.g. agent.ramesh@bazaario.com" 
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
                       value="{{ old('phone') }}" 
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
                       value="{{ old('staff_id', $suggestedStaffId) }}" 
                       placeholder="e.g. BZ-AGT-001" 
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
                       value="{{ old('department', 'Order Inquiries & Disputes') }}" 
                       required
                       placeholder="Select or enter custom department" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                <datalist id="department_list">
                    @foreach($defaultDepartments as $d)
                        <option value="{{ $d }}">
                    @endforeach
                </datalist>
            </div>

            <!-- Initial Account Status -->
            <div class="space-y-1.5">
                <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                    Account Initial Status <span class="text-amber-500">*</span>
                </label>
                <select id="status" 
                        name="status" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Can login immediately)</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive (Access temporarily disabled)</option>
                </select>
            </div>

            <!-- Password -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                    Initial Password <span class="text-amber-500">*</span>
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       placeholder="Min. 8 characters" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
            </div>

            <!-- Confirm Password -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                    Confirm Password <span class="text-amber-500">*</span>
                </label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required
                       placeholder="Re-type password" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
            </div>
        </div>

        <!-- Bio / Specialist Notes -->
        <div class="space-y-1.5 pt-2">
            <label for="bio" class="block text-xs font-bold text-slate-700 uppercase tracking-wider font-mono">
                Internal Specialist Notes / Skill Scope
            </label>
            <textarea id="bio" 
                      name="bio" 
                      rows="2" 
                      placeholder="e.g. Handles APMC wholesale auction disputes, courier claims, and return verification." 
                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">{{ old('bio') }}</textarea>
        </div>

        <!-- Submit & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-5 border-t border-slate-100">
            <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-emerald-600 text-sm">lock</span>
                <span>Agent can log in securely from standard <code class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-mono text-[11px]">/login</code> page.</span>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.support.agents.index') }}" 
                   class="px-5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold shadow-2xs transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-[#F5A623] hover:bg-amber-400 text-slate-950 font-bold text-xs transition-all shadow-xs hover:shadow flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                    <span>Provision Agent Account</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
