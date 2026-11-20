@php
    $meta_title = $section->name . ' - ' . __('review.page_title');
    $meta_description = __('review.meta_description', ['count' => $totalCount]);
@endphp

@extends('layouts.public')

@push('structured_data')
    <script type="application/ld+json">
        {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "{{ $section->name }}",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ optional($google_stats)->overall_rating ?? 0 }}",
            "reviewCount": "{{ $totalCount }}",
            "bestRating": "5",
            "worstRating": "1"
        },
        "review": [
            @foreach($structuredReviews as $index => $review)
            {
            "@type": "Review",
            "author": {
                "@type": "Person",
                "name": "{{ explode(' ', $review->author_name)[0] }}"
            },
            "datePublished": "{{ $review->review_time->format('Y-m-d') }}",
            "reviewBody": "{{ str_replace('"', '\"', $review->text) }}",
            "reviewRating": {
                "@type": "Rating",
                "ratingValue": "{{ $review->rating }}",
                "bestRating": "5",
                "worstRating": "1"
            }
            }@if(!$loop->last),@endif
            @endforeach
        ]
        }
    </script>
@endpush

@section('content')
    <main class="flex flex-col max-w-screen-xl px-4 py-8 mx-auto text-[#2B2A25]">
        <header>
            <h1 class="content__heading">{{ $section->name }}</h1>
        </header>

        <div class="content__content">
            {{-- Google atsauksmju kopsavilkums --}}
            <section
                class="bg-white rounded-lg shadow-[0_4px_4px_0_rgba(0,0,0,0.07)] justify-between md:p-12 p-6 mb-8 flex flex-col md:flex-row gap-8"
                aria-label="@lang('review.summary_aria_label')">
                <a href="https://www.google.com/maps/place/Sosocredit.lv/@56.6653704,22.4921672,20.19z/data=!4m15!1m7!3m6!1s0x46ef9929cf7f25f7:0xcae769f72a6bbea7!2sSosocredit.lv!8m2!3d56.6653565!4d22.4922601!16s%2Fg%2F11z4sx4t_v!3m6!1s0x46ef9929cf7f25f7:0xcae769f72a6bbea7!8m2!3d56.6653565!4d22.4922601!15sCg1Tb3NvY3JlZGl0Lmx2kgEOZmluYW5jZV9icm9rZXLgAQA!16s%2Fg%2F11z4sx4t_v?entry=ttu&g_ep=EgoyMDI2MDQwOC4wIKXMDSoASAFQAw%3D%3D"
                    target="_blank" rel="nofollow" class="flex items-center gap-2 min-w-[240px]">
                    <div class="p-1 mt-1 bg-white rounded-full md:mt-0" aria-hidden="true">
                        <svg width="54" height="54" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Google logo">
                            <title>Google</title>
                            <path
                                d="M22.3585 10.0305H17.9514H12.2316V14.2012H17.8691C17.3617 15.5556 16.3983 16.6869 15.1656 17.4098C14.2371 17.955 13.155 18.2672 11.9999 18.2672C9.31263 18.2672 7.02039 16.5757 6.13005 14.1995C5.87284 13.5153 5.73269 12.7739 5.73269 11.9999C5.73269 11.2868 5.85198 10.6016 6.07098 9.96323C6.9167 7.50187 9.25183 5.73276 11.9999 5.73276C13.2169 5.73276 14.3529 6.07973 15.3139 6.68048L18.7294 3.88429C16.9035 2.36836 14.558 1.45715 11.9999 1.45715C7.92527 1.45715 4.39052 3.76847 2.63584 7.15148C1.88219 8.60311 1.45703 10.2517 1.45703 12C1.45703 13.8079 1.91177 15.5092 2.71352 16.9963C4.4942 20.2987 7.98494 22.5428 11.9999 22.5428C14.4983 22.5428 16.7934 21.674 18.6002 20.2217C20.4579 18.7283 21.7989 16.618 22.3122 14.2012C22.4634 13.491 22.5427 12.7548 22.5427 12C22.5427 11.3269 22.4796 10.6682 22.3585 10.0305Z"
                                fill="#FBBB00" />
                            <path
                                d="M18.5999 20.2211L18.6006 20.2217C16.7938 21.674 14.4987 22.5429 12.0002 22.5429C7.98529 22.5429 4.49455 20.2988 2.71387 16.9963L6.13045 14.1995C7.02079 16.5757 9.31298 18.2672 12.0003 18.2672C13.1554 18.2672 14.2375 17.955 15.166 17.4099L18.5999 20.2211Z"
                                fill="#28B446" />
                            <path
                                d="M22.3584 10.0305C22.4795 10.6682 22.5426 11.3269 22.5426 12C22.5426 12.7548 22.4633 13.4911 22.3121 14.2013C21.7989 16.618 20.4578 18.7283 18.6001 20.2217L18.5995 20.2211L15.1655 17.4098C16.3982 16.6869 17.3615 15.5556 17.869 14.2013H12.2314V10.0305H17.9513H22.3584Z"
                                fill="#518EF8" />
                            <path
                                d="M18.7292 3.88434L15.3138 6.68053C14.3528 6.07978 13.2169 5.73281 11.9998 5.73281C9.25168 5.73281 6.9166 7.50192 6.07088 9.96328L2.63635 7.15148H2.63574C4.39041 3.76847 7.92512 1.45715 11.9998 1.45715C14.5579 1.45715 16.9034 2.36836 18.7292 3.88434Z"
                                fill="#FC3F1D" />
                        </svg>
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-3xl font-bold"
                                itemprop="ratingValue">{{ optional($google_stats)->overall_rating ?? 0 }}</span>
                            <div class="flex gap-[2px]" role="img" aria-label="@lang('review.rating_aria', ['rating' => '5', 'max' => '5'])">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4" viewBox="0 0 14 13" fill="#F4D20F" aria-hidden="true">
                                        <path
                                            d="M6.80467 0.377511L9.27558 3.7471L13.2786 4.71182L10.8375 8.10305L11.157 12.2083L7.17743 10.9346L3.37184 12.5071L3.35343 8.32865L0.681939 5.19524L4.65016 3.88653L6.80467 0.377511Z" />
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        <p class="text-sm text-[#878579]">
                            @lang('review.based_on') <span itemprop="reviewCount">{{ $totalCount }}</span>
                            @lang('review.reviews_count')
                        </p>
                    </div>
                </a>

                {{-- Zvaigžņu sadalījums --}}
                <div class="flex-1 space-y-2 max-w-[450px]">
                    @for ($stars = 5; $stars >= 1; $stars--)
                        @php
                            $count = $ratingStats[$stars] ?? 0;
                            $percentage = $count ? round(($count / $totalCount) * 100) : 0;
                        @endphp
                        <div class="flex items-end gap-3">
                            <div class="w-full mb-1">
                                <div class="pb-1 text-sm">
                                    {{ $stars }} {{ trans_choice('review.stars', $stars) }}
                                </div>
                                <div class="flex-1 h-2 bg-[#E0E0E0] rounded" role="progressbar"
                                    aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"
                                    aria-label="@lang('review.star_distribution_aria', ['stars' => $stars, 'count' => $count, 'total' => $totalCount])">
                                    <div class="h-2 rounded bg-primary-normal" style="width: {{ $percentage }}%">
                                    </div>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-xs text-right text-white rounded-full bg-primary-normal">
                                {{ $count }}
                            </span>
                        </div>
                    @endfor
                </div>
            </section>

            {{-- Atsauksmju saraksts --}}
            <section aria-labelledby="reviews-heading">
                <div class="flex items-center justify-between mb-4">
                    <h2 id="reviews-heading" class="text-lg font-bold">@lang('review.customer_experience')</h2>

                    <form method="GET" aria-label="@lang('review.sort_reviews')">
                        <label for="sort-select" class="sr-only">@lang('review.sort_by')</label>
                        <select id="sort-select" name="sort" onchange="this.form.submit()"
                            class="border border-[#EBEBEB] rounded px-3 pr-10 py-2 text-sm md:min-w-[220px]">
                            <option value="newest" @selected(request('sort', 'newest') === 'newest')>
                                @lang('review.sort_newest')
                            </option>
                            <option value="oldest" @selected(request('sort') === 'oldest')>
                                @lang('review.sort_oldest')
                            </option>
                            <option value="rating_desc" @selected(request('sort') === 'rating_desc')>
                                @lang('review.sort_highest')
                            </option>
                            <option value="rating_asc" @selected(request('sort') === 'rating_asc')>
                                @lang('review.sort_lowest')
                            </option>
                        </select>
                    </form>
                </div>

                <div>
                    @foreach ($all_reviews as $index => $review)
                        <article class="bg-white shadow-[0_4px_4px_0_rgba(0,0,0,0.07)] rounded-lg p-4 mb-3" itemscope
                            itemtype="https://schema.org/Review">
                            <div class="flex flex-row items-start gap-4 md:items-center">
                                <div class="relative w-10 h-10 md:w-14 md:h-14 shrink-0">
                                    <img src="{{ $review->profile_photo_url }}" alt="{{ $review->author_name }}"
                                        class="object-cover w-full h-full rounded-full"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div
                                        class="absolute inset-0 hidden rounded-full bg-[#F3D10980] text-white flex items-center justify-center font-bold md:text-2xl text-xl">
                                        {{ strtoupper(mb_substr($review->author_name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="w-full">
                                    <div class="flex items-start justify-between mb-2 md:items-center">
                                        <div class="flex flex-col items-start gap-1 md:gap-2 md:items-center md:flex-row">
                                            <div class="flex gap-[2px]" itemprop="reviewRating" itemscope
                                                itemtype="https://schema.org/Rating" role="img"
                                                aria-label="@lang('review.rating_aria', ['rating' => $review->rating, 'max' => '5'])">
                                                <meta itemprop="ratingValue" content="{{ $review->rating }}">
                                                <meta itemprop="bestRating" content="5">
                                                <meta itemprop="worstRating" content="1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4"
                                                        fill="{{ $i <= $review->rating ? '#F4D20F' : '#EBEBEB' }}"
                                                        viewBox="0 0 14 13" aria-hidden="true">
                                                        <path
                                                            d="M6.80467 0.377511L9.27558 3.7471L13.2786 4.71182L10.8375 8.10305L11.157 12.2083L7.17743 10.9346L3.37184 12.5071L3.35343 8.32865L0.681939 5.19524L4.65016 3.88653L6.80467 0.377511Z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-sm font-bold" itemprop="author" itemscope
                                                itemtype="https://schema.org/Person">
                                                <span itemprop="name">{{ explode(' ', $review->author_name)[0] }}</span>
                                            </span>
                                        </div>

                                        <time class="text-xs text-[#7C838199]"
                                            datetime="{{ $review->review_time->format('Y-m-d') }}"
                                            itemprop="datePublished">
                                            {{ $review->review_time->format('d.m.Y') }}
                                        </time>
                                    </div>

                                    <p class="text-sm" itemprop="reviewBody">
                                        {!! nl2br(e($review->text)) !!}
                                    </p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <nav aria-label="@lang('review.pagination_aria')">
                {{ $all_reviews->links('includes.pagination') }}
            </nav>

            @if ($section->text)
                <article class="max-w-full mt-8 prose" id="article_section_text">
                    {!! $section->text !!}
                </article>
            @endif

            @include('includes.news.news-banner')
        </div>
    </main>
@endsection
