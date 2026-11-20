@php
    use App\Models\Application;
    use App\Models\Section;

    $is_business = false;

    if (!empty($application->section_id)) {
        $section = Section::where('id', $application->section_id)->first();
        $is_business = in_array($section->common_name, ['kredits-uznemumam']);
    }

    /** @var Application $application */
    /** @var Section $section */
    /** @var array $applicant_form_types */

@endphp


@extends('layouts.public', ['meta_robots' => 'noindex, nofollow'])

@section('content')
    <script src="https://unpkg.com/@popperjs/core@2" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/tippy.js@6" referrerpolicy="no-referrer"></script>

    <style type="text/css">
        @keyframes blink {
            50% {
                color: transparent
            }
        }

        .loader__dot {
            animation: 1s blink infinite
        }

        .loader__dot:nth-child(2) {
            animation-delay: 250ms
        }

        .loader__dot:nth-child(3) {
            animation-delay: 500ms
        }


        .loader {
            width: 40px;
            height: 40px;
            display: inline-block;
            position: relative;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        .loader::after {
            content: '';
            box-sizing: border-box;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid transparent;
            border-bottom-color: #3faf4f;
        }

        .public-section_loan .loader::after {
            border-bottom-color: #3faf4f;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .text {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .text_inactive {
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        .text_active {
            opacity: 1;
            transition: opacity 0.5s ease-in-out;
        }
    </style>

    @if (empty($application->complete_opened_once) &&
            config('app.name') === 'Sosocredit.lv' &&
            ($head_code_go = \App\Models\Setting::retrieve('head_code_go')))
        {!! $head_code_go !!}
    @endif

    <main>

        @include('includes.modals.additional_required_fields_modal')

        <section class="flex justify-center w-full px-3 bg-loan-normal" id="slogan-top" style="height: 1px;"></section>
        <section class="py-5 section md:py-8">
            <div class="max-w-screen-xl px-0 mx-auto md:px-4">
                <div class="max-w-[320px] md:max-w-[1136px] mx-auto place-items-center">
                    <div class="flex flex-col items-center justify-between w-full md:flex-row" id="preLoadingContainer">
                        {{--                        <div class="text-center md:text-left"> --}}
                        {{--                            <h2 class="pt-0 mt-0 mb-0 text-lg font-bold md:mb-4 sm:text-xl md:text-xl text_neutral-900"> --}}
                        {{--                                @if (empty($application->complete_opened_once)) --}}
                        {{--                                    @lang("Your application is being processed") --}}
                        {{--                                @else --}}
                        {{--                                    @lang("My application") {{ $application->created_at->format('d.m.Y') }} --}}
                        {{--                                @endif --}}

                        {{--                                <div class="inline-block loader__dots"> --}}
                        {{--                                    <span class="loader__dot">.</span> --}}
                        {{--                                    <span class="loader__dot">.</span> --}}
                        {{--                                    <span class="loader__dot">.</span> --}}
                        {{--                                </div> --}}
                        {{--                            </h2> --}}

                        {{--                            @if (empty($application->complete_opened_once)) --}}
                        {{--                                <p class="mt-1 text-sm font-bold md:text-base"> --}}
                        {{--                                    @lang('Please be patient as this process takes a few minutes') --}}
                        {{--                                </p> --}}
                        {{--                            @endif --}}
                        {{--                        </div> --}}

                        {{--                        <div class="hidden md:block"> --}}
                        {{--                            <span class="loader"></span> --}}
                        {{--                        </div> --}}
                    </div>

                    <div class="flex flex-col items-center justify-between hidden w-full px-4 md:px-1 md:flex-row"
                        id="postLoadingContainer">
                        {{--                        <div class="flex flex-row items-start justify-between w-full mx-2 text-center sm:text-left"> --}}
                        {{--                            <h2 class="pt-0 mt-0 mb-2 text-lg font-bold md:mb-4 md:text-xl text_neutral-900"> --}}
                        {{--                                @lang("My application") {{ $application->created_at->format('d.m.Y') }} --}}
                        {{--                                <div class="inline-block loader__dots" id="dotsOnReady"> --}}
                        {{--                                    <span class="loader__dot">.</span> --}}
                        {{--                                    <span class="loader__dot">.</span> --}}
                        {{--                                    <span class="loader__dot">.</span> --}}
                        {{--                                </div> --}}
                        {{--                            </h2> --}}

                        {{--                            @if (!empty($application->amount)) --}}
                        {{--                                <div class="flex items-center justify-center gap-2 text-lg font-bold md:text-xl text_primary"> --}}
                        {{--                                    {{ $application->amount }}&nbsp;{{ currency_symbol() }} --}}
                        {{--                                    <div class="hidden md:block" id="spinnerOnReady"> --}}
                        {{--                                        <span class="loader"></span> --}}
                        {{--                                    </div> --}}
                        {{--                                </div> --}}

                        {{--                            @endif --}}
                        {{--                        </div> --}}
                    </div>

                    <div class="w-full px-3 py-3 mb-4 bg-white rounded-lg shadow-lg md:px-4 md:py-4"
                        x-data="{ showInfo: false }">
                        <div class="items-center w-full gap-6 md:inline-flex md:justify-between">
                            <div class="flex-col items-start justify-start hidden gap-2 min-w-fit md:inline-flex">
                                <div class="flex text-sm font-normal leading-tight text-neutral-900">
                                    <div class="">@lang('My application') {{ $application->created_at->format('d.m.Y') }}
                                    </div>
                                    <div class="loader__dots" id="dotsOnReady">
                                        <span class="loader__dot">.</span>
                                        <span class="loader__dot">.</span>
                                        <span class="loader__dot">.</span>
                                    </div>
                                </div>
                                <div class="inline-flex items-start justify-start gap-6">
                                    <div class="flex items-center gap-1">
                                        <div class="text-base font-bold leading-tight text-neutral-900">@lang('Sum'):
                                        </div>
                                        <div class="text-lg font-bold leading-normal text-green-500">
                                            {{ $application->amount }} {{ currency_symbol() }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-col md:flex-row">
                                <div class="m-3 md:pb-0
                                    @if ($application->completion_percentage < 99) md:pb-4 @endif
                                    md:m-0"
                                    id="spinnerOnReady">
                                    <div class="flex items-center gap-2">
                                        <span class="loader"></span>
                                        <div>
                                            <p>@lang('The application is in review')</p>
                                            <div class="text-xs font-normal leading-tight text-green-800">
                                                @lang('Please be patient as this process takes a few minutes')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if ($application->completion_percentage < 99 && !$is_business)
                                    <div class="flex flex-col gap-4 lg:flex-row">
                                        <a href="{{ route('application.return-update-gocardless', $application->token) }}"
                                            class="button_primary h-11 mx-4 md:mx-auto flex flex-row items-center justify-center text-center px-6 py-2.5 text-base text-white font-medium rounded border">
                                            @lang('main.submit_gocardless')
                                        </a>
                                        <div class="flex-row content-center justify-center max-w-xs mb-2 md:mb-0"
                                            id="gocardlessDisclaimer">
                                            <div class="text-xs font-normal leading-tight text-green-800">
                                                @lang('Submitting GoCardless significantly increases chances')
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button class="flex justify-between w-full md:w-auto min-w-fit md:justify-end md:gap-4"
                                @click="showInfo = !showInfo">
                                <div class="justify-start text-base font-medium leading-tight text-neutral-900">
                                    @lang('Review application')</div>
                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" :class="{ 'rotate-180': showInfo }"
                                    class="transition-transform duration-200">
                                    <path d="M6 9.5L12 15.5L18 9.5" stroke="#081B1A" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                        <div x-show="showInfo" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-95"
                            class="flex flex-col items-start justify-start gap-2 mt-4 text-base font-medium leading-tight text-neutral-900">
                            <div class="items-start justify-start gap-2">
                                <div class="text-sm font-normal leading-tight text-neutral-900">
                                    <div class="pb-2 md:hidden">@lang('My application')
                                        {{ $application->created_at->format('d.m.Y') }}
                                    </div>
                                    <p>
                                        @lang('Loan type'): {{ $application->section->name ?? '' }}
                                    </p>
                                    <div class="inline-flex items-start justify-start gap-6 md:hidden">
                                        <div class="flex items-center gap-1">
                                            <div class="text-sm leading-tight text-neutral-900">@lang('Sum'):</div>
                                            <div class="text-sm leading-normal text-green-500">{{ $application->amount }}
                                                {{ currency_symbol() }}</div>
                                        </div>
                                    </div>
                                    <p class="pt-2">{{ __('main.loan_term') }}: {{ $application->term_months }}
                                        @lang('months')</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full px-3 py-3 md:px-4 md:py-4 lg:p-8" id="applicant_form">
                        <div class="applicant-form__wrapper">
                            <div>
                                <input type="hidden" id="application_token" value="{{ $application->token }}" />
                                <input type="hidden" id="application_hash" value="{{ $application->hash }}" />
                                <div
                                    class="flex flex-col items-center justify-start text-center min-h-[340px] sm:min-h-[460px] h-full">
                                    <div id="resultContainer" class="w-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script type="text/javascript">
        function blinkTexts() {
            const blinkingTexts = [
                "@lang('Lenders in our network are reviewing your request.')",
                "@lang('As soon as lenders respond we will redirect you to the lender.')",
                "@lang('Please wait while we process your request with our panel of authorized lenders or lender-networks.')",
                "@lang('In case of approval, you will be redirected to their site, or an electronic signature page to review the loan terms.')",
            ];

            const blinkingText = document.getElementById("blinkingText");

            setInterval(function() {
                if (blinkingText) {
                    let new_text = blinkingTexts[Math.floor(Math.random() * blinkingTexts.length)];
                    if (new_text !== blinkingText.innerText) {
                        blinkingText.classList.add("text_inactive");
                        blinkingText.classList.remove("text_active");

                        setTimeout(function() {
                            blinkingText.innerText = new_text;
                            blinkingText.classList.remove("text_inactive");
                            blinkingText.classList.add("text_active");
                        }, 650);
                    }
                }
            }, 7500);
        }

        function checkStatusAndRedirect(application_token, application_hash) {
            const SECONDS = Math.floor(Math.random() * (10 - 5 + 1) + 5),
                ENDPOINT = "/api/application/" + application_token + "/" + application_hash + "/status",
                resultContainer = document.getElementById("resultContainer"),
                applicantForm = document.getElementById("applicant_form"),
                dotsOnReady = document.getElementById("dotsOnReady"),
                spinnerOnReady = document.getElementById("spinnerOnReady"),
                preLoadingContainer = document.getElementById("preLoadingContainer"),
                postLoadingContainer = document.getElementById("postLoadingContainer");

            let SHOULD_POLL = true;

            let poll = async function() {
                    await fetch(ENDPOINT, {
                            method: "GET",
                            headers: {
                                'Accept': 'application/json',
                                'Accept-Language': '{{ app()->getLocale() }}'
                            },
                        })
                        .then(function(response) {
                            if (response.ok) {
                                return response.json();
                            }

                            if (response.status >= 400 && response.status <= 503) {
                                throw {
                                    message: response.statusText || -1,
                                    status: response.status || -1,
                                    url: response.url || "",
                                }
                            }

                            if (response.statusText) {
                                throw new Error(response.statusText);
                            }

                            throw new Error('Something went wrong.');
                        })
                        .then((data) => {
                            data = data.data;
                            console.log(new Date(), " Common response status: " + data.status);

                            if (data.has_approved) {
                                console.log("Firing Google Conversion.");
                                fireGoogleConversion();
                                fireFacebookConversion();
                            }

                            if (data.status && data.status === "Pending") {
                                // Still pending - do nothing
                            } else if (data.status && data.status === "Ready") {
                                // Ready to show some results
                                preLoadingContainer.classList.add("hidden");
                                postLoadingContainer.classList.remove("hidden");
                                if (data.hasOwnProperty("status_html") && data.status_html) {
                                    // Display status html if provided
                                    resultContainer.classList.add("text_inactive");
                                    setTimeout(function() {
                                        resultContainer.innerHTML = data.status_html;
                                        resultContainer.classList.add("text_active");
                                        resultContainer.classList.remove("text_inactive");
                                        applicantForm.classList.add("section__content__make-bg-visible")
                                        if (resultContainer.getElementsByClassName('hasTippy')) {
                                            tippy('[data-tippy-content]');
                                        }
                                    }, 550);
                                }
                            } else if (data.status && data.status === "Completed") {
                                // All done, stop polling and show results
                                SHOULD_POLL = false;
                                dotsOnReady.classList.add("hidden");
                                spinnerOnReady.classList.add("hidden");
                                preLoadingContainer.classList.add("hidden");
                                postLoadingContainer.classList.remove("hidden");
                                if (data.hasOwnProperty("status_html") && data.status_html) {
                                    // Display status html if provided
                                    resultContainer.classList.add("text_inactive");
                                    setTimeout(function() {
                                        resultContainer.innerHTML = data.status_html;
                                        resultContainer.classList.add("text_active");
                                        resultContainer.classList.remove("text_inactive");
                                        applicantForm.classList.add("section__content__make-bg-visible")
                                        if (resultContainer.getElementsByClassName('hasTippy')) {
                                            tippy('[data-tippy-content]');
                                        }
                                    }, 550);
                                }
                            }
                        })
                        .catch((error) => {
                            if (error.status === 400 || error.status === 401 || error.status === 404) {
                                SHOULD_POLL = false;
                            }

                            console.error(`Application status Error: ${error.message}`, error);
                        });
                },
                pollInterval = setInterval(function() {
                    if (SHOULD_POLL === false) {
                        console.log("Stopping the poll.");
                        clearInterval(pollInterval);
                        return;
                    }

                    poll();
                }, SECONDS * 1000);

            poll();
        }

        blinkTexts();

        setTimeout(function() {
            let application_token = document.getElementById("application_token") ? document.getElementById(
                    "application_token").value : "",
                application_hash = document.getElementById("application_hash") ? document.getElementById(
                    "application_hash").value : "";

            checkStatusAndRedirect(application_token, application_hash);
        }, 500);

        function fireGoogleConversion() {
            // Prevent firing twice
            if (window.__conversion_fired__) return;
            window.__conversion_fired__ = true;

            // Load the Tag Manager script dynamically
            let script = document.createElement('script');
            script.async = true;
            script.src = "https://www.googletagmanager.com/gtag/js?id=AW-16524480321";
            document.head.appendChild(script);

            // Initialize gtag after script loads
            script.onload = function() {
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }
                window.gtag = gtag;

                gtag('js', new Date());
                gtag('config', 'AW-16524480321');

                gtag('event', 'conversion', {
                    'send_to': 'AW-16524480321/PzotCJbf3McbEMGevsc9',
                    'value': 1.0,
                    'currency': 'EUR',
                    'aw_remarketing_only': true
                });

                console.log("Google conversion fired.");
            };
        }

        function fireFacebookConversion() {
            // Prevent firing twice
            if (window.__fb_conversion_fired__) return;
            window.__fb_conversion_fired__ = true;

            // Make sure fbq exists
            if (typeof fbq !== 'function') {
                console.warn('Facebook Pixel not loaded yet.');
                return;
            }

            // Fire the conversion event
            fbq('track', 'Lead', {
                value: 1.0,
                currency: 'EUR'
            });

            console.log('Facebook conversion fired.');
        }
    </script>
@endsection
