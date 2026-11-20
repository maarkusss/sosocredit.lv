@php
    use App\Models\Block;
@endphp

@extends('layouts.public')

@section('content')
    <main>
        <section class="section_slogan px-3 py-2 flex justify-center" id="slogan-top">
            <span class="text-lg font-semibold leading-tight text-center text-white">
                @lang('main.easy_profitable_reliable_day_and_night')
            </span>
        </section>
        <section class="section py-8">
            <div class="section__content">
                <div class="flex flex-col items-center justify-center">
                    <div class="section__content__hero">
                        <h1 class="section__content__hero__heading">
                            @lang('main.micro_credits_online_below_1p')
                        </h1>
                    </div>
                    <div class="landing-form-wrapper">
                        @include('includes.landing-form.form')
                    </div>
                </div>
            </div>
        </section>
        <section class="section" id="why-choosing-us">
            <div class="section__content">
                <div class="section__content__heading-wrapper">
                    <h2 class="section__content__heading">
                        @lang('main.why_choosing_us')
                    </h2>
                </div>
                {{-- STEPS --}}
                <div class="flex flex-wrap">
                    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full sm:w-1/4">
                        <div class="flex items-center justify-center w-32 h-32 bg-white rounded-lg shadow-lg">
                            <svg class="w-16 h-16 fill-current text_primary"
                                 viewBox="0 0 24 24">
                                <path
                                    d="M18.71 7.21a1 1 0 0 0-1.42 0l-7.45 7.46-3.13-3.14A1 1 0 1 0 5.29 13l3.84 3.84a1 1 0 0 0 1.42 0l8.16-8.16a1 1 0 0 0 0-1.47Z"/>
                            </svg>
                        </div>
                        <div class="relative lg:px-4">
                            <h4 class="section__content__step__subtitle">@lang('main.easy')</h4>
                            <p class="section__content__step__desc">@lang('main.online_service')</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full mt-8 sm:mt-0 sm:w-1/4">
                        <div class="flex items-center justify-center w-32 h-32 bg-white rounded-lg shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 class="w-16 h-16 fill-current text_primary"
                                 viewBox="0 0 256 256">
                                <path
                                    d="M80,64a8,8,0,0,1,8-8H216a8,8,0,0,1,0,16H88A8,8,0,0,1,80,64Zm136,56H88a8,8,0,0,0,0,16H216a8,8,0,0,0,0-16Zm0,64H88a8,8,0,0,0,0,16H216a8,8,0,0,0,0-16ZM44,52A12,12,0,1,0,56,64,12,12,0,0,0,44,52Zm0,64a12,12,0,1,0,12,12A12,12,0,0,0,44,116Zm0,64a12,12,0,1,0,12,12A12,12,0,0,0,44,180Z"></path>
                            </svg>
                        </div>
                        <div class="relative lg:px-4">
                            <h4 class="section__content__step__subtitle">@lang('main.reliable')</h4>
                            <p class="section__content__step__desc">@lang('main.transparent_terms_with_no_commissions')</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full mt-8 sm:mt-0 sm:w-1/4">
                        <div class="flex items-center justify-center w-32 h-32 bg-white rounded-lg shadow-lg">
                            <svg class="w-16 h-16 fill-current text_primary"
                                 viewBox="0 0 24 24">
                                <path
                                    d="m15.29 10.29-2.78 2.78A2.09 2.09 0 0 0 12 13a2 2 0 0 0-2 2 2.09 2.09 0 0 0 .07.51l-.78.78a1 1 0 0 0 0 1.42 1 1 0 0 0 1.42 0l.78-.78A2.09 2.09 0 0 0 12 17a2 2 0 0 0 2-2 2.09 2.09 0 0 0-.07-.51l2.78-2.78a1 1 0 0 0-1.42-1.42ZM12 4A10 10 0 0 0 2 14a9.91 9.91 0 0 0 1.69 5.56 1 1 0 0 0 1.66-1.12 8 8 0 1 1 13.3 0 1 1 0 0 0 .27 1.39 1 1 0 0 0 .56.17 1 1 0 0 0 .83-.44A9.91 9.91 0 0 0 22 14 10 10 0 0 0 12 4Z"/>
                            </svg>
                        </div>
                        <div class="relative lg:px-4">
                            <h4 class="section__content__step__subtitle">@lang('main.profitable')</h4>
                            <p class="section__content__step__desc">@lang('main.individual_approach')</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full mt-8 sm:mt-0 sm:w-1/4">
                        <div class="flex items-center justify-center w-32 h-32 bg-white rounded-lg shadow-lg">
                            <svg class="w-16 h-16 fill-current text_primary"
                                 viewBox="0 0 24 24">
                                <path
                                    d="M15.098 12.634 13 11.423V7a1 1 0 0 0-2 0v5a1 1 0 0 0 .5.866l2.598 1.5a1 1 0 1 0 1-1.732ZM12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8 8.01 8.01 0 0 1-8 8Z"/>
                            </svg>
                        </div>
                        <div class="relative lg:px-4">
                            <h4 class="section__content__step__subtitle">@lang('main.day_and_night')</h4>
                            <p class="section__content__step__desc">@lang('main.online_access_247_from_any_device')</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section" id="requirements-to-obtain">
            <div class="section__content">
                <div class="section__content__heading-wrapper">
                    <h2 class="section__content__heading">
                        @lang('main.requirements_to_get_micro_credit')
                    </h2>
                </div>
                {{-- STEPS --}}
                <div class="section__content__steps-wrapper">
                    <div class="section__content__step">
                        <div class="flex items-center justify-center">
                            <span class="section__content__step__title">@lang('main.age')</span>
                        </div>
                        <p class="section__content__step__desc">@lang('main.micro_credit_for_maturity')</p>
                    </div>
                    <div class="section__content__step">
                        <div class="flex items-center justify-center">
                            <span class="section__content__step__title">@lang('main.citizenship')</span>
                        </div>
                        <p class="section__content__step__desc">@lang('main.country_citizens_and_residents')</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="section" id="how-to-obtain">
            <div class="section__content">
                <div class="section__content__heading-wrapper">
                    <h2 class="section__content__heading">
                        @lang('main.how_to_obtain_micro_credit')
                    </h2>
                </div>
                {{-- STEPS --}}
                <div class="flex flex-wrap">
                    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full sm:w-1/3">
                        <div class="flex items-center justify-center w-32 h-32 bg-white rounded-lg shadow-lg">
                            <svg class="w-16 h-16 fill-current text_primary">
                                <path
                                    d="M42.667 37.333H21.333a2.667 2.667 0 0 0 0 5.334h21.334a2.667 2.667 0 1 0 0-5.334zm0-10.666h-16a2.667 2.667 0 0 0 0 5.333h16a2.667 2.667 0 0 0 0-5.333zm10.666-16h-8V8A2.667 2.667 0 1 0 40 8v2.667h-5.333V8a2.667 2.667 0 0 0-5.334 0v2.667H24V8a2.667 2.667 0 0 0-5.333 0v2.667h-8A2.667 2.667 0 0 0 8 13.333v37.334a8 8 0 0 0 8 8h32a8 8 0 0 0 8-8V13.333a2.667 2.667 0 0 0-2.667-2.666zm-2.666 40A2.667 2.667 0 0 1 48 53.333H16a2.667 2.667 0 0 1-2.667-2.666V16h5.334v2.667a2.667 2.667 0 1 0 5.333 0V16h5.333v2.667a2.667 2.667 0 0 0 5.334 0V16H40v2.667a2.667 2.667 0 0 0 5.333 0V16h5.334v34.667z"/>
                            </svg>
                        </div>
                        <div class="relative lg:px-4">
                            <h4 class="section__content__step__subtitle">
                                1. @lang('main.fill_enquiry')</h4>
                            <p class="section__content__step__desc">@lang('main.after_signup_pick_best_offer_and_fill_enquiry_form')</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full mt-8 sm:mt-0 sm:w-1/3">
                        <div class="flex items-center justify-center w-32 h-32 bg-white rounded-lg shadow-lg">
                            <svg class="w-16 h-16 fill-current text_primary">
                                <path
                                    d="M53.333 23.84a3.488 3.488 0 0 0-.16-.72v-.24a2.854 2.854 0 0 0-.506-.747l-16-16a2.854 2.854 0 0 0-.747-.506.853.853 0 0 0-.24 0 2.347 2.347 0 0 0-.88-.294H18.667a8 8 0 0 0-8 8v37.334a8 8 0 0 0 8 8h26.666a8 8 0 0 0 8-8V23.84zm-16-9.413l6.907 6.906H40a2.667 2.667 0 0 1-2.667-2.666v-4.24zM48 50.667a2.667 2.667 0 0 1-2.667 2.666H18.667A2.666 2.666 0 0 1 16 50.667V13.333a2.667 2.667 0 0 1 2.667-2.666H32v8a8 8 0 0 0 8 8h8v24zm-9.893-17.894l-8.774 8.8-3.44-3.466a2.678 2.678 0 0 0-3.786 3.786l5.333 5.334a2.666 2.666 0 0 0 3.787 0L41.893 36.56a2.678 2.678 0 1 0-3.786-3.787z"/>
                            </svg>
                        </div>
                        <div class="relative lg:px-4">
                            <h4 class="section__content__step__subtitle">
                                2. @lang('main.obtain_approval')</h4>
                            <p class="section__content__step__desc">@lang('main.solution_can_be_obtained_within_5_minutes')</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-start flex-shrink-0 w-full mt-8 sm:mt-0 sm:w-1/3">
                        <div class="flex items-center justify-center w-32 h-32 bg-white rounded-lg shadow-lg">
                            <svg class="w-16 h-16 fill-current text_primary">
                                <path
                                    d="M16 29.334a2.667 2.667 0 1 0 0 5.333 2.667 2.667 0 0 0 0-5.334zm32 0a2.667 2.667 0 1 0 0 5.333 2.667 2.667 0 0 0 0-5.334zm5.333-16H10.667a8 8 0 0 0-8 8v21.333a8 8 0 0 0 8 8h42.666a8 8 0 0 0 8-8V21.333a8 8 0 0 0-8-8zM56 42.666a2.667 2.667 0 0 1-2.667 2.666H10.667A2.667 2.667 0 0 1 8 42.668V21.333a2.667 2.667 0 0 1 2.667-2.666h42.666A2.667 2.667 0 0 1 56 21.333v21.334zM32 24a8 8 0 1 0 0 16.001 8 8 0 0 0 0-16zm0 10.667a2.667 2.667 0 1 1 0-5.334 2.667 2.667 0 0 1 0 5.334z"/>
                            </svg>
                        </div>
                        <div class="relative lg:px-4">
                            <h4 class="section__content__step__subtitle">
                                3. @lang('main.get_money')</h4>
                            <p class="section__content__step__desc">@lang('main.you_will_get_couple_minutes_upon_approval')</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section" id="how-to-pay">
            <div class="section__content">
                <div class="section__content__heading-wrapper">
                    <h2 class="section__content__heading">
                        @lang('main.how_to_pay_micro_credit')
                    </h2>
                </div>
                {{-- STEPS --}}
                <div class="section__content__steps-wrapper">
                    <div class="section__content__step">
                        <div class="flex items-center justify-center">
                            <span class="section__content__step__title">1. @lang('main.in_internet')</span>
                        </div>
                        <p class="section__content__step__desc">
                            @lang('main.can_pay_via_card_in_micro_creditors_area')
                        </p>
                    </div>
                    <div class="section__content__step">
                        <div class="flex items-center justify-center">
                            <span class="section__content__step__title">2. @lang('main.in_bank_branch')</span>
                        </div>
                        <p class="section__content__step__desc">@lang('main.in_bank_branch_description')</p>
                    </div>
                    <div class="section__content__step">
                        <div class="flex items-center justify-center">
                            <span
                                class="section__content__step__title">3. @lang('main.in_clients_profile')</span>
                        </div>
                        <p class="section__content__step__desc">@lang('main.in_clients_profile_description')</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="section_narrower" id="home-reviews">
            <div class="section__content">
                <div class="section__content__heading-wrapper">
                    <h2 class="section__content__heading">
                        @lang('main.reviews')
                    </h2>
                </div>
                <div class="flex items-stretch gap-8 pb-4 overflow-x-auto sm:grid sm:grid-cols-3 sm:overflow-x-visible"
                     style="scroll-snap-type: x mandatory; scroll-behavior: smooth;"
                     id="container_review">
                    {{-- Customer reviews --}}
                    @include('includes.reviews.reviews')
                </div>
                <div class="flex items-center justify-center mx-auto sm:hidden">
                    @include('includes.reviews.bullets')
                </div>
            </div>
        </section>
        <section class="section" id="important-info">
            <div class="section__content">
                <p class="text-sm font-normal leading-tight text-left text-dark">
                    {{ Block::getValue('home_footer') }}
                </p>
            </div>
        </section>
        <section class="section_slogan" id="slogan-bottom">
            <div class="flex flex-col items-center justify-center max-w-screen-xl p-4 mx-auto lg:flex-row">
                <p class="text-xl font-normal leading-tight text-center text-white lg:text-left">
                    @lang('main.get_up_to')
                </p>
                <a class="px-4 py-3 mt-4 text-lg font-semibold text-center text-darkest transition-all duration-200 bg-gray-100 rounded cursor-pointer lg:mt-0 lg:ml-4 hover:bg-gray-300 focus:outline-none focus:shadow-outline buttonScrollTo"
                   data-target="landing_form">
                    @lang('main.take_micro_credit')
                </a>
            </div>
        </section>
    </main>
@endsection

@section('javascript')
    @include('includes.modals.rightsofusagemodal')

    @if (config('landing_form.show_optional_subscribe'))
        @include('includes.modals.recieveoffersmodal')
    @endif

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js"
            defer></script>

    <script>
        function onTypeIdChange() {
            const type_id = document.getElementById("type_id").value;

            window.location.href = type_id;
        }
    </script>
@endsection
