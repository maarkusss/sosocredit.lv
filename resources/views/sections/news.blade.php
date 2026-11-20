@extends('layouts.public')

@section('content')
    @if(config('app.loan_modal_enabled'))
        @include('includes.modals.loan-modal')
    @endif
    <main class="flex flex-col max-w-screen-xl p-6 md:px-4 md:py-8 mx-auto text-[#081B1A]">
        <h1 class="mb-1 text-xl font-bold md:text-3xl">
            {{ $section->name }}
        </h1>
        <div class="text-sm md:text-base mb-4 md:mb-6 leading-[130%] font-medium">
            @lang('news.news_message')
        </div>

        @if (!empty($news['mains_news_items']))
            <div class="grid items-stretch grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3 md:gap-5">
                <!-- Wide left side news item -->
                <div class="hidden col-span-2 lg:block">
                    @foreach ($news['mains_news_items'] as $post)
                        @include('includes.news.news-list-item-large')
                    @endforeach
                </div>
                <!-- Default width left side news item -->
                <div class="block lg:hidden">
                    @foreach ($news['mains_news_items'] as $post)
                        @include('includes.news.news-list-item')
                    @endforeach
                </div>
                <!-- Default width right side news item -->
                <div>
                    @foreach ($news['secondary_news_items'] as $post)
                        @include('includes.news.news-list-item')
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Bottom news list and categories -->
        @if (!empty($news['news_list']) || $selected_category_id)
            <h1 class="mt-8 mb-1 text-xl font-bold md:text-3xl md:mt-14">
                @lang('news.all_news')
            </h1>
            <div class="text-sm md:text-base mb-4 leading-[130%] font-medium">
                @lang('news.all_news_message')
            </div>

            <!-- News categories -->
            <div class="flex flex-wrap gap-2 mb-4 md:mb-6">
                @include('includes.news.news-category-link', [
                    'route_params' => ['name' => $section->route_name],
                    'is_selected' => $selected_category_id === null,
                ])

                @foreach ($categories as $category)
                    @include('includes.news.news-category-link', [
                        'route_params' => ['name' => $section->route_name, 'c' => $category['id']],
                        'is_selected' => $selected_category_id === $category['id'],
                        'category_name' => $category['name'],
                    ])
                @endforeach
            </div>

            <!-- News list -->
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3 md:gap-5">
                @if (!empty($news['news_list']))
                    @foreach ($news['news_list'] as $post)
                        @include('includes.news.news-list-item')
                    @endforeach
                @endif
            </div>
        @endif
    </main>
@endsection
