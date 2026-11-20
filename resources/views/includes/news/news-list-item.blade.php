<a href="{{ route('section.deep', ['name' => $section->route_name, 'deep_name' => $post['url']]) }}"
    class="flex w-full h-full transition-all duration-300 ease-in-out hover:shadow-lg rounded-2xl">
    <div
        class="relative bg-white rounded-2xl overflow-hidden flex flex-col h-full w-full shadow-[0_4px_10px_0_rgba(0,0,0,0.08)]">
        <!-- Image -->
        <img src="{{ asset($post['image']) }}" alt="{{ $post['image_alt_text'] }}" class="object-cover w-full h-48"
            onerror="this.onerror=null; this.src='{{ asset('images/no_image.png') }}';" />

        <!-- Category tag -->
        <div class="hidden md:block">
            @include('includes.news.news-category-tag', ['class' => 'absolute top-4 right-4'])
        </div>

        <!-- Content -->
        <div class="flex flex-col flex-1 p-4 md:p-6">
            <div class="flex-1">
                <div class="flex items-center">
                    <!-- Date -->
                    <div class="flex-1">
                        @include('includes.news.news-date')
                    </div>

                    <!-- Category tag -->
                    <div class="md:hidden">
                        @include('includes.news.news-category-tag')
                    </div>
                </div>

                <!-- Title -->
                <h2 class="mt-2.5 sm:mt-3 md:mb-2 text-lg font-bold leading-[140%]">
                    {{ $post['title'] }}
                </h2>

                <!-- Tiny description -->
                <p
                    class="text-base font-normal mb-3 leading-[136%] flex-1 {{ isset($compact_view) ? 'hidden' : 'hidden md:block' }}">
                    {{ $post['tiny_description'] }}
                </p>
            </div>

            <div class="hidden md:flex {{ isset($compact_view) ? 'justify-start' : 'justify-end' }}">
                <div class="inline-flex items-center text-base font-light hover:underline space-x-2 text-[#3FAF4F]">
                    <span>
                        @lang('main.read_more')
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21"
                        fill="none" class="{{ isset($compact_view) ? 'hidden' : '' }}">
                        <path
                            d="M1.5864 12.2087H14.632L12.188 14.6528C11.4859 15.3548 12.539 16.4078 13.241 15.7058L15.4669 13.4765L16.9528 11.9883C17.2408 11.6986 17.2408 11.2307 16.9528 10.941L13.241 7.22488C13.0998 7.07976 12.9054 6.99834 12.7029 7.00003C12.0344 7.00011 11.7063 7.81438 12.188 8.27796L14.6378 10.7221H1.54796C0.51836 10.7731 0.595236 12.26 1.5864 12.2087Z"
                            fill="#3FAF4F" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</a>
