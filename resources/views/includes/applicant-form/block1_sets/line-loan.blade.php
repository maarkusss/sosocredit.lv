@php
    /** @var \App\Models\Section $section */
    $count = !empty($section->json['block1_options'][3]) ? 4 : 3;
@endphp

<div class="grid grid-cols-2 gap-8 py-4 md:px-0 md:justify-items-center">
    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <img src="{{ asset('images/cc.svg') }}" alt="{{ __('alts.cc') }}">
        </div>
        <div class="relative px-1 pt-1 lg:px-4 md:min-w-60">
            <h4 class="mx-auto text-sm font-bold leading-tight text-center text-white md:text-base md:leading-normal">
                {{ $section->json['block1_options'][0] ?? '' }}
            </h4>
        </div>
    </div>
    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <img src="{{ asset('images/pie-chart.svg') }}" alt="{{ __('alts.pie_chart') }}">
        </div>
        <div class="relative px-1 pt-1 lg:px-4 md:min-w-60">
            <h4 class="mx-auto text-sm font-bold leading-tight text-center text-white md:text-base md:leading-normal">
                {{ $section->json['block1_options'][1] ?? '' }}
            </h4>
        </div>
    </div>
    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <img src="{{ asset('images/refresh.svg') }}" alt="{{ __('alts.refresh') }}">
        </div>
        <div class="relative px-1 pt-1 lg:px-4 md:min-w-60">
            <h4 class="mx-auto text-sm font-bold leading-tight text-center text-white md:text-base md:leading-normal">
                {{ $section->json['block1_options'][2] ?? '' }}
            </h4>
        </div>
    </div>
    @if (!empty($section->json['block1_options'][3]))
        <div class="flex flex-col items-center justify-start flex-shrink-0 w-full">
            <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
                <img src="{{ asset('images/coins.svg') }}" alt="{{ __('alts.coins') }}">
            </div>
            <div class="relative px-1 pt-1 lg:px-4 md:min-w-60">
                <h4
                    class="mx-auto text-sm font-bold leading-tight text-center text-white md:text-base md:leading-normal">
                    {{ $section->json['block1_options'][3] ?? '' }}
                </h4>
            </div>
        </div>
    @endif
</div>
