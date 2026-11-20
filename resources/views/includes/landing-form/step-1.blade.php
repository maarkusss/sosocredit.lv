<div class=""
     id="landing_form_step_1">
    <div class="flex flex-col items-center justify-start mb-8">
        <span class="text-xl font-normal leading-none text-gray-800">@lang('main.sum_required')</span>
        <div class="mt-4">
            <span class="ml-2 text-4xl font-semibold leading-none text-gray-800"
                  id="slider_loan_amount"></span>
            <span class="ml-1 text-3xl font-normal leading-none text-gray-800">{{ currency_symbol() }}</span>
        </div>
    </div>
    <div class="flex items-center">
        <button class="flex items-center justify-center flex-shrink-0 w-6 h-6 text-base font-bold leading-none text-white border rounded-full appearance-none select-none border_primary bg_primary focus:outline-none focus:shadow-outline"
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
        <button class="flex items-center justify-center flex-shrink-0 w-6 h-6 text-base font-bold leading-none text-white border rounded-full appearance-none select-none border_primary bg_primary focus:outline-none focus:shadow-outline"
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
    <div class="flex flex-row items-center justify-between w-full text-sm mt-8 text-gray-800 sm:w-auto sm:text-lg sm:justify-center">
        <span>@lang('main.percents')</span>
        <span class="ml-1">
            <span id="slider_percentage_amount">3</span> {{ currency_symbol() }}
        </span>
    </div>
    <div class="flex flex-row items-center justify-between w-full text-sm mt-1 text-gray-800 sm:w-auto sm:text-lg sm:justify-center">
        <span>@lang('main.total_to_pay')</span>
        <span class="ml-1 font-semibold text_primary">
            <span id="slider_final_amount">1003</span> {{ currency_symbol() }}
        </span>
    </div>
    <button class="landing-form__button text-lg mt-8"
            id="slider_button_1"
            type="button">
        @lang('main.take_micro_credit')
    </button>
</div>
