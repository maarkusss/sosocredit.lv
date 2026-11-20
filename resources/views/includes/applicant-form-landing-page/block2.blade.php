<section class="flex items-center justify-center pt-6 pb-12 bg-zinc-100 md:pb-0" id="requirements-to-unify">
    <div class="w-[467px] inline-flex flex-col justify-start items-start gap-2.5 md:gap-6">
        <div class="">
            <h2
                class="px-1 py-0.5 bg-green-700 rounded justify-start text-white text-lg md:text-xl font-bold leading-normal md:leading-7">
                {{ $section->json['block2_title'] }}
            </h2>
        </div>
        {{-- STEPS --}}
        <div class="section__content__steps-wrapper">
            <div class="justify-start">
                <div class="flex items-start justify-start">
                    <h3 class="text-lg font-bold text-green-700 md:text-2xl md:font-semibold">@lang('main.age')</h3>
                </div>
                <p
                    class="self-stretch justify-start text-sm font-medium leading-normal text-neutral-900 md:text-base md:leading-tight">
                    @lang('main.micro_credit_for_maturity')</p>
            </div>
            <div class="justify-start">
                <div class="flex items-start justify-start">
                    <h3 class="text-lg font-bold text-green-700 md:text-2xl md:font-semibold">@lang('main.citizenship')</h3>
                </div>
                <p
                    class="self-stretch justify-start text-sm font-medium leading-normal text-neutral-900 md:text-base md:leading-tight">
                    @lang('main.country_citizens_and_residents')</p>
            </div>
        </div>
    </div>
</section>
