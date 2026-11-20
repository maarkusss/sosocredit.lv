@php
    use App\Models\Section;
    $review_route = Section::where('template', Section::TEMPLATE_REVIEWS)
        ->where('lang', app()->getLocale())
        ->pluck('route_name')
        ->first();

    $avatarColors = [
        'bg-blue-100 text-blue-600',
        'bg-emerald-100 text-emerald-600',
        'bg-violet-100 text-violet-600',
        'bg-amber-100 text-amber-600',
        'bg-rose-100 text-rose-600',
        'bg-cyan-100 text-cyan-600',
    ];

    $reviewCount = $reviews->take(20)->count();
    $fewReviews = $reviewCount <= 4;
@endphp

<section class="w-full overflow-hidden bg-white">

    <div class="flex items-center justify-center px-4 mb-6 md:px-8">
        <div class="section__content__heading-wrapper">
            <h2 class="section__content__heading">
                @lang('google.what_customers_say_about_us')
            </h2>
        </div>
    </div>

    <div class="swiper reviews-swiper !px-4 md:!px-8 !py-5">
        <div class="swiper-wrapper {{ $fewReviews ? 'md:!justify-center' : '' }}">
            @foreach ($reviews->take(20) as $index => $review)
                @php
                    $firstName = explode(' ', trim($review->author_name))[0];
                    $initial = mb_strtoupper(mb_substr($firstName, 0, 1));
                    $colorClass = $avatarColors[$index % count($avatarColors)];
                    $rating = (int) ($review->rating ?? 5);
                    $short = mb_strlen($review->text) > 120;
                @endphp
                <div class="swiper-slide !h-auto">
                    <div
                        class="flex flex-col h-full p-4 bg-white border border-gray-100 rounded-lg shadow-[0px_4px_4px_0px_rgba(0,0,0,0.10)] transition-shadow duration-200">

                        {{-- Header --}}
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 shrink-0">
                                @if (!empty($review->profile_photo_url))
                                    <img src="{{ $review->profile_photo_url }}" alt="{{ $firstName }}"
                                        class="object-cover w-8 h-8 rounded-full">
                                @else
                                    <div
                                        class="flex items-center justify-center w-8 h-8 text-xs font-semibold rounded-full {{ $colorClass }}">
                                        {{ $initial }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold truncate text-stone-800">{{ $firstName }}</p>
                                @if (!empty($review->review_time))
                                    <p class="text-[11px] text-stone-400">{{ $review->review_time->format('d.m.Y') }}
                                    </p>
                                @endif
                            </div>
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" aria-label="Google review">
                                <path
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                    fill="#4285F4" />
                                <path
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                    fill="#34A853" />
                                <path
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"
                                    fill="#FBBC05" />
                                <path
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                    fill="#EA4335" />
                            </svg>
                        </div>

                        <div class="flex items-center gap-x-[2px] mb-2" aria-label="{{ $rating }} out of 5 stars" role="img">
                            @foreach (range(1, 5) as $star)
                                <svg class="w-3.5 h-3.5" viewBox="0 0 14 13" fill="none" aria-hidden="true">
                                    <path
                                        d="M6.80467 0.377511C6.91812 0.182727 7.19952 0.182727 7.31297 0.377512L9.27558 3.7471C9.31714 3.81845 9.38678 3.86905 9.46749 3.88653L13.2786 4.71182C13.4989 4.75952 13.5859 5.02715 13.4357 5.19524L10.8375 8.10305C10.7825 8.16463 10.7559 8.24649 10.7642 8.32865L11.157 12.2083C11.1797 12.4326 10.9521 12.598 10.7458 12.5071L7.17743 10.9346C7.10186 10.9013 7.01578 10.9013 6.94022 10.9346L3.37184 12.5071C3.16557 12.598 2.93791 12.4326 2.96062 12.2083L3.35343 8.32865C3.36175 8.24649 3.33515 8.16463 3.28013 8.10305L0.681939 5.19524C0.531747 5.02715 0.618704 4.75952 0.839013 4.71182L4.65016 3.88653C4.73087 3.86905 4.80051 3.81845 4.84206 3.7471L6.80467 0.377511Z"
                                        fill="{{ $star <= $rating ? '#F4D20F' : 'none' }}"
                                        stroke="{{ $star <= $rating ? 'none' : '#D6D3D1' }}" />
                                </svg>
                            @endforeach
                        </div>

                        <p class="text-xs leading-relaxed text-stone-600 review-text">
                            {!! nl2br(e($review->text)) !!}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="px-4 mt-4 md:px-8 md:hidden swiper-pagination"></div>
    </div>

    @if (!empty($review_route))
        <div class="flex items-center justify-center px-4 mb-6 md:px-8">
            <a href="{{ route('section', ['name' => $review_route]) }}"
                class="h-11 w-full max-w-md md:w-44 px-6 py-2.5 mt-4 bg-[#3FAF4F] transition-all duration-200 cursor-pointer hover:bg-green-700 focus:outline-none focus:shadow-outline border border-green-600 rounded inline-flex justify-center items-center gap-1">
                <div class="justify-start text-base font-normal leading-tight text-white md:font-bold">@lang('main.read_more')
                </div>
            </a>
        </div>
    @endif

</section>
