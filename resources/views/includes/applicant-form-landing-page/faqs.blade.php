<section class="section_narrower">
    <div class="section__content pt-4 md:pt-0">
        <div class="section__content__heading-wrapper">
            <h2 class="section__content__heading text-white bg-green-700 p-1 rounded-md">
                @lang('main.faqs')
            </h2>
        </div>
        <div class="flex flex-col items-center justify-center gap-2 pt-8 max-w-3xl mx-auto">
            @foreach($faqs as $faq)
                <div class="flex-col self-stretch px-6 py-4 bg-white rounded-lg shadow-[0px_4px_4px_0px_rgba(0,0,0,0.10)] inline-flex justify-between items-center"
                     x-data="{ open: false }">
                    <button @click="open = !open" class="w-full">
                        <div class="flex items-start w-full">
                            <div class="flex-1 min-w-0 justify-start text-neutral-900 text-sm sm:text-base text-left font-medium leading-tight break-after-auto">{{ $faq->question }}</div>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                                     class="shrink-0"
                                     x-show="!open">
                                    <path d="M19 13h-6v6a1 1 0 0 1-2 0v-6H5a1 1 0 0 1 0-2h6V5a1 1 0 0 1 2 0v6h6a1 1 0 0 1 0 2z" fill="#081B1A"/>
                                </svg>
                                <svg width="22" height="22" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"
                                     class="pr-1 pt-1 shrink-0"
                                     x-show="open">
                                    <path d="M14 9H2C1.73478 9 1.48043 8.89464 1.29289 8.70711C1.10536 8.51957 1 8.26522 1 8C1 7.73478 1.10536 7.48043 1.29289 7.29289C1.48043 7.10536 1.73478 7 2 7H14C14.2652 7 14.5196 7.10536 14.7071 7.29289C14.8946 7.48043 15 7.73478 15 8C15 8.26522 14.8946 8.51957 14.7071 8.70711C14.5196 8.89464 14.2652 9 14 9Z" fill="#081B1A"/>
                                </svg>
                        </div>
                    </button>
                    <div class="max-w-full prose mt-2 justify-start text-zinc-500 text-sm font-medium leading-tight"
                         x-show="open"
                         x-transition>
                        {!! $faq->answer !!}
                    </div>
                </div>

            @endforeach
            <a href="#"
               class="h-11 w-full max-w-md md:w-44 px-6 py-2.5 mt-4 bg-[#3FAF4F] transition-all duration-200 cursor-pointer hover:bg-green-700 focus:outline-none focus:shadow-outline border border-green-600 rounded inline-flex justify-center items-center gap-1"
               @click.prevent="window.location.href = '{{ app()->getLocale() === 'lv' ? '/buj' : 'ru/faq' }}'">
                <div class="justify-start text-white text-base font-normal md:font-bold leading-tight">@lang('main.read_more')</div>
            </a>
        </div>
    </div>
</section>
