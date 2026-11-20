<a
    href="{{ route('section', $route_params) }}"
    class="whitespace-nowrap rounded-full py-2 px-5 cursor-pointer text-sm sm:text-base transition-all duration-300 ease-in-out {{ $is_selected ? 'bg-[#3FAF4F] text-white' : 'bg-white hover:text-white hover:bg-[#3FAF4F]' }}"
>
    @if(isset($category_name))
        {{ $category_name }}
    @else
        @lang('news.all_news')
    @endif
</a>
