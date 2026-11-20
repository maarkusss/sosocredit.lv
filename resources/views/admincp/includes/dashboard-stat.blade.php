<div class="flex items-center justify-between flex-1 p-4 mb-3 leading-none {{ $bg_color ?? 'bg-white' }} rounded">
    <div class="flex flex-col">
        @if (!empty($link))<a href="{{ $link }}" class="flex flex-col hover:opacity-80">@endif
            <span class="mb-2 text-2xl lg:text-3xl font-medium {{ $value_color ?? 'text-primary-normal' }}">{{ $value }}</span>
            <span class="font-normal text-normal {{ $title_color ?? 'text-gray-500' }}">
                @if (!empty($link)) <a href="{{ $link }}" class="{{ $title_color ?? '' }}">@endif
                {{ $title }}
            </span>
            @if (!empty($link))</a>@endif
    </div>
    <div class="h-full">
        @if (!empty($detail))
            <span class="text-xl font-medium {{ $detail_color }}">{{ $detail }}</span>
        @endif

        @if (!empty($details_array))
            <div class="h-full flex flex-col text-sm md:text-base text-right justify-start opacity-90">
                @foreach ($details_array as $detail_key => $detail_value)
                    <div class="{{ $detail_color ?? 'text-gray-400' }} {{ $detail_container_class ?? '' }}">
                        <span class="font-semibold">{{ $detail_key }}</span>
                        <span class="font-normal">{!! $detail_value !!}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
