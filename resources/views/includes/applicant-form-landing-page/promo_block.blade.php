<section class="flex justify-center items-center w-full h-72 md:h-80 bg-[#D4E6D6] relative overflow-hidden"
    id="slogan-bottom">

    <!-- Promo desktop bg -->
    <img class="absolute z-0 min-h-[21.25rem] object-cover hidden md:block" src="{{ asset('/images/cta_bg.avif') }}"
        alt="{{ __('alts.cta_banner') }}" fetchpriority="high" />

    <div class="relative md:grid md:grid-cols-2">
        <div
            class="mx-4 md:w-[558px] inline-flex flex-col justify-center md:justify-start items-center md:items-start gap-6">
            <div class="md:w-[537px] flex flex-col justify-start md:items-start gap-2">
                <p
                    class="px-6 text-xl font-bold leading-7 text-center md:px-0 md:text-3xl md:leading-9 text-neutral-900 lg:text-left">
                    {{ $section->json['bottom_slogan'] }}
                </p>
                <p
                    class="self-stretch justify-center px-2 text-sm font-medium leading-tight text-center md:px-0 md:text-start text-neutral-900 md:text-base">
                    {{ $section->json['bottom_slogan_description'] ?? '' }}
                </p>
            </div>
            <button
                class="w-full max-w-md md:w-auto h-11 px-6 py-2.5 bg-[#3FAF4F] text-white rounded border border-green-600 inline-flex justify-center items-center transition-all duration-200 cursor-pointer hover:bg-green-700 focus:outline-none focus:shadow-outline buttonScrollTo"
                data-target="applicant_form">
                @lang('Apply')
            </button>
        </div>
        <div class="hidden md:block"></div>
    </div>
</section>
