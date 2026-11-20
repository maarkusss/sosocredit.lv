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
            width: 52px;
            height: 52px;
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
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 6px solid transparent;
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

    @if (config('app.name') === 'Sosocredit.lv' && $head_code_go = \App\Models\Setting::retrieve('head_code_go'))
        {!! $head_code_go !!}
    @endif

    <main>
        <section class="w-full bg-loan-normal px-3 flex justify-center" id="slogan-top" style="height: 1px;"></section>
        <section class="section py-5 sm:py-8">
            <div class="max-w-screen-xl px-0 sm:px-4 mx-auto">
                <div class="flex flex-col items-center justify-center">
                    @if (!empty($section->h1_description))
                        <div class="section__content__hero">

                            <div class="mt-4 w-full text-base leading-5 text-center">
                                {{ $section->h1_description }}
                            </div>
                        </div>
                    @endif
                    <div class="landing-form-wrapper applicant-form">
                        <div class="w-full" id="applicant_form">
                            <div class="landing-form__steps">
                                <div class="applicant-form__wrapper">
                                    <div>
                                        <div class="mb-5">
                                            @include('includes.applicant-form._breadcrumbs', [
                                                'step' => 5,
                                                'title' => ' ',
                                                'hide_step4' => $is_business,
                                            ])
                                        </div>

                                        <input type="hidden"
                                               name="application_token"
                                               id="application_token"
                                               value="{{ $application->token }}"
                                        />

                                        <input type="hidden"
                                               name="application_hash"
                                               id="application_hash"
                                               value="{{ $application->hash }}"
                                        />

                                        <div class="flex flex-col items-center justify-start text-center min-h-[340px] sm:min-h-[460px] h-full">
                                            <div class="flex flex-col sm:flex-row justify-between items-center w-full mb-4"
                                                 id="preLoadingContainer">
                                                <div class="text-center sm:text-left">
                                                    <h2 class="mt-0 pt-0 font-bold text-base sm:text-xl md:text-xl text_primary">
                                                        @lang("Your application is being processed")
                                                        <div class="loader__dots inline-block">
                                                            <span class="loader__dot">.</span>
                                                            <span class="loader__dot">.</span>
                                                            <span class="loader__dot">.</span>
                                                        </div>
                                                    </h2>

                                                    <p class="my-2 text-sm sm:text-base font-semibold">
                                                        @lang('Please be patient as this process takes a few minutes')
                                                    </p>
                                                </div>

                                                <div class="hidden sm:block">
                                                    <span class="loader"></span>
                                                </div>
                                            </div>

                                            <div class="flex flex-col sm:flex-row justify-between items-center w-full mb-4 hidden"
                                                 id="postLoadingContainer">
                                                <div class="text-center sm:text-left">
                                                    <h2 class="mt-0 pt-0 font-bold text-base sm:text-xl md:text-xl text_primary">
                                                        @lang("My offers")
                                                    </h2>
                                                    <p class="mt-1 text-sm sm:text-base font-semibold">
                                                        @lang('Please review offers below and select the one which suits best')
                                                    </p>
                                                </div>
                                            </div>

                                            <div id="resultContainer" class="w-full"></div>
                                        </div>
                                    </div>
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

                        setTimeout(function () {
                            blinkingText.innerText = new_text;
                            blinkingText.classList.remove("text_inactive");
                            blinkingText.classList.add("text_active");
                        }, 650);
                    }
                }
            }, 7500);
        }

        function checkStatusAndRedirect(application_token, application_hash) {
            const SECONDS = 5,
                ENDPOINT = "/api/application/" + application_token + "/" + application_hash + "/status",
                resultContainer = document.getElementById("resultContainer"),
                preLoadingContainer = document.getElementById("preLoadingContainer"),
                postLoadingContainer = document.getElementById("postLoadingContainer");

            let SHOULD_POLL = true;

            let poll = async function () {
                    console.log("Polling..", new Date());

                    await fetch(ENDPOINT, {
                        method: "GET",
                        headers: {
                            'Accept': 'application/json'
                        },
                    })
                        .then(function (response) {
                            if (response.ok) {
                                return response.json();
                            }

                            if (response.status >= 400 && response.status < 503) {
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
                            console.log("Common response status:", data.status);

                            if (data.status && data.status === "Pending") {
                                // Still pending - do nothing
                            } else if (data.status && data.status === "Complete") {
                                // positive scenario
                                SHOULD_POLL = false;
                                preLoadingContainer.classList.add("hidden");
                                postLoadingContainer.classList.remove("hidden");
                            }

                            if (data.hasOwnProperty("status_html") && data.status_html) {
                                // Display status html if provided
                                resultContainer.classList.add("text_inactive");
                                setTimeout(function() {
                                    resultContainer.innerHTML = data.status_html;
                                    resultContainer.classList.add("text_active");
                                    resultContainer.classList.remove("text_inactive");
                                }, 550);
                            }
                        })
                        .catch((error) => {
                            console.error(`Application status Error: ${error.message}`, error);
                        });
                },
                pollInterval = setInterval(function () {
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
            let application_token = document.getElementById("application_token") ? document.getElementById("application_token").value : "",
                application_hash = document.getElementById("application_hash") ? document.getElementById("application_hash").value : "";

            checkStatusAndRedirect(application_token, application_hash);
        }, 2500);
    </script>
@endsection
