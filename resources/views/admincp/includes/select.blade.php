<div class="input-wrap {{ $container_class ?? '' }}">
    @if (!empty($label))
        <label for="{{ $id }}" class="input-wrap__label">
            {{ $label }} @if (!empty($required))* @endif
        </label>
    @endif
    <select
        class="w-full px-3 py-2 mt-2 text-gray-700 bg-white border-gray-300 rounded sm:mt-0 focus:border-primary-dark focus:ring-primary-dark {{ $class ?? '' }}"
        name="{{ $name }}"
        id="{{ $id }}">
        @if (!empty($default_option))
            <option value="">{{ $default_option }}</option>
        @endif
        @if (!empty($options))
            @foreach ($options as $option_key => $option_name)
                <option value="{{ $option_key }}" @if (!empty($value) && $value == $option_key) selected @endif>{{ $option_name }}</option>
            @endforeach
        @endif
    </select>
</div>
