@php
    /** @var \App\Models\Section $section */
    $count = !empty($section->json['block1_options'][3]) ? 4 : 3;
@endphp

<div class="grid grid-cols-3 gap-6 py-4 md:gap-2 md:px-0 md:justify-items-center">
    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <img src="{{ asset('images/target.svg') }}" alt="{{ __('alts.target') }}">
        </div>
        <div class="relative px-1 pt-1 md:px-2 md:min-w-92">
            <h4 class="mx-auto text-sm font-bold leading-tight text-center text-white md:text-base md:leading-normal">
                {{ $section->json['block1_options'][0] ?? '' }}
            </h4>
        </div>
    </div>
    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <img src="{{ asset('images/graph.svg') }}" alt="{{ __('alts.graph') }}">
        </div>
        <div class="relative px-1 pt-1 md:px-2 md:min-w-92">
            <h4 class="mx-auto text-sm font-bold leading-tight text-center text-white md:text-base md:leading-normal">
                {{ $section->json['block1_options'][1] ?? '' }}
            </h4>
        </div>
    </div>
    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <img src="{{ asset('images/shopping-list.svg') }}" alt="{{ __('alts.shopWping_list') }}">
        </div>
        <div class="relative px-1 pt-1 md:px-2 md:min-w-92">
            <h4 class="mx-auto text-sm font-bold leading-tight text-center text-white md:text-base md:leading-normal">
                {{ $section->json['block1_options'][2] ?? '' }}
            </h4>
        </div>
    </div>
    @if (!empty($section->json['block1_options'][3]))
        <div class="flex flex-col items-center justify-start flex-shrink-0 w-full">
            <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="#3faf4f"
                    viewBox="0 0 256 256">
                    <path
                        d="M165.66,194.34a8,8,0,0,1,0,11.32l-32,32a8,8,0,0,1-11.32,0l-32-32a8,8,0,0,1,11.32-11.32L120,212.69V43.31L101.66,61.66A8,8,0,0,1,90.34,50.34l32-32a8,8,0,0,1,11.32,0l32,32a8,8,0,0,1-11.32,11.32L136,43.31V212.69l18.34-18.35A8,8,0,0,1,165.66,194.34Z">
                    </path>
                </svg>
            </div>
            <div class="relative px-1 pt-1 md:px-2 md:min-w-92">
                <h4
                    class="mx-auto text-sm font-bold leading-tight text-center text-white md:text-base md:leading-normal">
                    {{ $section->json['block1_options'][3] ?? '' }}
                </h4>
            </div>
        </div>
    @endif
</div>
