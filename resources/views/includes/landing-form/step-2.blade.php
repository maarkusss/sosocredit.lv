<div class="grid hidden grid-cols-1 gap-2"
     id="landing_form_step_2">
    {{-- Steps --}}
    <div class="w-full max-w-screen-md mb-8">
        <div class="landing-form__breadcrumbs">
            <div class="landing-form__breadcrumb">
                <span class="landing-form__breadcrumb__title_active">@lang('main.sum')</span>
                <div class="landing-form__breadcrumb__dot_active"></div>
            </div>
            <div class="landing-form__breadcrumb">
                <span class="landing-form__breadcrumb__title_active">@lang('main.information')</span>
                <div class="landing-form__breadcrumb__dot_active"></div>
            </div>
            <div class="landing-form__breadcrumb">
                <span class="landing-form__breadcrumb__title_inactive">@lang('main.result')</span>
                <div class="landing-form__breadcrumb__dot_inactive"></div>
            </div>
        </div>
        <div class="landing-form__breadcrumb__bgline">
            <div class="h-full rounded-full bg_primary"
                 style="width: 50%;"></div>
        </div>
    </div>
    <span class="mb-2 text-2xl font-semibold leading-none text-center text-gray-800">
        @if (config('landing_form.require_phone'))
            @lang('main.please_input_your_phone_number')
        @else
            @lang('main.please_input_your_email')
        @endif
    </span>
    <p class="text-sm text-center text-gray-600">
        @if (config('landing_form.require_phone'))
            @lang('main.new_customers_confirm_phone_number')
        @else
            @lang('main.new_customers_confirm_email')
        @endif
    </p>
    <p class="mb-4 text-sm text-center text-gray-600">
        @if (config('landing_form.require_phone'))
            @lang('main.existing_customer_provide_phone_number')
        @else
            @lang('main.existing_customer_provide_email')
        @endif
    </p>
    <form class="grid grid-cols-1 gap-4"
          id="registration_form"
          method="POST"
          action="{{ route('landing.form.post') }}">
        <input type="hidden"
               id="amount"
               value="0"
               name="amount">

        <div class="hidden text_primary py-3 px-4 rounded" id="registration_form_errors"></div>

        @if (config('landing_form.require_email'))
            <label>
                <input class="w-full px-5 py-4 bg-white border-gray-200 rounded border_primary_dark ring_primary_dark"
                       id="email"
                       name="email"
                       type="email"
                       inputmode="email"
                       autocomplete="email"
                       placeholder="@lang('main.your_email_address')"
                       required>
            </label>
        @endif

        @if (config('landing_form.require_phone'))
            <label>
                <input class="w-full px-5 py-4 bg-white border-solid border-gray-200 rounded border_primary_dark ring_primary_dark applyPhoneMask"
                       id="phone"
                       name="phone"
                       type="text"
                       inputmode="tel"
                       placeholder="+{{ phone_extension() }}{{ config('landing_form.phone_placeholder') }}"
                       required>
            </label>
        @endif

        <label class="text-sm font-normal text-center text-gray-600">
            <input class="mr-1 text-base border-gray-300 rounded ring_primary_dark text_primary"
                   x-model="rightsOfUsageChecked"
                   name="terms"
                   value="1"
                   type="checkbox"
                   required>
            @lang('main.i_acquainted')
            <span class="underline cursor-pointer text_primary link_primary"
                  @click="rightsOfUsageModalClosed = false"
                  onclick="event.preventDefault();">@lang('main.with_information_rules')</span>
            @lang('main.and_confirm_i_agree_with_personal_data_processing')
        </label>

        @if (config('landing_form.show_optional_subscribe'))
            <label class="text-sm font-normal text-center text-gray-600">
                <input class="mr-1 text-base border-gray-300 rounded ring_primary_dark text_primary"
                       name="subscribe[]"
                       id="subscribe"
                       type="checkbox"
                       x-model="recieveOffersChecked"
                       value="true">
                @lang('main.i_agree')
                <span class="underline cursor-pointer text_primary link_primary"
                      @click="recieveOffersModalClosed = false"
                      onclick="event.preventDefault();">@lang('main.to_receive_offers')</span>
                @if (config('landing_form.require_phone'))
                    @lang('main.via_sms')
                @else
                    @lang('main.via_email')
                @endif
            </label>
        @endif

        <button class="landing-form__button disabled:opacity-50 disabled:cursor-not-allowed"
                id="slider_button_2"
                type="submit">
            @lang('main.continue')

            @include('includes.loading-spinner')
        </button>
    </form>
</div>
