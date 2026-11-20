@extends('layouts.public')

@section('content')
    @if(config('app.loan_modal_enabled'))
        @include('includes.modals.loan-modal')
    @endif
    <main class="flex flex-col max-w-screen-xl p-6 md:px-4 md:py-8 mx-auto text-[#081B1A]">
        <div class="flex items-center mb-4 text-sm">
            <!-- Date -->
            @include('includes.news.news-date')

            <!-- Category tag -->
            @include('includes.news.news-category-tag', ['class' => 'ml-4'])
        </div>

        <!-- Title -->
        <h1 class="text-[32px] leading-[120%] font-bold mb-3">
            {{ $post['title'] }}
        </h1>

        <!-- Tiny description -->
        <div class="mb-6 text-base">
            {{ $post['tiny_description'] }}
        </div>

        <!-- Main image -->
        <img src="{{ asset($post['image']) }}" alt="{{ $post['image_alt_text'] }}"
            class="object-cover w-full mb-8 h-80 rounded-2xl"
            onerror="this.onerror=null; this.src='{{ asset('images/no_image.png') }}';" />

        <!-- Text -->
        <div class="prose" style="max-width: 100%;">
            {!! $post['text'] !!}
        </div>

        <!-- Banner -->
        @include('includes.news.news-banner')

        <!-- Other news title -->
        <h2 class="text-[22px] font-bold leading-[130%] mb-3">
            @lang('news.other_news')
        </h2>

        <!-- Other news list -->
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($latest_news as $latest_post)
                @include('includes.news.news-list-item', ['post' => $latest_post, 'compact_view' => true])
            @endforeach
        </div>
    </main>
@endsection
