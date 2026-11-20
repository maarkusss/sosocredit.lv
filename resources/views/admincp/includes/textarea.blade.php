@php
    /** @var string $name */
    if (!empty($value_if_auth)) {
        $textarea_value = $value_if_auth;
    } else {
        $textarea_value = old($name, $value ?? '');
    }
@endphp

<div class="input-wrap {{ $container_class ?? '' }}">
    <label for="{{ $id }}" class="input-wrap__label">
        {{ $label }} @if (!empty($required))* @endif
    </label>
    <textarea
        class="input-wrap__textarea {{ $class ?? '' }}"
        id="{{ $id }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder ?? '' }}"
        cols="{{ $cols ?? '' }}"
        rows="{{ $rows ?? '' }}">{{ $textarea_value }}</textarea>
</div>
