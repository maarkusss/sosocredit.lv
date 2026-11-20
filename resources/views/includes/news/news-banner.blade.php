<div class="my-10 text-white rounded-2xl p-7 md:p-10 responsive-banner-bg">
    <div class="mb-3 text-2xl leading-[130%] font-medium">
        @lang('news.news_banner_message')
    </div>
    <div class="mb-5 md:mb-9 text-lg leading-[130%] font-normal">
        @lang('news.news_banner_message_2')
    </div>
    <div>
        <a href="{{ route('home.index', ['utm_source' => 'reviews', 'utm_campaign' => $section['id']]) }}"
            class="text-[#081B1A] px-6 py-2.5 rounded bg-white inline-block text-base transition-all duration-300 ease-in-out hover:shadow-lg font-semibold">
            @lang('main.submit_enquiry')
        </a>
    </div>
</div>

<style scoped>
    .responsive-banner-bg {
        background: url('{{ asset('/images/news-banner-small-x2-min.jpg') }}') center/cover no-repeat;
        min-height: 420px;
    }

    @media (min-width: 768px) {
        .responsive-banner-bg {
            background: url('{{ asset('/images/news-banner-large-x2-min.jpg') }}') center/cover no-repeat;
            min-height: 260px;
        }
    }
</style>
