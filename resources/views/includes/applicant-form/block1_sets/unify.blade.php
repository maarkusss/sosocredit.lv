@php
    /** @var \App\Models\Section $section */
    $count = !empty($section->json['block1_options'][3]) ? 4 : 3;
@endphp

<div class="grid grid-cols-3 gap-6 md:gap-2 py-4 md:px-0 md:justify-items-center">
    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" fill="#297935" viewBox="0 0 256 256">
                <g transform="translate(128, 128) scale(0.8) translate(-128, -128)">
                    <path
                        d="M244,136v64a12,12,0,0,1-12,12H168a12,12,0,0,1,0-24h35l-67-67-31.51,31.52a12,12,0,0,1-17,0l-72-72a12,12,0,0,1,17-17L96,127l31.51-31.52a12,12,0,0,1,17,0L220,171V136a12,12,0,0,1,24,0Z">
                    </path>
                </g>
            </svg>
        </div>
        <div class="relative px-1 md:px-2 pt-1 md:min-w-92">
            <h4 class="text-white text-sm md:text-base font-bold leading-tight md:leading-normal text-center mx-auto">
                {{ $section->json['block1_options'][0] ?? '' }}
            </h4>
        </div>
    </div>
    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <img src="{{ asset('images/discount.svg') }}" alt="{{ __('alts.discount') }}" width="40" height="40">
        </div>
        <div class="relative px-1 md:px-2 pt-1 md:min-w-92">
            <h4 class="text-white text-sm md:text-base font-bold leading-tight md:leading-normal text-center mx-auto">
                {{ $section->json['block1_options'][1] ?? '' }}
            </h4>
        </div>
    </div>
    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.404 29.5V15.0299L16.0846 17.0584V13.7882L19.404 11.7965H22.4284V29.5H19.404Z"
                    fill="#297935" />
                <circle cx="20" cy="20" r="17.5" stroke="#297935" stroke-width="3" />
            </svg>
        </div>
        <div class="relative px-1 md:px-2 pt-1 md:min-w-92">
            <h4 class="text-white text-sm md:text-base font-bold leading-tight md:leading-normal text-center mx-auto">
                {{ $section->json['block1_options'][2] ?? '' }}
            </h4>
        </div>
    </div>
    @if (!empty($section->json['block1_options'][3]))
        <div class="flex flex-col items-center justify-start flex-shrink-0 w-full sm:mt-0 sm:w-1/{{ $count }}">
            <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
                <img src="{{ asset('images/discount.svg') }}" alt="{{ __('alts.discount') }}" width="40" height="40">
            </div>
            <div class="relative px-1 lg:px-4 pt-1 md:min-w-max">
                <h4
                    class="text-white text-sm md:text-base font-bold leading-tight md:leading-normal text-center mx-auto">
                    {{ $section->json['block1_options'][3] ?? '' }}
                </h4>
            </div>
        </div>
    @endif
</div>
