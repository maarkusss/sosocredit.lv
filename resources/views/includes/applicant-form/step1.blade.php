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

    $current_loantype = collect($applicant_form_types)
        ->filter(function ($array) use ($section) {
            return $array['id'] == $section->parent_section_id;
        })
        ->first();

    if (!empty($current_loantype)) {
        $is_business = !empty($current_loantype['is_business']);
        $is_mortgage = !empty($current_loantype['is_mortgage']);
        $is_car_mortgage = !empty($current_loantype['is_car_mortgage']);
        $loantype = $current_loantype['name'];
    }

    $hasLoginConsumers = collect(session()->all())
        ->keys()
        ->contains(fn($key) => str_contains($key, 'login_consumers'));
@endphp

@extends('layouts.public')

@section('content')
    <main>
        <section class="flex justify-center w-full px-3 bg-loan-normal" id="slogan-top" style="height: 1px;"></section>
        <section class="py-5 section_narrower md:section sm:py-8">
            <div class="lg:grid grid-cols-[2fr_800px_280px_2fr]">
                <div class="hidden lg:block"></div>
                <div class="section__content">
                    <div class="flex flex-col items-center justify-center">
                        @if (!empty($section->h1_description))
                            <div class="section__content__hero">
                                <div class="w-full mt-4 text-base leading-5 text-center">
                                    {{ $section->h1_description }}
                                </div>
                            </div>
                        @endif

                        <div class="landing-form-wrapper applicant-form">
                            <form action="" method="POST" class="w-full" id="applicant_form">
                                @csrf

                                <div class="mb-5">
                                    @include('includes.applicant-form._breadcrumbs', [
                                        'step' => 1,
                                        'hide_step4' => $is_business,
                                    ])
                                </div>

                                <input type="hidden" name="section_id" value="{{ $section_id ?? '' }}" />
                                <input type="hidden" name="loantype" value="{{ $loantype ?? '' }}" />
                                <input type="hidden" id="locale" name="locale" value="{{ app()->getLocale() }}" />

                                <div class="landing-form__steps">
                                    @include('admincp.includes.infobox')

                                    <div class="pb-2 applicant-form__wrapper md:px-4">
                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            {{-- Applicant inputs --}}
                                            <div class="grid grid-cols-1 gap-4 md:py-5">
                                                <div
                                                    class="p-3 md:p-6 bg-[#EAF6ED] rounded-lg inline-flex flex-col justify-start items-start gap-6">
                                                    <div
                                                        class="justify-start text-[#3FAF4F] text-base font-bold leading-tight">
                                                        @lang('applicant_form.all_that_is_required')</div>
                                                    <div class="flex flex-col items-start justify-start gap-4">
                                                        <div class="inline-flex items-center justify-start gap-3">
                                                            <svg class="flex-shrink-0" width="32" height="32"
                                                                viewBox="0 0 32 32" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M9.97522 17.5729C10.8425 17.5729 11.5482 16.8673 11.5482 16C11.5482 15.1327 10.8425 14.4271 9.97522 14.4271C9.10791 14.4271 8.40234 15.1327 8.40234 16C8.40234 16.8673 9.10791 17.5729 9.97522 17.5729Z"
                                                                    fill="#3FAF4F" />
                                                                <path
                                                                    d="M9.97482 19.4479C8.60369 19.4479 7.43907 20.3487 7.0415 21.5896H12.9081C12.5106 20.3487 11.3459 19.4479 9.97482 19.4479Z"
                                                                    fill="#3FAF4F" />
                                                                <path
                                                                    d="M29.0541 4.51874H2.94587C1.29981 4.51874 0 5.85561 0 7.46461V24.5354C0 26.1814 1.33687 27.4812 2.94587 27.4812H29.0541C30.7002 27.4812 32 26.1444 32 24.5354V7.46461C32 5.81849 30.6631 4.51874 29.0541 4.51874ZM13.9916 23.4646H5.95831C5.44056 23.4646 5.02081 23.0449 5.02081 22.5271C5.02081 20.7362 5.97606 19.1643 7.40375 18.2939C6.85906 17.684 6.52712 16.8802 6.52712 16.0001C6.52712 14.0989 8.07381 12.5521 9.975 12.5521C11.8762 12.5521 13.4229 14.0989 13.4229 16.0001C13.4229 16.8802 13.091 17.6841 12.5463 18.294C13.9739 19.1644 14.9292 20.7363 14.9292 22.5272C14.9291 23.0449 14.5094 23.4646 13.9916 23.4646ZM24.0333 23.4646H19.0125C18.4948 23.4646 18.075 23.0449 18.075 22.5271C18.075 22.0094 18.4948 21.5896 19.0125 21.5896H24.0333C24.5511 21.5896 24.9708 22.0094 24.9708 22.5271C24.9708 23.0449 24.5511 23.4646 24.0333 23.4646ZM26.0417 19.4479H19.0125C18.4948 19.4479 18.075 19.0282 18.075 18.5104C18.075 17.9927 18.4948 17.5729 19.0125 17.5729H26.0417C26.5594 17.5729 26.9792 17.9927 26.9792 18.5104C26.9792 19.0282 26.5594 19.4479 26.0417 19.4479ZM26.0417 15.4312H19.0125C18.4948 15.4312 18.075 15.0115 18.075 14.4937C18.075 13.976 18.4948 13.5562 19.0125 13.5562H26.0417C26.5594 13.5562 26.9792 13.976 26.9792 14.4937C26.9792 15.0115 26.5594 15.4312 26.0417 15.4312ZM26.0417 10.4104H5.95831C5.44056 10.4104 5.02081 9.99061 5.02081 9.47286C5.02081 8.95511 5.44056 8.53536 5.95831 8.53536H26.0417C26.5594 8.53536 26.9792 8.95511 26.9792 9.47286C26.9792 9.99061 26.5594 10.4104 26.0417 10.4104Z"
                                                                    fill="#3FAF4F" />
                                                            </svg>
                                                            <div
                                                                class="justify-start text-sm font-medium leading-tight text-neutral-900">
                                                                @lang('applicant_form.req_age')</div>
                                                        </div>
                                                        <div class="inline-flex items-center justify-start gap-3">
                                                            <svg class="flex-shrink-0" width="32" height="32"
                                                                viewBox="0 0 32 32" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M6 31H16C17.3261 31 18.5979 30.4732 19.5355 29.5355C20.4732 28.5979 21 27.3261 21 26V21C21 20.7348 20.8946 20.4804 20.7071 20.2929C20.5196 20.1054 20.2652 20 20 20C19.7348 20 19.4804 20.1054 19.2929 20.2929C19.1054 20.4804 19 20.7348 19 21V23H3V6C3.00237 5.20508 3.31921 4.4434 3.8813 3.8813C4.4434 3.31921 5.20508 3.00237 6 3H10C10.2652 3 10.5196 2.89464 10.7071 2.70711C10.8946 2.51957 11 2.26522 11 2C11 1.73478 10.8946 1.48043 10.7071 1.29289C10.5196 1.10536 10.2652 1 10 1H6C5.34339 1 4.69321 1.12933 4.08658 1.3806C3.47995 1.63188 2.92876 2.00017 2.46447 2.46447C1.52678 3.40215 1 4.67392 1 6V26C1 27.3261 1.52678 28.5979 2.46447 29.5355C2.92876 29.9998 3.47995 30.3681 4.08658 30.6194C4.69321 30.8707 5.34339 31 6 31ZM11 26C11.1978 26 11.3911 26.0586 11.5556 26.1685C11.72 26.2784 11.8482 26.4346 11.9239 26.6173C11.9996 26.8 12.0194 27.0011 11.9808 27.1951C11.9422 27.3891 11.847 27.5673 11.7071 27.7071C11.5673 27.847 11.3891 27.9422 11.1951 27.9808C11.0011 28.0194 10.8 27.9996 10.6173 27.9239C10.4346 27.8482 10.2784 27.72 10.1685 27.5556C10.0586 27.3911 10 27.1978 10 27C10 26.7348 10.1054 26.4804 10.2929 26.2929C10.4804 26.1054 10.7348 26 11 26Z"
                                                                    fill="#3FAF4F" />
                                                                <path
                                                                    d="M16.0001 1C15.3066 1.00019 14.6347 1.2406 14.0985 1.68034C13.5623 2.12009 13.195 2.732 13.0591 3.412L22.0001 7.882L30.9411 3.412C30.8052 2.732 30.4379 2.12009 29.9017 1.68034C29.3655 1.2406 28.6935 1.00019 28.0001 1H16.0001Z"
                                                                    fill="#3FAF4F" />
                                                                <path
                                                                    d="M21.553 9.9L13 5.618V12C13 12.7956 13.3161 13.5587 13.8787 14.1213C14.4413 14.6839 15.2044 15 16 15H28C28.7956 15 29.5587 14.6839 30.1213 14.1213C30.6839 13.5587 31 12.7956 31 12V5.618L22.447 9.9C22.308 9.96883 22.1551 10.0046 22 10.0046C21.8449 10.0046 21.692 9.96883 21.553 9.9Z"
                                                                    fill="#3FAF4F" />
                                                            </svg>
                                                            <div
                                                                class="justify-start text-sm font-medium leading-tight text-neutral-900">
                                                                @lang('applicant_form.req_phone_email')</div>
                                                        </div>
                                                        <div class="inline-flex items-center justify-start gap-3">
                                                            <svg class="flex-shrink-0" width="32" height="32"
                                                                viewBox="0 0 32 32" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M30.3289 2.14513H11.3949C10.4907 2.14513 9.75506 2.882 9.75506 3.78694V14.4144H10.6536H11.5861C11.6855 14.4144 11.7817 14.4478 11.8599 14.508C12.6591 15.1243 13.4581 15.7406 14.2566 16.3563C14.6446 16.6558 15.0326 16.9547 15.4206 17.2542H30.3289C31.2331 17.2542 31.9688 16.5179 31.9688 15.6124V3.78694C31.9688 2.882 31.2331 2.14513 30.3289 2.14513ZM17.4054 14.3688H13.0656V13.4709H17.4055V14.3688H17.4054ZM22.0429 11.8183H13.0656V10.9204H22.0429V11.8183ZM31.0709 7.91513H10.6536V5.523H31.0709V7.91513ZM0.03125 13.478H4.745V26.3494H0.03125V13.478ZM30.8503 21.9025C31.1081 22.4335 31.1427 23.0358 30.9471 23.5988C30.7521 24.1619 30.3519 24.6134 29.8202 24.8712C28.4465 25.5369 27.2319 26.141 26.1608 26.6746C21.8145 28.8372 19.6328 29.9211 17.4618 29.8518C15.28 29.7812 12.9437 28.5466 8.28956 26.0865C7.49625 25.6671 6.60481 25.1963 5.64281 24.6936V15.3129H11.4327C12.6795 16.2743 13.9256 17.2356 15.1723 18.1969C16.4588 19.1897 17.7453 20.1825 19.0324 21.1746C19.8136 21.7781 20.0945 22.8113 19.7539 23.7162L19.7379 23.7573C19.7097 23.8284 19.6776 23.8983 19.6417 23.9669C19.5827 24.0804 19.5121 24.1888 19.4326 24.2921C18.6855 25.2618 17.2874 25.442 16.3158 24.6943L10.9209 20.5314L10.3726 21.242L15.7674 25.4055C16.335 25.8423 17.0052 26.0533 17.6709 26.0533C18.6059 26.0533 19.5307 25.6364 20.1432 24.8411C20.2234 24.7373 20.2971 24.6288 20.3625 24.5166L27.8814 20.8719C28.4124 20.6141 29.0153 20.5801 29.5777 20.7751C30.1409 20.9707 30.5931 21.3709 30.8503 21.9025Z"
                                                                    fill="#3FAF4F" />
                                                            </svg>
                                                            <div
                                                                class="justify-start text-sm font-medium leading-tight text-neutral-900">
                                                                @lang('applicant_form.req_bank_account')</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col gap-2 py-5">

                                                <div class="applicant-form__input-wrap">
                                                    <label for="email">@lang('E-mail')<sup>*</sup></label>
                                                    <input class="applicant-form__input applyEmailMask" name="email"
                                                        id="step0_email" type="email" inputmode="email" autocomplete="email"
                                                        placeholder="@lang('Your E-mail')"
                                                        value="{{ old('email', $application ? $application->email : '') }}"
                                                        @if ($hasLoginConsumers) readonly @endif required />
                                                </div>

                                                @if (!$hasLoginConsumers)
                                                    <div class="applicant-form__input-wrap">
                                                        <label for="password">@lang('Parole')<sup>*</sup></label>
                                                        <input class="applicant-form__input" name="password"
                                                            id="step0_password" type="password" autocomplete="new-password"
                                                            placeholder="••••••••" required />
                                                    </div>
                                                @endif
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
                                                        <input type="hidden" name="subscribed_email" value="0">
                                                        <input
                                                            class="mr-1 text-base border-gray-300 rounded text-loan-normal focus:ring-loan-dark"
                                                            name="subscribed_email" id="subscribed_email"
                                                            x-model="subscribeEmailChecked" value="1"
                                                            type="checkbox" />
                                                        <label class="text-sm font-normal text-left text-gray-600"
                                                            for="subscribed_email">
                                                            @lang('Piekrītu saņemt jaunumus un aizdevēju piedāvājumus e-pastā')
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="flex flex-col justify-start">
                                                    <div class="text-left applicant-form__checkbox-wrap">
                                                        <input type="hidden" name="subscribed_sms" value="0">
                                                        <input
                                                            class="mr-1 text-base border-gray-300 rounded text-loan-normal focus:ring-loan-dark"
                                                            name="subscribed_sms" id="subscribed_sms"
                                                            x-model="subscribeSmsChecked" value="1"
                                                            type="checkbox" />
                                                        <label class="text-sm font-normal text-left text-gray-600"
                                                            for="subscribed_sms">
                                                            @lang('Piekrītu saņemt jaunumus un aizdevēju piedāvājumus telefonā (SMS)')
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="items-center justify-center hidden grid-cols-1 gap-2 md:grid md:grid-cols-2 md:gap-4">
                                            @if ($step < Application::MAX_STEPS && !empty($go_back_link))
                                                {{-- Go back button --}}
                                                <a href="{{ $go_back_link }}"
                                                    class="flex order-2 px-5 md:order-1 applicant-form__back-button"
                                                    id="back_button" type="button">
                                                    <div class="w-full grid grid-cols-[1fr_10fr_1fr]">
                                                        <svg class="h-5 mt-1 mr-2" xmlns="http://www.w3.org/2000/svg"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 19l-7-7 7-7" />
                                                        </svg>
                                                        @lang('main.back')
                                                    </div>
                                                </a>

                                                {{-- Submit button --}}
                                                <button id="submit_button" type="submit"
                                                    class="flex flex-row items-center justify-center order-1 text-lg applicant-form__button md:order-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <div class="w-full grid grid-cols-[1fr_10fr_1fr]">
                                                        <div></div>
                                                        @lang('Continue')
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="h-5 mt-1 ml-2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                                        </svg>
                                                    </div>

                                                    @include('includes.loading-spinner')
                                                </button>
                                            @else
                                                {{-- Submit button --}}
                                                <button id="submit_button" type="submit"
                                                    class="flex flex-row items-center justify-center order-1 text-lg applicant-form__button md:order-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                                    @lang('Continue')
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
        {{--        Sticky footer nav buttons --}}
        @include('includes.applicant-form._sticky_footer')
    </main>
@endsection
