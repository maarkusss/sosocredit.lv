<div class="checkbox-wrap">
    <input type="radio"
           class="checkbox-wrap__checkbox {{ $class ?? '' }}"
           id="{{ $id }}"
           name="{{ $name }}"
           value="{{ old($name, $value ?? '') }}"
           @if (!empty($checked))
               checked="checked"
           @endif
    />
    @if (!empty($label))
        <label for="{{ $id }}" class="checkbox-wrap__label @if(!empty($class)) {{ $class }} @endif">
            {{ $label }} @if (!empty($required))
                *
            @endif
        </label>
    @endif
</div>
