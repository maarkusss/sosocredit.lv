@php
    $loanTypes = [];
    foreach ($loan_modal_data as $loanTypeSection) {
        $loanTypes[] = [
            'id' => $loanTypeSection->id,
            'label' => $loanTypeSection->name,
            'routeName' => $loanTypeSection->route_name,
            'rate' => 28.74,
            'min' => $loanTypeSection->json['slider_min_amount'],
            'max' => $loanTypeSection->json['slider_max_amount'],
            'startAmount' => $loanTypeSection->json['slider_start_amount'],
            'stepAmount' => $loanTypeSection->json['slider_step_amount'],
            'minTerm' => $loanTypeSection->json['slider_min_term'],
            'maxTerm' => $loanTypeSection->json['slider_max_term'],
            'startTerm' => $loanTypeSection->json['slider_start_term'],
        ];
    }
@endphp

<div
    id="loan-sticky-modal"
    class="fixed right-5 bottom-5 w-80 max-w-[calc(100vw-40px)] z-[1050] animate-[loan-modal-in_0.25s_ease-out] bg-transparent pt-8"
    x-data="loanWidget(@js($loanTypes))"
    x-show="visible"
    x-cloak
>
    <button type="button"
            class="absolute w-8 h-8 top-6 right-[-6px] border-0 rounded-full shadow-[0_0_6px_rgba(0,0,0,0.1)] bg-white text-[22px] leading-none cursor-pointer z-30 "
            aria-label="Close" @click="hide()">&times;</button>

    <div class="bg-white rounded-xl shadow-[0_10px_30px_rgba(0,0,0,0.15)] m-2">

        <div class="mb-3">
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button type="button"
                        class="w-full flex items-center justify-between bg-green-700 text-white border-0 px-4 py-3 text-sm font-bold rounded-t-lg cursor-pointer"
                        @click="open = !open">
                    <span x-text="selectedType.label"></span>
                    <svg class="transition-transform duration-150 ease-in-out" :class="{ 'rotate-180': open }"
                         width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 5L7 9L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div class="absolute top-full left-0 right-0
                    bg-white border border-gray-200 border-t-0 rounded-b-lg shadow-[0_8px_20px_rgba(0,0,0,0.1)]
                    z-10 overflow-y-auto
                    @if(config('app.term_picker_enabled')) max-h-80 @else max-h-40 @endif"
                     x-show="open" x-transition>
                    <template x-for="type in loanTypes" :key="type.id">
                        <button type="button"
                                class="block w-full text-left px-4 py-2.5 bg-transparent border-0 border-b border-[#f0f0f0] last:border-b-0 text-sm font-bold cursor-pointer text-[#081B1A] hover:bg-[#EBF4EC]"
                                :class="{ 'text-green-700': type.id === loanTypeId }"
                                @click="loanTypeId = type.id; clampAmount(); clampTerm(); open = false"
                                x-text="type.label"
                        ></button>
                    </template>
                </div>
            </div>
        </div>

        <div class="m-3 mt-4">
            <div class="flex justify-between">
                <label class="flex text-sm font-bold mb-1 items-center">
                    @lang('main.sum_required')
                </label>
                <div class="relative flex items-center">
                    <input type="text"
                           :value="formatCurrency(amount)"
                           @change="amount = parseFloat($event.target.value.replace(/\s/g, '').replace(',', '.')) || amount; clampAmount()"
                           @blur="$event.target.value = formatCurrency(amount)"
                           class="w-[110px] block px-3 py-1.5 font-bold text-gray-800 border focus:ring-0 focus:border-primary-normal border-[#CDD7CF] rounded-lg">
                    <span class="absolute right-3 text-[#7C8381]">{{ config('app.currency_symbol') }}</span>
                </div>
            </div>

            <div class="relative h-5 flex items-center mt-2">
                <div class="absolute left-[10px] right-[10px] h-2 rounded-full bg-[#d7e2dd]"></div>
                <div class="absolute left-[10px] h-2 rounded-full bg-green-800 pointer-events-none" :style="`width: calc(${amountPercent}% - ${amountPercent * 0.16}px)`"></div>
                <input type="range" class="loan-range-input relative z-[2] w-full h-2 m-0 bg-transparent appearance-none cursor-pointer"
                       :min="selectedType.min" :max="selectedType.max"
                       :step="stepFor(selectedType.max)" x-model.number="amount">
            </div>
            <div class="flex justify-between text-[11px] text-gray-500 mt-0.5">
                <div><span x-text="formatCurrency(selectedType.min)"></span>&nbsp{{ config('app.currency_symbol') }}</div>
                <div><span x-text="formatCurrency(selectedType.max)"></span>&nbsp{{ config('app.currency_symbol') }}</div>
            </div>
        </div>

        @if (config('app.term_picker_enabled'))
            <div class="m-3">
                <div class="flex justify-between">
                    <label class="flex text-sm font-bold mb-1 items-center">
                        @lang('main.loan_term')
                    </label>
                    <div class="relative flex items-center">
                        <input type="text"
                               :value="term"
                               @change="term = parseInt($event.target.value) || term; clampTerm(); $event.target.value = term"
                               :min="selectedType.minTerm" :max="selectedType.maxTerm" step="1"
                               class="w-[90px] block p-1.5 px-3 font-bold text-gray-800 border focus:ring-0 focus:border-primary-normal border-[#CDD7CF] rounded-lg">
                        <span class="absolute right-3 text-[#7C8381]">@lang('main.month')</span>
                    </div>
                </div>
                <div class="relative h-5 flex items-center mt-2">
                    <div class="absolute left-[10px] right-[10px] h-2 rounded-full bg-[#d7e2dd]"></div>
                    <div class="absolute left-[10px] h-2 rounded-full bg-green-800 pointer-events-none" :style="`width: calc(${termPercent}% - ${termPercent * 0.20}px)`"></div>
                    <input type="range" class="loan-range-input relative z-[2] w-full h-2 m-0 bg-transparent appearance-none cursor-pointer"
                           :min="selectedType.minTerm" :max="selectedType.maxTerm" x-model.number="term">
                </div>
                <div class="flex justify-between text-[11px] text-gray-500 mt-0.5">
                    <div><span x-text="selectedType.minTerm"></span>&nbsp @lang('main.month')</div>
                    <div><span x-text="selectedType.maxTerm"></span>&nbsp @lang('main.month')</div>
                </div>
            </div>

            <div class="m-3 pt-3">
                <div class="flex justify-between">
                    <div>
                        <span class="text-sm text-stone mb-1 font-bold items-center">@lang('Monthly Payment'):</span>&nbsp
                        <span class="text-base font-medium leading-tight text-red-600">*</span>
                        <div class="justify-start w-[180px] text-[10px] font-normal leading-none text-stone-800">
                            @lang('Monthly Payment info')
                        </div>
                    </div>
                    <div class="px-2 py-1 bg-loan-normal/10 rounded-lg flex justify-center items-center gap-2.5">
                        <div class="flex justify-start text-xl font-bold leading-9 text-[#0E3A13]">
                            <strong x-text="formatCurrency(monthlyPayment)"></strong>&nbsp{{ config('app.currency_symbol') }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="m-2 pb-2">
            <button type="button"
                    class="flex items-center justify-center mt-2 text-base applicant-form__button"
                    @click="goToStep1">@lang('Apply')</button>
        </div>
    </div>
</div>
