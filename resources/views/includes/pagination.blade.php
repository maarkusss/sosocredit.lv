@if ($paginator->hasPages())
    <nav class="flex justify-center mt-8">
        <ul class="inline-flex items-center gap-1 text-sm">

            {{-- Previous --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span
                        class="w-8 h-8 flex items-center justify-center rounded border border-[#B3B3AD] bg-[#B3B3AD] text-white">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.1602 7.41L10.5802 12L15.1602 16.59L13.7502 18L7.75016 12L13.7502 6L15.1602 7.41Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                        class="w-8 h-8 flex items-center justify-center rounded border border-[#B3B3AD] text-[#B3B3AD] hover:bg-[#B3B3AD]/10">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.1602 7.41L10.5802 12L15.1602 16.59L13.7502 18L7.75016 12L13.7502 6L15.1602 7.41Z"
                                fill="currentColor" />
                        </svg>
                    </a>
                @endif
            </li>

            {{-- Pages --}}
            @foreach ($elements as $element)
                {{-- Dots --}}
                @if (is_string($element))
                    <li>
                        <span class="w-8 h-8 flex items-center justify-center text-[#7C8381]">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                {{-- Page numbers --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span
                                    class="w-8 h-8 flex items-center justify-center rounded border border-[#F3D109] text-[#F3D109] font-semibold">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="w-8 h-8 flex items-center justify-center rounded border border-[#B3B3AD] hover:bg-[#B3B3AD]/10">
                                    {{ $page }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                        class="w-8 h-8 flex items-center justify-center rounded border border-[#B3B3AD] text-[#B3B3AD] hover:bg-[#B3B3AD]/10">
                        <svg class="rotate-180" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.1602 7.41L10.5802 12L15.1602 16.59L13.7502 18L7.75016 12L13.7502 6L15.1602 7.41Z"
                                fill="currentColor" />
                        </svg>
                    </a>
                @else
                    <span
                        class="w-8 h-8 flex items-center justify-center rounded border border-[#B3B3AD] bg-[#B3B3AD] text-white">
                        <svg class="rotate-180" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.1602 7.41L10.5802 12L15.1602 16.59L13.7502 18L7.75016 12L13.7502 6L15.1602 7.41Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                @endif
            </li>

        </ul>
    </nav>
@endif
