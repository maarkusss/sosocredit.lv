@if (empty($_GET['hide_types']) && !empty($applicant_form_types) && count($applicant_form_types) > 1)
    <div class="relative flex flex-col items-center justify-start bg-white border-b border-gray-200 rounded-t-lg">
        @include('includes.applicant-form._type_dropdown', [
            'current_type_id' => $section->id,
        ])
    </div>
@endif

<style>
    /* Hide the arrows (spinner) for number input */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
        /* For Firefox */
    }
</style>

<input type="hidden" id="locale" name="locale" value="{{ app()->getLocale() }}" />

<div class="w-full p-4 bg-white rounded-b-lg shadow-xl md:p-8 sm:p-8">
    @include('admincp.includes.infobox')

    <div class="applicant-form__input-wrap">
        @php
            $minAmount = collect($amounts)->min('key');
            $maxAmount = collect($amounts)->max('key');
            $amountStep = $amounts['step'];
            $selectedAmount = collect($amounts)->where('selected', true)->first()->key ?? $minAmount;
        @endphp

        <div class="flex items-center justify-between mb-3">
            <label for="amount_display" class="!text-base !font-semibold !text-gray-800 !m-0">
                @lang('main.sum_required')
            </label>
            <div class="relative flex items-center text-base font-medium text-gray-800">
                @if (config('app.currency_as_prefix') == true)
                    <span class="absolute left-3 text-[#7C8381]">{{ config('app.currency_symbol') }}</span>
                @endif

                <!-- Visible formatted display input -->
                <input type="text" id="amount_display" value="{{ currency_formatted($selectedAmount) }}"
                    class="block px-3 py-1.5 font-bold text-gray-800 border focus:ring-0 focus:border-primary-normal border-[#CDD7CF] rounded-lg"
                    @if (!empty($disable_term_months)) disabled @endif>

                <!-- Hidden real value -->
                <input type="hidden" name="amount" id="amount" value="{{ $selectedAmount }}">

                @if (config('app.currency_as_prefix') == false)
                    <span class="absolute right-3 text-[#7C8381]">{{ config('app.currency_symbol') }}</span>
                @endif
            </div>
        </div>

        <div class="mb-2">
            <div id="applicantAmountSlider" data-start="{{ $selectedAmount }}" data-min="{{ $minAmount }}"
                data-max="{{ $maxAmount }}" data-step="{{ $amountStep }}">
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="text-xs font-semibold text-zinc-500">{{ currency_formatted($minAmount) }}</div>
            <div class="text-xs font-semibold text-zinc-500">{{ currency_formatted($maxAmount) }}</div>
        </div>

        @php
            $minTermMonths = collect($term_months)->min('key');
            $maxTermMonths = collect($term_months)->max('key');
            $selectedTermMonths = collect($term_months)->where('selected', true)->first()->key ?? $minTermMonths;
        @endphp

        <input type="hidden" name="term_months" id="term_months" value="{{ $selectedTermMonths }}">

        <div class="mt-4 applicant-form__input-wrap">
            <div class="flex items-center justify-between mb-3">
                <label for="term_months_input" class="!text-base !font-semibold !text-gray-800 !m-0">
                    @lang('main.loan_term')
                </label>
                <div class="relative flex items-center text-base font-medium text-gray-800">
                    <input type="number" name="term_months_input" id="term_months_input"
                        value="{{ $selectedTermMonths }}" min="{{ $minTermMonths }}" max="{{ $maxTermMonths }}"
                        step="1"
                        class="block p-1.5 px-3 font-bold text-gray-800 border focus:ring-0 focus:border-primary-normal border-[#CDD7CF] rounded-lg"
                        @if (!empty($disable_term_months)) disabled @endif>
                    <span class="absolute right-3 text-[#7C8381]">@lang('main.month')</span>
                </div>
            </div>

            <div class="mb-2">
                <div id="applicantTermMonthsSlider" data-start="{{ $selectedTermMonths }}"
                    data-min="{{ $minTermMonths }}" data-max="{{ $maxTermMonths }}" data-step="1">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-xs font-semibold text-zinc-500">{{ $minTermMonths }} @lang('months')</div>
                <div class="text-xs font-semibold text-zinc-500">{{ $maxTermMonths }} @lang('months')</div>
            </div>
        </div>

        @include('includes.applicant-form.monthly-payment')

        <button type="submit" class="flex items-center justify-center mt-4 text-base applicant-form__button">
            @lang('Apply')
            @include('includes.loading-spinner')
        </button>
    </div>
</div>
<p class="landing-form__footer">
    @lang('applicant_form.unification_example') @lang('applicant_form.calculation_example')
</p>
