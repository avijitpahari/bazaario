@props([
    'action' => '',
    'method' => 'GET',
    'search' => ['name' => 'search', 'placeholder' => 'Search...', 'value' => ''],
    'filters' => [], // [['name' => '', 'label' => '', 'options' => []]]
    'hidden' => [], // [['name' => '', 'value' => '']]
    'class' => '',
])

<form method="{{ $method }}" action="{{ $action }}" class="space-y-2 {{ $class }}">
    @foreach($hidden as $h)
        <input type="hidden" name="{{ $h['name'] }}" value="{{ $h['value'] }}">
    @endforeach

    <!-- Search -->
    <div class="relative">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">search</span>
        <input type="text"
               name="{{ $search['name'] }}"
               value="{{ $search['value'] }}"
               placeholder="{{ $search['placeholder'] }}"
               class="w-full bg-slate-50 text-slate-800 text-xs pl-9 pr-3 py-2 rounded-xl border border-slate-200 focus:bg-white focus:border-blue-400 focus:outline-none transition-colors">
    </div>

    <!-- Filters -->
    @foreach($filters as $filter)
        @php
            $options = $filter['options'] ?? [];
            $selected = request($filter['name']);
        @endphp
        <select name="{{ $filter['name'] }}" class="w-full bg-slate-50 text-slate-800 text-xs px-3 py-2 rounded-xl border border-slate-200 focus:bg-white focus:border-blue-400 focus:outline-none transition-colors">
            <option value="">{{ $filter['label'] }}</option>
            @foreach($options as $value => $label)
                <option value="{{ $value }}" {{ $selected === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    @endforeach
</form>