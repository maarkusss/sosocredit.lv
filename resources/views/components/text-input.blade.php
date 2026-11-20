@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'p-3 shadow-sm border border-gray-300 rounded ring_primary_dark border_primary_dark']) !!}>
