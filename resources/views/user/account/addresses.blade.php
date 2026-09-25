@extends('layouts.user')

@section('title', 'My Addresses — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6" x-data="{ showAddForm: false, editId: null }">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-amber-action/10 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold">Delivery Network</span>
                <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">My Addresses</h1>
                <p class="font-body-small text-body-small text-on-surface-variant mt-1">{{ $addresses->count() }} saved delivery address(es)</p>
            </div>
            <button @click="showAddForm = !showAddForm" type="button"
                class="self-start md:self-auto px-5 py-2.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm hover:opacity-95 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add_location_alt</span>
                Add New Address
            </button>
        </div>
    </div>

    {{-- Add Address Form (collapsible) --}}
    <div x-show="showAddForm" x-transition class="rounded-2xl bg-card-white/90 backdrop-blur-xl border border-white/90 p-6 md:p-8 shadow-sm">
        <div class="flex items-center gap-2 mb-5 pb-4 border-b border-surface-container">
            <span class="material-symbols-outlined text-amber-action text-[22px]">add_location_alt</span>
            <h2 class="font-title-card text-title-card font-bold text-slate-authority">Add New Address</h2>
        </div>

        @if(isset($errors) && $errors->any())
        <div class="mb-4 p-4 rounded-xl bg-error/10 border border-error/20 text-error font-body-small text-body-small">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('user.addresses.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf

            <div class="flex flex-col gap-1.5">
                <label class="font-body-small text-body-small font-semibold text-slate-authority">Address Type</label>
                <select name="type" class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority focus:outline-none focus:border-amber-action transition-colors">
                    <option value="home">Home</option>
                    <option value="work">Work</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-body-small text-body-small font-semibold text-slate-authority">Full Name *</label>
                <input type="text" name="full_name" value="{{ old('full_name', $user->name) }}" placeholder="Receiver's full name"
                    class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority focus:outline-none focus:border-amber-action transition-colors" required>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-body-small text-body-small font-semibold text-slate-authority">Phone *</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+91 9876543210"
                    class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority focus:outline-none focus:border-amber-action transition-colors" required>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-body-small text-body-small font-semibold text-slate-authority">Address Line 1 *</label>
                <input type="text" name="address_line_1" value="{{ old('address_line_1') }}" placeholder="House no., Street, Area"
                    class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority focus:outline-none focus:border-amber-action transition-colors" required>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-body-small text-body-small font-semibold text-slate-authority">Address Line 2</label>
                <input type="text" name="address_line_2" value="{{ old('address_line_2') }}" placeholder="Apartment, Suite (optional)"
                    class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority focus:outline-none focus:border-amber-action transition-colors">
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-body-small text-body-small font-semibold text-slate-authority">Landmark</label>
                <input type="text" name="landmark" value="{{ old('landmark') }}" placeholder="Near landmark (optional)"
                    class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority focus:outline-none focus:border-amber-action transition-colors">
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-body-small text-body-small font-semibold text-slate-authority">City *</label>
                <input type="text" name="city" value="{{ old('city') }}" placeholder="City"
                    class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority focus:outline-none focus:border-amber-action transition-colors" required>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-body-small text-body-small font-semibold text-slate-authority">State *</label>
                <input type="text" name="state" value="{{ old('state') }}" placeholder="State"
                    class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority focus:outline-none focus:border-amber-action transition-colors" required>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-body-small text-body-small font-semibold text-slate-authority">Postal Code *</label>
                <input type="text" name="postal_code" value="{{ old('postal_code') }}" placeholder="Postal code"
                    class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority focus:outline-none focus:border-amber-action transition-colors" required>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="font-body-small text-body-small font-semibold text-slate-authority">Country *</label>
                <input type="text" name="country" value="{{ old('country', 'India') }}" placeholder="Country"
                    class="bg-surface-container-low border border-slate-authority/20 rounded-xl px-4 py-3 text-slate-authority focus:outline-none focus:border-amber-action transition-colors" required>
            </div>

            <div class="md:col-span-2 flex items-center gap-3">
                <input type="checkbox" name="is_default" value="1" id="is_default" class="rounded border-slate-authority/30 text-amber-action focus:ring-amber-action" {{ $addresses->isEmpty() ? 'checked' : '' }}>
                <label for="is_default" class="font-body-small text-body-small text-slate-authority">Set as default address</label>
            </div>

            <div class="md:col-span-2 flex items-center gap-3 pt-2 border-t border-surface-container">
                <button type="button" @click="showAddForm = false" class="px-5 py-2.5 rounded-xl border border-slate-authority/20 text-slate-authority font-button-text text-body-small hover:bg-surface-container transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm hover:opacity-95">
                    Save Address
                </button>
            </div>
        </form>
    </div>

    {{-- Addresses Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($addresses as $address)
        <div class="relative p-5 rounded-2xl {{ $address->is_default ? 'bg-card-white border border-amber-action/40 shadow-md' : 'bg-surface-container-lowest/90 border border-white/60 shadow-sm hover:shadow-md' }} flex flex-col gap-4 transition-all">

            {{-- Header --}}
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl {{ $address->is_default ? 'bg-amber-action/20' : 'bg-surface-container' }} flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px] {{ $address->is_default ? 'text-amber-action' : 'text-on-surface-variant' }}">{{ $address->type_icon }}</span>
                    </div>
                    <div>
                        <p class="font-body-regular text-body-regular font-bold text-slate-authority">{{ $address->type_label }}</p>
                        <p class="font-label-micro text-label-micro text-on-surface-variant">{{ $address->full_name }}</p>
                    </div>
                </div>
                @if($address->is_default)
                <span class="px-2.5 py-1 rounded-full bg-amber-action text-slate-authority font-label-micro text-label-micro font-bold">DEFAULT</span>
                @endif
            </div>

            {{-- Address --}}
            <p class="font-body-small text-body-small text-on-surface-variant leading-relaxed">
                {{ $address->address_line_1 }}{{ $address->address_line_2 ? ', ' . $address->address_line_2 : '' }}<br>
                {{ $address->city }}, {{ $address->state }} — <span class="font-mono text-slate-authority font-semibold">{{ $address->postal_code }}</span><br>
                {{ $address->country }}
            </p>

            <p class="font-label-micro text-label-micro text-on-surface-variant">📞 {{ $address->phone }}</p>

            {{-- Actions --}}
            <div class="flex items-center gap-2 pt-2 border-t border-slate-authority/5">
                @if(!$address->is_default)
                <form action="{{ route('user.addresses.default', $address->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-3 py-1.5 rounded-lg bg-surface-container text-slate-authority font-label-micro text-label-micro font-semibold hover:bg-surface-container-high transition-colors">
                        Set Default
                    </button>
                </form>
                @endif
                <form action="{{ route('user.addresses.destroy', $address->id) }}" method="POST" onsubmit="return confirm('Remove this address?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 rounded-lg text-error hover:bg-error/10 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="md:col-span-2 lg:col-span-3 p-12 rounded-2xl bg-surface-container-lowest/90 text-center flex flex-col items-center gap-4">
            <span class="material-symbols-outlined text-[64px] text-on-surface-variant/40">add_location_alt</span>
            <div>
                <h3 class="font-title-card text-title-card font-semibold text-slate-authority">No addresses saved</h3>
                <p class="font-body-small text-body-small text-on-surface-variant mt-1">Add your first delivery address.</p>
            </div>
            <button @click="showAddForm = true" type="button" class="px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm">
                Add Address
            </button>
        </div>
        @endforelse
    </div>
</div>
@endsection