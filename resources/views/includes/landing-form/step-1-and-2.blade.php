<div class=""
     id="landing_form_step_2">
    <div class="flex flex-col items-center justify-start mb-8">
        <span class="text-xl font-normal leading-none text-gray-800">@lang('main.sum_required')</span>
        <div class="mt-4">
            <span class="ml-2 text-4xl font-semibold leading-none text-gray-800"
                  id="slider_loan_amount"></span>
            <span class="ml-1 text-3xl font-normal leading-none text-gray-800">{{ currency_symbol() }}</span>
        </div>
    </div>
    <div class="flex items-center">
        <button class="flex items-center justify-center flex-shrink-0 w-6 h-6 text-base font-bold leading-none text-white border rounded-full appearance-none select-none border-primary-normal bg-primary-normal focus:outline-none focus:shadow-outline"
                id="minusButton"
                type="button"
                aria-label="decrement amount"
                style="touch-action: manipulation;">
            <svg class="w-4 h-4"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="3"
                      d="M20 12H4" />
            </svg>
        </button>
        <div class="w-full mx-2 my-4"
             id="amountSlider"></div>
        <button class="flex items-center justify-center flex-shrink-0 w-6 h-6 text-base font-bold leading-none text-white border rounded-full appearance-none select-none border-primary-normal bg-primary-normal focus:outline-none focus:shadow-outline"
                id="plusButton"
                type="button"
                aria-label="increment amount"
                style="touch-action: manipulation;">
            <svg class="w-4 h-4"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="3"
                      d="M12 4v16m8-8H4" />
            </svg>
        </button>
    </div>
    <div class="flex items-center justify-between">
        <input type="hidden"
               id="term"
               name="term">
        <span class="text-base leading-none text-gray-800 font-regular">50 {{ currency_symbol() }}</span>
        <span class="text-base leading-none text-gray-800 font-regular">25000 {{ currency_symbol() }}</span>
    </div>
    <div class="flex flex-row items-center justify-between w-full text-sm mt-6 text-gray-800 sm:w-auto sm:justify-center">
        <span>@lang('main.percents')</span>
        <span class="ml-1">
            <span id="slider_percentage_amount">3</span> {{ currency_symbol() }}
        </span>
    </div>
    <div class="flex flex-row items-center justify-between w-full text-sm mt-1 text-gray-800 sm:w-auto sm:justify-center">
        <span>@lang('main.total_to_pay')</span>
        <span class="ml-1 font-semibold text-primary-normal">
            <span id="slider_final_amount">1003</span> {{ currency_symbol() }}
        </span>
    </div>

    <p class="my-4 text-sm text-center text-gray-600">
        @if (config('landing_form.require_phone'))
            @lang('main.new_customers_confirm_phone_number')
        @else
            @lang('main.new_customers_confirm_email')
        @endif

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

        <div class="hidden text-primary-dark py-3 px-4 rounded" id="registration_form_errors"></div>

        @if (config('landing_form.require_email'))
            <label>
                <input class="w-full px-5 py-4 bg-white border-gray-200 rounded focus:border-primary-dark focus:ring-primary-dark"
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
                <input class="w-full px-5 py-4 bg-white border-solid border-gray-200 rounded focus:border-primary-dark focus:ring-primary-dark applyPhoneMask"
                       id="phone"
                       name="phone"
                       type="text"
                       inputmode="tel"
                       placeholder="+{{ phone_extension() }}{{ config('landing_form.phone_placeholder') }}"
                       required>
            </label>
        @endif

        <label class="text-sm font-normal text-center text-gray-600">
            <input class="mr-1 text-base border-gray-300 rounded focus:ring-primary-dark text-primary-normal"
                   x-model="rightsOfUsageChecked"
                   name="terms"
                   value="1"
                   type="checkbox"
                   required>
            @lang('main.i_acquainted')
            <span class="underline cursor-pointer text-primary-normal hover:text-primary-dark"
                  @click="rightsOfUsageModalClosed = false"
                  onclick="event.preventDefault();">
                @lang('main.with_information_rules')
            </span>
            @lang('main.and_confirm_i_agree_with_personal_data_processing')
        </label>

        @if (config('landing_form.show_optional_subscribe'))
            <label class="text-sm font-normal text-center text-gray-600">
                <input class="mr-1 text-base border-gray-300 rounded focus:ring-primary-dark text-primary-normal"
                       name="subscribe[]"
                       id="subscribe"
                       type="checkbox"
                       x-model="recieveOffersChecked"
                       value="true">
                @lang('main.i_agree')
                <span class="underline cursor-pointer text-primary-normal hover:text-primary-dark"
                      @click="recieveOffersModalClosed = false"
                      onclick="event.preventDefault();">
                    @lang('main.to_receive_offers')
                </span>
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
