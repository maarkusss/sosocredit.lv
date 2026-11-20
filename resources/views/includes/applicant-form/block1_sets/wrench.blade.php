@php
    /** @var \App\Models\Section $section */
    $count = !empty($section->json['block1_options'][3]) ? 4 : 3;
@endphp

<div class="grid grid-cols-3 gap-6 md:gap-2 py-4 md:px-0 md:justify-items-center">
    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M35.4841 36.9098H24.4395C23.6454 36.9098 22.9958 36.2601 22.9958 35.466V28.6083C22.9958 26.8758 21.6242 25.5042 19.8917 25.5042C18.1592 25.5042 16.7877 26.8758 16.7877 28.6083V35.466C16.7877 36.2601 16.138 36.9098 15.344 36.9098H4.44374C3.64968 36.9098 3 36.2601 3 35.466V16.9862C3 15.6868 3.64968 14.4597 4.6603 13.7378L19.1699 3.2707C19.6752 2.90977 20.3248 2.90977 20.8302 3.2707L35.3397 13.7378C36.3503 14.4597 37 15.6868 37 16.9862V35.5382C36.9278 36.2601 36.2781 36.9098 35.4841 36.9098ZM25.8832 34.0945H34.1125V16.9862C34.1125 16.6253 33.896 16.2643 33.6072 16.0478L19.9639 6.15817L6.32059 15.9756C6.03185 16.1921 5.81529 16.5531 5.81529 16.914V34.0223H14.0446V28.6083C14.0446 25.3599 16.7155 22.689 19.9639 22.689C23.2123 22.689 25.8832 25.3599 25.8832 28.6083V34.0945Z"
                    fill="#297935" />
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
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_219_9182)">
                    <path
                        d="M39.83 19.4596L36.1933 23.0972L32.5549 19.4596M36.6382 22.1878C36.7042 21.5907 36.7381 20.9838 36.7381 20.369C36.7381 11.3287 29.4095 4 20.369 4C11.3287 4 4 11.3287 4 20.369C4 29.4095 11.3287 36.7381 20.369 36.7381C25.5113 36.7381 30.0995 34.3669 33.1005 30.6584M20.369 11.2751V20.369L25.8254 24.0066"
                        stroke="#297935" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                </g>
                <defs>
                    <clipPath id="clip0_219_9182">
                        <rect width="40" height="40" fill="white" />
                    </clipPath>
                </defs>
            </svg>
        </div>
        <div class="relative px-1 md:px-2 pt-1 md:min-w-92">
            <h4 class="text-white text-sm md:text-base font-bold leading-tight md:leading-normal text-center mx-auto">
                {{ $section->json['block1_options'][1] ?? '' }}
            </h4>
        </div>
    </div>
    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full">
        <div class="flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
            <img src="{{ asset('images/discount.svg') }}" alt="{{ __('alts.discount') }}" width="40" height="40">
        </div>
        <div class="relative px-1 md:px-2 pt-1 md:min-w-92">
            <h4 class="text-white text-sm md:text-base font-bold leading-tight md:leading-normal text-center mx-auto">
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
            <div class="relative px-1 md:px-2 pt-1 md:min-w-92">
                <h4
                    class="text-white text-sm md:text-base font-bold leading-tight md:leading-normal text-center mx-auto">
                    {{ $section->json['block1_options'][3] ?? '' }}
                </h4>
            </div>
        </div>
    @endif
</div>
