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
        {{-- Phone --}}
        <div class="flex flex-col items-center justify-center p-4 border rounded bg-primary-normal bg-opacity-10 border-primary-dark">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-12 h-12 fill-current text-primary-dark"
                     xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 256 256">
                    <path fill="none"
                          d="M0 0h256v256H0z" />
                    <path opacity=".2"
                          d="M64 56h128v144H64z" />
                    <rect x="64"
                          y="24"
                          width="128"
                          height="208"
                          rx="16"
                          stroke-width="16"
                          stroke="currentColor"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          fill="none" />
                    <path fill="none"
                          stroke="currentColor"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="16"
                          d="M64 56h128M64 200h128" />
                </svg>
            </div>
            <div class="hidden"
                 id="phone_notification">
                <span class="block mt-4 text-xl font-medium text-center text-gray-800">
                    @lang('main.your_phone_number')
                    <span class="mb-4 text-2xl font-semibold text-center text-primary-dark"
                          id="phone_notification_phone"></span>
                </span>
                <span class="block text-xl font-medium text-center text-gray-800"
                      id="phone_notification_recurring_user">
                    @lang('main.thanks_your_phone_number_confirmed')
                </span>
                <span class="block mt-4 text-xl font-bold text-center text-gray-800">
                    @lang('main.we_will_reply_via_sms_within_couple_minutes')
                </span>
                <a class="block w-full px-5 py-4 mt-4 text-lg font-semibold text-center text-white transition-all duration-200 rounded disabled:opacity-50 disabled:cursor-not-allowed bg-primary-normal hover:bg-primary-dark focus:outline-none focus:shadow-outline"
                   href="/u/"
                   target="_blank">
                    @lang('main.continue')
                </a>
            </div>
            <div class="grid hidden grid-cols-1 gap-4"
                 id="phone_verification_container">
                <span class="block text-xl font-medium text-center text-gray-800">
                    @lang('main.to_your_phone_number')
                    <span class="font-semibold text-primary-normal"
                          id="phone_verification_phone"></span>
                    @lang('main.sent_message_with_verification_code_input_to_continue')
                </span>

                <form class="grid grid-cols-1 gap-4"
                      id="phone_verification_form"
                      method="POST"
                      action="#">
                    <label>
                        <input class="w-full px-4 py-3 bg-white text-black border-gray-200 rounded focus:border-primary-dark focus:ring-primary-dark"
                               id="phone_verification_input"
                               name="phone_verification"
                               type="text"
                               inputmode="numeric"
                               autocomplete="one-time-code"
                               pattern="[0-9]*"
                               minlength="6"
                               maxlength="6"
                               placeholder="@lang('main.verification_code')"
                               required>
                    </label>
                    <div class="hidden -mt-2"
                         id="phone_verification_alert">
                        <div class="px-2 py-1 text-sm text-center text-red-800 bg-red-300 border border-red-800 rounded">
                            <span>@lang('main.invalid_verification_code')!</span>
                        </div>
                    </div>

                    <div class="hidden text-primary-dark py-3 px-4 rounded" id="phone_form_errors"></div>

                    <div class="-mt-2">
                        <span class="text-sm text-gray-500"
                              id="phone_verification_send_verification_again_timer"></span>
                        <button class="text-sm text-primary-normal hover:text-primary-dark focus:outline-none disabled:text-gray-500"
                                id="phone_verification_send_verification_again"
                                type="button">
                            @lang('main.send_again')
                        </button>
                    </div>
                    <button class="landing-form__button"
                            id="confirm_phone_button"
                            type="submit">
                        @lang('main.confirm_phone_number')

                        @include('includes.loading-spinner')
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
