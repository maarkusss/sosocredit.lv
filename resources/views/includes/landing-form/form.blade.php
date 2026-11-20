@php
    /** @var array $applicant_form_types */
@endphp

<div class="w-full" id="landing_form">
    @csrf

    <input type="hidden" id="locale" name="locale" value="{{ app()->getLocale() }}" />

    <div class="landing-form__steps">
        <input id="locale"
               type="hidden"
               value="{{ app()->getLocale() }}" />

        @if (!empty($landing_form_version) && $landing_form_version === 2)
            <div class="flex items-center justify-start hidden mb-4">
                <button
                    class="landing-form__back-button"
                    id="back_button_v2"
                    type="button">
                    <svg class="w-4 h-4 mr-1"
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
                </button>
            </div>

            {{-- Amount slider && Credentials form --}}
            @include('includes.landing-form.step-1-and-2')
        @else

            <div class="flex items-center justify-start hidden mb-4">
                <button
                    class="landing-form__back-button"
                    id="back_button"
                    type="button">
                    <svg class="w-4 h-4 mr-1"
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
                </button>
            </div>
            {{-- Amount slider --}}
            @include('includes.landing-form.step-1')
            {{-- Credentials form --}}
            @include('includes.landing-form.step-2')
        @endif

        {{-- Result --}}
        @if (config('landing_form.require_phone'))
            @include('includes.landing-form.step-3-phone', [
                'hide_steps' => empty($landing_form_version) || $landing_form_version === 2
            ])
        @elseif (config('landing_form.require_email'))
            @include('includes.landing-form.step-3-email', [
                'hide_steps' => empty($landing_form_version) || $landing_form_version === 2
            ])
        @endif
    </div>
    <p class="landing-form__footer">@lang('main.micro_credit_slogan_rules')</p>
</div>
