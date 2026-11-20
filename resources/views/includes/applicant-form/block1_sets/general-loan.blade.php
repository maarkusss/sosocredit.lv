@php
    /** @var \App\Models\Section $section */
    $count = !empty($section->json['block1_options'][3]) ? 4 : 3;
@endphp

<div class="grid grid-cols-2 gap-8 py-4 md:px-0 md:justify-items-center">
    <div class="flex flex-col items-center justify-start flex-shrink-0 sm:w-1/{{ $count }}">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <img src="{{ asset('images/target.svg') }}" alt="{{ __('alts.target') }}" width="40" height="40">
        </div>
        <div class="relative px-1 pt-1 lg:px-4 md:min-w-max">
            <h4 class="mx-auto text-sm font-bold leading-tight text-center text-white md:text-base md:leading-normal">
                {{ $section->json['block1_options'][0] ?? '' }}
            </h4>
        </div>
    </div>
    <div class="flex flex-col items-center justify-start flex-shrink-0 sm:w-1/{{ $count }}">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <img src="{{ asset('images/discount.svg') }}" alt="{{ __('alts.discount') }}" width="40" height="40">
        </div>
        <div class="relative px-1 pt-1 lg:px-4 md:min-w-max">
            <h4 class="mx-auto text-sm font-bold leading-tight text-center text-white md:text-base md:leading-normal">
                {{ $section->json['block1_options'][1] ?? '' }}
            </h4>
        </div>
    </div>
    <div class="flex flex-col items-center justify-start flex-shrink-0 sm:w-1/{{ $count }}">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <img src="{{ asset('images/application.svg') }}" alt="{{ __('alts.application') }}" width="40" height="40">
        </div>
        <div class="relative px-1 pt-1 lg:px-4 md:min-w-max">
            <h4 class="mx-auto text-sm font-bold leading-tight text-center text-white md:text-base md:leading-normal">
                {{ $section->json['block1_options'][2] ?? '' }}
            </h4>
        </div>
    </div>
    @if (!empty($section->json['block1_options'][3]))
        <div class="flex flex-col items-center justify-start flex-shrink-0 sm:w-1/{{ $count }}">
            <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
                <img src="{{ asset('images/approved.svg') }}" alt="{{ __('alts.approved') }}" width="40" height="40">
            </div>
            <div class="relative px-1 pt-1 lg:px-4 md:min-w-max">
                <h4
                    class="mx-auto text-sm font-bold leading-tight text-center text-white md:text-base md:leading-normal">
                    {{ $section->json['block1_options'][3] ?? '' }}
                </h4>
            </div>
        </div>
    @endif
</div>
