@php
    use App\Models\Application;
    use App\Models\Section;

    /** @var Application $application */
    /** @var array $applicant_form_types */
    /** @var Section $section */

    $is_business = false;
    $is_mortgage = false;
    $is_car_mortgage = false;
    $section_id = $section->parent_section_id ?? '';
    $loantype = $section->parent?->name ?? '';

    $current_loantype = collect($applicant_form_types)->filter(function($array) use ($section) {
        return $array['id'] == $section->parent_section_id;
    })->first();

    if (!empty($current_loantype)) {
        $is_business = !empty($current_loantype['is_business']);
        $is_mortgage = !empty($current_loantype['is_mortgage']);
        $is_car_mortgage = !empty($current_loantype['is_car_mortgage']);
        $loantype = $current_loantype['name'];
    }
@endphp

@extends('layouts.public')

@section('content')

    <style>
        :root {
            --primary-cta: #ff7473;
            --primary-cta-200: #9ca3af;
            --primary-cta-650: #1A2CBD;
            --grey-darker: #EEF1F7;
            --text-color: #1B2021;
            --text-secondary: #495555;
            --modal-border: #D9D9D9;
            --logo-container-border: #E9EBFC;
        }

        @keyframes jumpInfinite {
            0% {
                margin-top: 0;
            }
            50% {
                margin-top: 20px;
            }
            100% {
                margin-top: 0;
            }
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            font-family: ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,"Apple Color Emoji","Segoe UI Emoji",Segoe UI Symbol,"Noto Color Emoji"
        }

        .row {
            display: flex;
            flex-direction: column;
            flex-wrap: nowrap;
        }

        .institution-arrow-block {
            display: flex;
            align-items: center;
        }

        .institution-arrow-block a {
            text-decoration: none;
            color: var(--primary-cta);
            /*font-size: 1.6rem;*/
            position: relative;
            font-weight: 600;
            left: 1.2rem;
        }

        .institution-arrow-block:hover {
            color: var(--primary-cta-650);
            filter: invert(15%) sepia(85%) saturate(5792%) hue-rotate(239deg) brightness(74%) contrast(101%);
        }

        .institution-arrow-block:hover > a {
            position: relative;
            left: 20px;
            transition:all .5s ease-in-out;
        }

        .institution-arrow-block > .ob-left-arrow {
            cursor: pointer;
            height: 1.4rem;
            filter: invert(61%) sepia(7%) saturate(0%) hue-rotate(225deg) brightness(92%) contrast(92%);
        }

        #institution-content-wrapper {
            margin: 0 auto;
            min-height: 100%;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            grid-gap: 2rem;
            padding: 0;
        }

        .institution-search-bx-body {
            /*max-height: 48vh;*/
            overflow-y: auto;
        }

        .institution-search-bx-body::-webkit-scrollbar-thumb {
            background: #d1d4d4;
            border-radius: 7px;
            height: 3rem;
        }

        .institution-search-bx-body::-webkit-scrollbar {
            width: 6px;
            background-color: #F1F1F1;
            border-radius: 7px;
        }

        .institution-modal-content {
            width: 100%;
            background-color: #fff;
            padding: 1rem 0;
            display: flex;
            flex-direction: column;
            border-radius: 8px;
            border: 0;
            color: var(--text-color);
        }

        #institution-modal-header {
            display: none;
            text-align: right;
        }

        #institution-modal-header h2 {
            text-align: left;
            font-size: 1.8rem;
            margin: 0;
        }

        .institution-modal-footer {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 2rem;
        }

        .institution-modal-footer > a {
            line-height: 2.8rem;
            text-decoration: none;
            color: #808080;
        }

        .institution-modal-footer > a:hover {
            text-decoration: underline;
        }

        .institution-modal-close {
            color: var(--text-secondary);
            cursor: pointer;
            position: relative;
            top: -20px;
        }

        .institution-search-container {
            display: none;
            position: relative;
            top: 2.2rem;
            padding-bottom: 1.5rem;
        }

        .institution-search-container input {
            text-indent: 2.8rem;
        }

        .institution-search-icon {
            position: relative;
            padding: 1rem;
            height: 3.6rem;
            bottom: 3.85rem;
        }

        .institution-search-input {
            width: 100%;
            padding: 1rem;
            border-radius: 4px;
            color: var(--text-secondary);
            border: 1px solid var(--primary-cta-200);
            font-size: 1.6rem;
            font-weight: 400;
            outline-color: var(--primary-cta-200);
        }

        .institution-container {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            min-height: 216px;
        }

        .ob-institution {
            display: flex;
            align-items: center;
            /*padding: 0.75rem .75rem;*/
            border: 0px solid var(--primary-cta-200);
            margin: 0 1% 1% 0;
            border-radius: 6px;
            width: 24%;
            min-height: 4rem;
        }

        .ob-institution a {
            display: flex;
            align-items: center;
            flex-direction: column;
            /*justify-content: center;*/
            justify-content: space-between !important;
            text-decoration: none;
            width: 100%;
            /*height: 100%;*/
        }

        .ob-institution:hover {
            border-color: var(--primary-cta);
            transition: all .25s ease-in-out;
        }

        .ob-institution:active {
            background-color: var(--grey-darker);
        }

        .ob-institution a img.ob-arrow-right {
            display: none;
            /*width: .8rem;*/
            /*height: 1.4rem;*/
            /*margin-left: auto;*/
            /*filter: invert(7%) sepia(6%) saturate(1618%) hue-rotate(142deg) brightness(96%) contrast(87%);*/
        }

        .ob-institution a:active img.ob-arrow-right {
            /*filter: invert(13%) sepia(79%) saturate(4951%) hue-rotate(239deg) brightness(85%) contrast(104%);*/
        }

        .ob-arrow-down {
            display: none;
            position: absolute;
            top: calc(100vh - 50px);
            left: 50%;
            animation: jumpInfinite 1.5s infinite;
        }

        .fi {
            font-size:2.5rem;
        }

        .ob-span-text {
            /*margin-left: 1.5rem;*/
            /*font-size: 1.6rem;*/
            display: none;
            /*font-size: .8rem !important;*/
            /*margin-top: .5rem;*/
            /*font-weight: 500;*/
            /*color: var(--text-color);*/
        }

        img.ob-institution-logo {
            width: 100%;
            /*max-width: 3.5rem;*/
            /*height: 3rem;*/
            border-radius: 6px;
        }

        img.country-logo {
            width: 100%;
            max-width: 3.5rem;
            height: 3.5rem;
            border-radius: 6px;
        }

        .container-onboarding {
            display: none;
            max-width: 35rem;
        }

        .container-onboarding p {
            font-size: 1.6rem;
            font-weight: 400;
            text-align: left;
            line-height: 20px;
            color: var(--text-secondary) !important;
        }

        .ob-btn-primary {
            border: none;
            width: 28rem;
            height: 5.5rem;
            margin-top: 10rem;
            background: var(--primary-cta);
            padding: 1rem;
            color: #fff;
            font-size: 1.4rem;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 10px;
            display: none;
        }

        .ob-btn-primary:hover {
            opacity: 0.75;
        }

        .company-image-wrapper img {
            max-width: 100%;
            height: auto;
            padding: 2.2rem;
        }

        .company-image-wrapper {
            display: flex;
            align-items: center;
            margin-bottom: 2.4rem;
            border: 1px solid var(--logo-container-border);
            background: #FFFFFF;
            border-radius: 16px;
            width: 16rem;
            height: 16rem;
        }

        @media screen and (max-width: 768px) {
            .ob-institution {
                width: 100%;
                margin-right: 0;
            }

            img.ob-institution-logo {
                width: 100%;
                max-width: 7rem;
                border-radius: 6px;
            }

            .ob-institution a {
                flex-direction: row;
                padding-right: .5rem;
            }

            .ob-institution a img.ob-arrow-right {
                display: inline-block;
                width: .8rem;
                height: 1.4rem;
                margin-left: auto;
                filter: invert(7%) sepia(6%) saturate(1618%) hue-rotate(142deg) brightness(96%) contrast(87%);
            }

            .ob-institution a:active img.ob-arrow-right {
                filter: invert(13%) sepia(79%) saturate(4951%) hue-rotate(239deg) brightness(85%) contrast(104%);
            }

            .ob-arrow-down {
                display: inline-block;
                position: absolute;
                top: calc(100vh - 50px);
                left: 50%;
                animation: jumpInfinite 1.5s infinite;
            }

            .ob-span-text {
                margin-left: 1rem;
                display: inline-block !important;
                font-weight: 500;
                font-size: 1rem !important;
                color: var(--text-color);
            }

            .institution-search-bx-body {
                max-height: 100%;
            }

            .institution-modal-content {
                /*height: 100vh;*/
                border-radius: 0;
            }

            .container-onboarding {
                max-width: 100%;
                /*height: 100vh;*/
                padding: 13.8rem 3.2rem 0 3.2rem;
            }

            #institution-content-wrapper {
                display: flex;
                flex-direction: column;
                flex-wrap: wrap;
                padding: 0;
            }

            .ob-btn-primary {
                display: block;
            }

            .institution-container {
                display: flex;
                flex-direction: column;
                flex-wrap: nowrap;
            }

            #institution-modal-header {
                margin-top: 3rem;
            }

            #institution-modal-header h2 {
                font-size: 1.4rem;
            }

            .ob-institution span {
                font-size: 1.9rem;
            }

            .ob-arrow-down {
                display: block;
            }
        }

        @media screen and (max-width: 360px) {
            .ob-span-text {
                font-size: .8rem !important;
            }
        }

        /*@media screen and (device-height: 568px) and (orientation: portrait) and (-webkit-min-device-pixel-ratio: 2) {*/
        /*    html {*/
        /*        font-size: 7px;*/
        /*    }*/
        /*}*/
    </style>

    <main>
        <section class="w-full bg-loan-normal px-3 flex justify-center" id="slogan-top" style="height: 1px;"></section>
        <section class="section_narrower md:section py-5 sm:py-8">
            <div class="lg:grid grid-cols-[2fr_800px_280px_2fr]">
                <div class="hidden lg:block"></div>
                <div class="section__content">
                <div class="mb-5">
                    @include('includes.applicant-form._breadcrumbs', [
                        'step' => 4,
                        'title' => __('Bank account information'),
                        'hide_step4' => $is_business,
                    ])
                </div>
                <div class="flex flex-col items-center justify-center">
                    @if (!empty($section->h1_description))
                        <div class="section__content__hero">

                            <div class="mt-4 w-full text-base leading-5 text-center">
                                {{ $section->h1_description }}
                            </div>
                        </div>
                    @endif
                    <div class="landing-form-wrapper applicant-form">
                        <form action="" method="POST" class="w-full" id="applicant_form" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="section_id" value="{{ $section_id ?? '' }}" />
                            <input type="hidden" name="loantype" value="{{ $loantype ?? '' }}" />
                            <input type="hidden" id="locale" name="locale" value="{{ app()->getLocale() }}" />
                            <input type="hidden" id="institution_id" name="institution_id" value="" />

                            <div class="landing-form__steps">
                                @include('admincp.includes.infobox')

                                <div class="applicant-form__wrapper">
                                    <div>
{{--                                        <h2 class="text-base text-gray-500 border-b border-gray-300 pb-4">@lang('Bank account information')</h2>--}}

                                        <p class="mt-4 mb-1 text-center font-bold">@lang('applicant_form.gocardless_service')</p>

                                        <div class="mt-2 text-center justify-center text-neutral-900 text-sm font-medium leading-tight">@lang('applicant_form.gocardless_disclaimer')</div>

{{--                                        @if (!empty($section->text))--}}
{{--                                            <p>--}}
{{--                                                {!! $section->text !!}--}}
{{--                                            </p>--}}
{{--                                        @endif--}}

                                        <div id="institution-content-wrapper"></div>

{{--                                        <p class="pt-0 text-center font-bold text-gray-600">@lang('or')</p>--}}

{{--                                        <p class="mt-4 pt-0 text-center font-bold text-gray-600">@lang('applicant_form.bank_note_in_pdf')</p>--}}

{{--                                        <div--}}
{{--                                            class="mt-4 mb-4 rounded border-2 border-gray-400 p-4 text-center cursor-pointer hover:border-loan-dark hover:text-gray-500"--}}
{{--                                        >--}}
{{--                                            <div class="mx-6">--}}
{{--                                                <div onclick="openFileBrowser()"--}}
{{--                                                     class="flex flex-row justify-center items-center disabled:opacity-80 disabled:cursor-not-allowed"--}}
{{--                                                >--}}
{{--                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"--}}
{{--                                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"--}}
{{--                                                         class="w-8 h-8 mr-3">--}}
{{--                                                        <path stroke-linecap="round" stroke-linejoin="round"--}}
{{--                                                              d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/>--}}
{{--                                                    </svg>--}}

{{--                                                    <div>@lang('applicant_form.upload_bank_note')</div>--}}
{{--                                                </div>--}}

{{--                                                <input type="file"--}}
{{--                                                       name="pdf"--}}
{{--                                                       accept="application/pdf"--}}
{{--                                                       id="attachPdfFileHandler"--}}
{{--                                                       class="hidden"--}}
{{--                                                />--}}

{{--                                                <p class="font-semibold"--}}
{{--                                                   id="attachPdfFileName"></p>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}

{{--                                        @if (!empty($section->source))--}}
{{--                                            <p>--}}
{{--                                                {!! $section->source !!}--}}
{{--                                            </p>--}}
{{--                                        @endif--}}

                                        <p class="my-2 md:my-4 pt-0 text-center font-bold text-gray-600">@lang('or')</p>

                                        {{-- Checkboxes --}}
                                        <label data-class="applicant-form__checkbox-wrap text-center sm:mx-auto"
                                               class="flex justify-start items-center md:mt-2 md:mb-2 bg-[#EAF6ED] rounded p-2 md:p-4 md:text-center cursor-pointer"
                                               for="step2_will_send_docs_via_email">
                                            <input
                                                    class="mr-1 text-base border-gray-300 rounded focus:ring-loan-dark text-loan-normal"
                                                    name="will_send_docs_via_email"
                                                    id="step2_will_send_docs_via_email"
                                                    value="1"
                                                    type="checkbox"
                                            />

                                            <span class="ml-2 text-neutral-900 text-sm font-medium">
                                                @lang('applicant_form.will_send_bank_note_via_email')
                                                {{ config('app.broker_email') }}
                                            </span>
                                        </label>
                                    </div>

                                    <div class="hidden md:grid grid-cols-1 md:grid-cols-[1fr_3fr] gap-2 md:gap-4 items-center mt-4">
                                        @if ($step < Application::MAX_STEPS && !empty($go_back_link))
                                            {{-- Go back button --}}

                                            <a
                                                href="{{ $go_back_link }}"
                                                class="block order-2 md:order-1 applicant-form__back-button px-5"
                                                id="back_button"
                                                type="button">
                                                <div class="w-full grid grid-cols-[1fr_10fr_1fr]">
                                                    <svg class="h-5 mr-2 mt-1"
                                                         xmlns="http://www.w3.org/2000/svg"
                                                         fill="none"
                                                         viewBox="0 0 24 24"
                                                         stroke="currentColor">
                                                        <path stroke-linecap="round"
                                                              stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M15 19l-7-7 7-7"/>
                                                    </svg>
                                                    @lang('main.back')
                                                    <div></div>
                                                </div>
                                            </a>

                                            {{-- Submit button --}}
                                            <button class="applicant-form__button order-1 md:order-2 text-lg disabled:opacity-50 disabled:cursor-not-allowed flex flex-row justify-center items-center"
                                                    id="submit_button"
                                                    type="submit">

                                                <div class="w-full grid grid-cols-[1fr_10fr_1fr]">
                                                    <div></div>
                                                    @if (!empty($application->phone_verified_at))
                                                        @lang('Continue')
                                                    @else
                                                        @lang('main.confirm')
                                                    @endif

                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                         stroke-width="1.5" stroke="currentColor" class="h-5 ml-2 mt-1">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                                    </svg>
                                                </div>


                                                @include('includes.loading-spinner')
                                            </button>

                                        @else
                                            {{-- Submit button --}}
                                            <button class="applicant-form__button text-lg disabled:opacity-50 disabled:cursor-not-allowed flex flex-row justify-center items-center"
                                                    id="submit_button"
                                                    type="submit">

                                                @if (!empty($application->phone_verified_at))
                                                    @lang('Continue')
                                                @else
                                                    @lang('main.confirm')
                                                @endif

                                                @include('includes.loading-spinner')
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
                <div class="section__aside">
                    @include('includes.applicant-form._aside')
                </div>
                <div class="hidden lg:block"></div>
            </div>
        </section>
        {{--        Sticky footer nav buttons--}}
        @include('includes.applicant-form._sticky_footer')
    </main>
@endsection
