<section class="flex justify-center w-full px-3 bg-loan-normal" id="slogan-top" style="height: 1px;"></section>
<section class="bg-[#EFEFEF] relative md:min-h-[592px]">
    <!-- Hero desktop bg -->
    <img class="hidden md:block absolute select-none inset-0 w-full h-full object-cover object-center z-0 lg:pr-[7rem] xl:pr-[15rem]"
        src="{{ asset('/images/hero/' . $section->getHeroBgName() . '.avif') }}"
        alt="{{ __('alts.' . $section->getHeroBgName()) }}" fetchpriority="high"
        onerror="this.src='{{ asset('/images/hero/paterina-kredits.avif') }}';" />

    <!-- Hero mobile bg -->
    <img class="absolute inset-0 z-0 object-cover object-center w-full h-full select-none md:hidden"
        src="{{ asset('/images/hero/mobile-' . $section->getHeroBgName() . '.avif') }}"
        alt="{{ __('alts.' . $section->getHeroBgName()) }}" fetchpriority="high"
        onerror="this.src='{{ asset('/images/hero/mobile-paterina-kredits.avif') }}';" />

    <div class="section__content relative pt-5 pb-[18rem] md:py-8 ">
        <div class="flex flex-col items-center justify-center">
            @if (!empty($section->h1_description))
                <div class="section__content__hero">
                    <div class="w-full mt-2 text-sm leading-5 text-center md:mt-4 md:text-base">
                        {{ $section->h1_description }}
                    </div>
                </div>
            @endif
            @include('includes.applicant-form.step0')
        </div>
    </div>
</section>
