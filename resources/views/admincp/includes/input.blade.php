@php
$input_type = 'text';
if (!empty($type)) {
    $input_type = $type;
}
@endphp

<div class="input-wrap {{ $container_class ?? '' }}">
    @if (!empty($label))
        <label for="{{ $id }}" class="input-wrap__label {{ $label_class ?? '' }}">
            {{ $label }} @if (!empty($required))* @endif
        </label>
    @endif
    <input
        type="{{ $input_type }}"
        class="input-wrap__input {{ $class ?? '' }}"
        id="{{ $id }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder ?? '' }}"
        autocomplete="off"
        @if (!empty($value_if_auth))
            @auth
                value="{{ $value_if_auth }}"
            @endauth
        @else
            value="{{ old($name, $value ?? '') }}"
        @endif
        @if (!empty($required))
            required
        @endif
        @if (!empty($alpine_required))
            :required="{{ $alpine_required }}"
        @endif
        @if (!empty($readonly))
            readonly
        @endif
    />
</div>
