<div class="grid hidden grid-cols-1 gap-2"
     id="landing_form_step_3">

    @if (empty($hide_steps))
    {{-- Steps --}}
    <div class="w-full max-w-screen-md mb-8">
        <div class="landing-form__breadcrumbs">
            <div class="landing-form__breadcrumb">
                <span class="text-sm font-semibold leading-none text-primary-normal">@lang('main.sum')</span>
                <div class="landing-form__breadcrumb__dot_active"></div>
            </div>
            <div class="landing-form__breadcrumb">
                <span class="text-sm font-semibold leading-none text-primary-normal">@lang('main.information')</span>
                <div class="landing-form__breadcrumb__dot_active"></div>
            </div>
            <div class="landing-form__breadcrumb">
                <span class="text-sm font-semibold leading-none text-primary-normal">@lang('main.result')</span>
                <div class="landing-form__breadcrumb__dot_active"></div>
            </div>
        </div>
        <div class="landing-form__breadcrumb__bgline">
            <div class="h-full rounded-full bg-primary-normal"
                 style="width: 100%;"></div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-4">
        {{-- E-mail --}}
        <div class="flex flex-col items-center justify-center p-4 border rounded bg-primary-normal bg-opacity-10 border-primary-dark">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 fill-current text-primary-dark"
                     xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 256 256">
                    <path fill="none"
                          d="M0 0h256v256H0z" />
                    <path opacity=".2"
                          d="m224.006 56-96 88-96-88h192z" />
                    <path fill="none"
                          stroke="currentColor"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="16"
                          d="m224 56-96 88-96-88" />
                    <path d="M32 56h192v136a8 8 0 0 1-8 8H40a8 8 0 0 1-8-8V56h0ZM110.545 128l-76.078 69.739M221.534 197.739 145.454 128"
                          fill="none"
                          stroke="currentColor"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="16" />
                </svg>
            </div>
            <div class="hidden"
                 id="email_offer_notification">
                <span class="block text-xl font-medium text-center text-gray-800">
{{--                    Если вы желаете получать предложения по электронной почте, в заявке введите свой адрес почты!--}}
                </span>
            </div>
            <div class="hidden"
                 id="email_notification">
                <span class="block text-xl font-medium text-center text-gray-800">
                    @lang('main.you_will_receive_email_within_couple_minutes_check_spam')
                </span>
            </div>
            <div class="hidden"
                 id="email_verification_container">
                <span class="block text-xl font-medium text-center text-gray-800">
                    @lang('main.to_your_email_address') -
                    <span class="font-semibold text-primary-normal"
                          id="email_verification_email"></span>
                    @lang('main.sent_email_confirmation_link_confirm_to_continue')
                </span>
            </div>
        </div>
    </div>
</div>
