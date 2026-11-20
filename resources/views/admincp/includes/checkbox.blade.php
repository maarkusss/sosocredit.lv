<div class="checkbox-wrap">
    <input type="checkbox"
           class="checkbox-wrap__checkbox {{ $class ?? '' }}"
           id="{{ $id }}"
           name="{{ $name }}"
           value="{{ old($name, $value ?? '') }}"
           @if (!empty($checked))
               checked="checked"
           @endif
           @if (isset($x_model))
               x-model="{{ $x_model }}"
           @endif
    />
    @if (!empty($label))
        <label for="{{ $id }}" class="checkbox-wrap__label @if(!empty($class)) {{ $class }} @endif">
            {{ $label }} @if (!empty($required))
                *
            @endif
        </label>
    @endif
    @if (!empty($hint))
        <svg class="ml-2" width="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <title>{{ $hint }}</title>
            <path d="M12 23C5.925 23 1 18.075 1 12S5.925 1 12 1s11 4.925 11 11-4.925 11-11 11zm0-2c4.97 0 9-4.03 9-9s-4.03-9-9-9-9 4.03-9 9 4.03 9 9 9z"/>
            <circle cx="12" cy="16" r="1"/>
            <path d="M13 14h-2c0-1.702.625-2.43 2.053-3.144C13.875 10.445 14 10.298 14 9.5 14 8.556 13.277 8 12 8c-1.105 0-2 .895-2 2H8c0-2.209 1.791-4 4-4 2.284 0 4 1.321 4 3.5 0 1.702-.625 2.43-2.053 3.144C12.125 13.555 13 13.702 13 14z"/>
        </svg>
    @endif
</div>
