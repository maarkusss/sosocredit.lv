@php
    use App\Models\Block;
@endphp

<section class="section_narrower bg-[#3FAF4F]" id="why-choosing-us">
    <div class="pt-4 section__content md:pt-0">
        <div class="section__content__heading-wrapper">
            <h2 class="text-white section__content__heading">
                {{ Block::getValue('why_choose_us_title') }}
            </h2>
        </div>
        {{-- STEPS --}}
        <div class="grid items-start max-w-4xl grid-cols-2 gap-4 mx-auto mt-8 md:grid-cols-4">
            <div class="flex flex-col items-center justify-center">
                <div class="flex items-center justify-center w-16 h-16 bg-green-200 rounded-full">
                    <img src="{{ asset('images/pointer_click.svg') }}" alt="{{ __('alts.pointer_click') }}"
                        loading="lazy" width="34" height="34">
                </div>
                <div class="relative pt-4 lg:px-4">
                    <h3 class="pb-1 text-white section__content__step__subtitle">@lang('main.easy')</h3>
                    <p class="text-white section__content__step__desc">@lang('main.online_service')</p>
                </div>
            </div>
            <div class="flex flex-col items-center justify-center">
                <div class="flex items-center justify-center w-16 h-16 bg-green-200 rounded-full">
                    <img src="{{ asset('images/group.svg') }}" alt="{{ __('alts.group') }}" loading="lazy" width="34" height="34">
                </div>
                <div class="relative pt-4 lg:px-4">
                    <h3 class="pb-1 text-white section__content__step__subtitle">@lang('main.reliable')</h3>
                    <p class="text-white section__content__step__desc">@lang('applicant_form.easy_conditions')</p>
                </div>
            </div>
            <div class="flex flex-col items-center justify-center">
                <div class="flex items-center justify-center w-16 h-16 bg-green-200 rounded-full">
                    <img src="{{ asset('images/discount1.svg') }}" alt="{{ __('alts.discount1') }}" loading="lazy" width="34" height="34">
                </div>
                <div class="relative pt-4 lg:px-4">
                    <h3 class="pb-1 text-white section__content__step__subtitle">@lang('main.profitable')</h3>
                    <p class="text-white section__content__step__desc"> @lang('applicant_form.percent_fee', [
                        'percent_fee' => $section->json['percent_fee'] ?? '5.8',
                    ])</p>
                </div>
            </div>
            <div class="flex flex-col items-center justify-center">
                <div class="flex items-center justify-center w-16 h-16 bg-green-200 rounded-full">
                    <img src="{{ asset('images/24-hours.svg') }}" alt="{{ __('alts.24_hours') }}" loading="lazy" width="34" height="34">
                </div>
                <div class="relative pt-4 lg:px-4">
                    <h3 class="pb-1 text-white section__content__step__subtitle">@lang('main.day_and_night')</h3>
                    <p class="text-white section__content__step__desc">@lang('main.online_access_247_from_any_device')</p>
                </div>
            </div>
        </div>
    </div>
</section>
