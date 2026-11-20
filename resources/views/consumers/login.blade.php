@extends('layouts.public', ['meta_robots' => 'noindex, follow'])

@section('content')
    @include('includes.modals.applicant_form_privacypolicymodal')
    @include('includes.modals.applicant_form_recieveoffersmodal')
    <main>
        <section class="flex justify-center w-full px-3 bg-loan-normal" id="slogan-top" style="height: 1px;"></section>
        <section class="py-5 section sm:py-8">
            <div class="section__content">
                <div class="flex flex-col items-center justify-center">
                    <div class="flex flex-col flex-shrink-0 w-full max-w-screen-md min-h-96">
                        <h1 class="mb-2 text-xl font-bold text-neutral-900 text-start md:text-center">{{ __('Login') }}
                        </h1>
                        <div
                            class="w-full p-6 bg-white rounded-lg shadow-[0px_4px_8px_0px_rgba(0,0,0,0.10)] sm:p-8 sm:pb-10">
                            <div class="max-w-2xl mx-auto">
                                @include('admincp.includes.infobox')
                                <x-auth-session-status class="mb-4" :status="session('status')" />

                                <!-- Validation Errors -->
                                <x-auth-validation-errors class="mb-4" :errors="$errors" />

                                <div class="applicant-form">
                                    <form id="login_form" method="POST" action="{{ route('consumers.login') }}">
                                        @csrf

                                        <div class="grid grid-cols-1 gap-2 md:py-3">
                                            <label for="phone">@lang('Phone')<sup>*</sup></label>
                                            <div class="flex flex-col gap-4 applicant-form__input-wrap md:flex-row">
                                                <input class="applicant-form__input applyPhoneMask" type="text"
                                                    name="phone" id="phone" placeholder="@lang('Your Phone number')"
                                                    inputmode="tel" autocomplete="off" required />
                                                <button type="submit" id="submit_button"
                                                    class="flex flex-row items-center justify-center text-lg applicant-form__button disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <div class="grid w-full grid-cols-2">
                                                        <div></div>
                                                        <div class="flex justify-between -ml-8">
                                                            @lang('Log in')
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
                                                                class="w-5 h-5 mt-1">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    @include('includes.loading-spinner')
                                                </button>
                                            </div>

                                            {{-- Checkboxes --}}
                                            <div class="flex flex-col justify-start">
                                                <div class="text-left applicant-form__checkbox-wrap">
                                                    <input
                                                        class="mr-1 text-base border-gray-300 rounded text-loan-normal focus:ring-loan-dark"
                                                        name="agree_to_terms" id="agree_to_terms"
                                                        x-model="privacyPolicyChecked" value="1" type="checkbox"
                                                        required />
                                                    <label class="text-sm font-normal text-left text-gray-600"
                                                           for="agree_to_terms">
                                                        @lang('main.i_am_infromed')
                                                        <span
                                                            class="underline cursor-pointer text-loan-normal hover:text-loan-dark"
                                                            @click="privacyPolicyModalClosed = false"
                                                            onclick="event.preventDefault();">
                                                                @lang('applicant_form.with_data_processing_rules')</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="flex flex-col justify-start">
                                                <div class="text-left applicant-form__checkbox-wrap">
                                                    <input
                                                        class="mr-1 text-base border-gray-300 rounded text-loan-normal focus:ring-loan-dark"
                                                        name="subscribe" id="subscribe"
                                                        x-model="recieveOffersChecked" value="1"
                                                        type="checkbox" />
                                                    <label class="text-sm font-normal text-left text-gray-600"
                                                           for="subscribe">
                                                        @lang('main.i_agree')
                                                        <span
                                                            class="underline cursor-pointer text-loan-normal hover:text-loan-dark"
                                                            @click="recieveOffersModalClosed = false"
                                                            onclick="event.preventDefault();">
                                                                @lang('main.to_receive_marketing_offers')</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="justify-start">
                                                <span
                                                    class="text-sm font-medium leading-tight text-neutral-900">@lang('auth.login_disclaimer')</span>
                                                <a href="{{ route('home.index', ['locale' => app()->getLocale()]) }}"
                                                    class="link_primary text-[#3FAF4F] text-sm">@lang('auth.login_disclaimer_link')</a><span
                                                    class="text-neutral-900">.</span>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="hidden md:block h-60"></div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
@section('javascript')
    <script>
        const loginForm = document.getElementById('login_form'),
            submitButton = document.getElementById("submit_button"),
            loadingSpinner = loginForm.querySelector(".loading-spinner");

        if (loginForm && submitButton) {
            loginForm.addEventListener('submit', (e) => {
                if (submitButton) {
                    submitButton.disabled = true;
                }

                if (loadingSpinner) {
                    loadingSpinner.classList.remove("hidden");
                }

                return true;
            });
        }
    </script>
@endsection
