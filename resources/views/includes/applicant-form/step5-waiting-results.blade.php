@php
    /**
     * @var array $lenders
     * @var string $common_status
     * @var bool $are_all_rejected
     * @var bool $cs_deal_status
     * @var array $lenders_info
     */
@endphp


<div class="flex flex-col items-start justify-start w-full">

    @foreach ($lenders as $prefix => $lenderData)
        @php
            $lender = $lenderData['data'];
            $benefits = $lenders_info[$lender['name']]['data']['benefits'] ?? null;
            $requiresAdditionalFieldUpdate = false;

            $discount = $lender['discount'] ?? null;
            if ($discount && $discount['unit'] == 'percentage' && $discount['value'] == 100) {
                $benefit = __('Until') . ' ' . $discount['period'] . ' ' . __('months') . ' ' . __('discount percentage');
                $benefits[0] = $benefit;
            }

            if ($lender['status'] != 'Lender') {
                $lenderConfig = $lender['lender_config'];
                $requiresAdditionalFieldUpdate = $lender['requires_additional_field_update'];
            }
        @endphp

        @if(($cs_deal_status == 'processing' && in_array($lender['status'], ['Lender', 'Done'])) || ! $cs_deal_status)
            <div
                class="flex flex-col md:flex-row justify-between md:items-center px-0 md:px-2 py-6 my-1 rounded-lg w-full min-h-[100px]
            {{ in_array($lender['status'], ['Approved', 'Done']) ? 'bg-[#EAF6ED]' : 'bg-neutral-100' }}">
                <div class="flex flex-col items-center justify-start md:flex-row">
                    <div class="flex flex-col items-center justify-start sm:w-48">
                        <div class="w-32 text-center lg:w-36">
                            @if (!empty($lender['link']))
                                <a href="{{ $lender['link'] }}" target="_blank">
                                    <img src="{{ $lenders_info[$lender['name']]['image'] }}" alt="" />
                                </a>
                            @else
                                <img src="{{ $lenders_info[$lender['name']]['image'] }}" alt="" />
                            @endif
                        </div>

                        @if (!empty($lenders_info[$lender['name']]['data']['slogan']))
                            <span
                                class="mt-2 px-2 py-1 text-xs font-bold leading-none shadow rounded-tl rounded text-neutral-900 bg-[#C6EECB]">
                            {{ $lenders_info[$lender['name']]['data']['slogan'] }}
                        </span>
                        @endif
                    </div>

                    @if (in_array($lender['status'], ['Registered', 'Waiting']))
                        <p class="mt-2 ml-0 text-sm text-left sm:ml-2 md:mt-0 md:text-center text-zinc-500">
                            @lang('The application is in review')
                        </p>
                    @endif
                </div>

                <div class="mt-4 md:mt-0">
                    @if (in_array($lender['status'], ['Cannot', 'PendingGC']))
                        <div
                            class="flex flex-row justify-center mx-4 mt-2 text-sm font-bold text-center md:mt-0 text-zinc-500">
                            @lang('The application does not meet the conditions of this lender')
                            <div data-tippy-content="@lang("Application is not compliant with lender's terms.")" data-tippy-theme="loan-info" class="hasTippy">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 ml-1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v .008H12v-.008Z" />
                                </svg>
                            </div>
                        </div>
                    @elseif (array_key_exists($lender['status'], $lenders_info[$lender['name']]['rejected_statuses']))
                        <div
                            class="flex flex-row justify-center mx-4 mt-2 text-sm font-bold text-center text-red-500 md:mt-0">
                            @lang('Loan is rejected')
                            <div data-tippy-content="@lang('Loan rejection reasons might be different.')" data-tippy-theme="loan-info" class="hasTippy">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor" class="w-5 h-5 ml-1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v .008H12v-.008Z" />
                                </svg>
                            </div>
                        </div>
                    @elseif (!empty($lender['link']) && in_array($lender['status'], ['Approved', 'Lender', 'Expired']))
                        <div class="items-center justify-center md:flex md:pr-4">
                            <div class="flex-col justify-between gap-6 md:grid md:grid-cols-1 lg:grid-cols-2">
                                @if (!empty($lender['details']['monthly_payment']))
                                    <div class="lg:min-w-[300px] mx-3 mt-2 md:mt-0 md:mx-auto md:mr-8 lg:mr-0">
                                        <div class="grid
                                        @if(isset($lender['details']['term']))
                                            grid-cols-2
                                        @else
                                            grid-cols-3
                                        @endif
                                        gap-1 md:gap-2 w-full text-sm md:text-base"
                                        >
                                            <div class="">
                                                <div class="text-xs text-gray-600">@lang('Sum')</div>
                                                <div class="font-bold" title="{{ __('Sum') }}">
                                                    {{ $lender['details']['max_amount'] ?? '' }} EUR
                                                </div>
                                            </div>
                                            @if(isset($lender['details']['term']))
                                                <div class="">
                                                    <div class="text-xs text-gray-600">@lang('Loan term (months)')</div>
                                                    <div class="font-bold" title="{{ __('Loan term (months)') }}">
                                                        {{ $lender['details']['term'] }} {{ __('months') }}
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="">
                                                <div class="text-xs text-gray-600">@lang('Mon. payment')</div>
                                                <div class="font-bold" title="{{ __('Monthly payment') }}">
                                                    {{ $lender['details']['monthly_payment'] }} EUR
                                                </div>
                                            </div>
                                            <div class="">
                                                <div class="text-xs text-gray-600">@lang('APR')</div>
                                                <div class="font-bold" title="{{ __('APR') }}">
                                                    {{ $lender['details']['gpl'] ? $lender['details']['gpl'] . '%' : '' }}
                                                </div>
                                            </div>
                                        </div>
                                        @if (isset($lender['alternates_to']))
                                            <div class="mt-2 text-sm bg-orange-100 rounded-full text-bold">
                                                @if ($lender['alternates_to'] == 'car')
                                                    {{ __('Can offer only car') }}
                                                @elseif($lender['alternates_to'] == 'installment')
                                                    {{ __('Can offer only installment') }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <div
                                    class="inline-flex flex-col items-start justify-start gap-1 py-3 md:py-0 md:justify-center md:gap-2">
                                    @if ($benefits)
                                        @foreach ($benefits as $benefit)
                                            <div
                                                class="inline-flex items-center justify-start gap-2 md:hidden lg:inline-flex">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="8" cy="8" r="8" fill="#297935" />
                                                    <path d="M4.8 8l1.6 1.6L10.4 5.6" stroke="white" stroke-width="1.5"
                                                          stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <div class="justify-start text-sm font-medium leading-tight text-green-950">
                                                    {{ $benefit }}</div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div class="flex-col {{ !empty($lender['expiration']) ? 'md:mt-4' : '' }}">
                                @php
                                    $isExpired = $lender['link'] === 'expired';
                                    $extraFields = $lenderConfig->additional_required_fields ?? [];
                                    $hasExtraFields = ! $isExpired && ! empty($extraFields);

                                    if ($hasExtraFields) {
                                        $path = parse_url($lender['link'], PHP_URL_PATH);
                                        $locale = app()->getLocale();
                                        $modalData = [
                                            'link'   => url(preg_replace('#^/api/#', '/' . $locale . '/', $path)),
                                            'fields' => $extraFields,
                                            'locale' => app()->getLocale()
                                        ];
                                    }
                                @endphp

                                <a href="{{ $isExpired ? '#' : $lender['link'] }}"
                                   target="{{ $isExpired ? '_self' : '_blank' }}"
                                   class="h-11 mx-4 md:mx-auto flex flex-row items-center justify-center text-center
                                        px-6 py-2.5 text-base font-medium rounded border
                                        {{ $isExpired
                                            ? 'text-zinc-200 bg-green-50 border-green-100 cursor-default'
                                            : 'text-white button_primary border-green-600'
                                        }}"
                                    @if($isExpired)
                                        onclick="return false;"
                                    @elseif($hasExtraFields && $requiresAdditionalFieldUpdate)
                                        x-data
                                        x-on:click.prevent="$dispatch('open-additional-fields', {{ json_encode($modalData) }})"
                                    @endif
                                >@lang('main.take_micro_credit')
                                </a>

                                @if (!empty($lender['expiration']) && $lender['link'] != 'expired')
                                    <p class="pt-2 text-xs font-normal text-zinc-500">
                                        @lang('Valid through') {{ date('Y-m-d H:i', $lender['expiration']) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @elseif(in_array($lender['status'], ['Done']))
                        <div class="inline-flex items-center justify-start gap-1 md:pr-12">
                            <div class="justify-start text-base font-bold leading-tight text-neutral-900">
                                {{ __('Thanks for cooperation') }}</div>
                        </div>
                    @else
                        <div class="inline-flex items-center justify-start gap-1 md:pr-12">
                            <div class="justify-start text-base font-bold leading-tight text-neutral-900">@lang('Waiting for response')
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    @endforeach
</div>

<style>
    .tippy-box[data-theme~='loan-info'] {
        background-color: #FFFFFF;
        color: #000000;
        display: flex;
        padding: 16px;
        justify-content: center;
        align-items: center;
        gap: 10px;
        align-self: stretch;
        border-radius: 4px;
        margin: 0 12px;
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.10);
    }

    .tippy-box[data-theme~='loan-info'] .tippy-arrow {
        color: #FFFFFF;
    }

    .tippy-box[data-theme~='loan-info'] .tippy-content {
        position: relative;
        z-index: 1;
    }

    /* Mobile styles */
    @media screen and (max-width: 768px) {
        .tippy-box[data-theme~='loan-info'] {
            padding: 12px;
            gap: 8px;
            max-width: calc(100vw - 32px);
            margin: 0 24px;
            font-size: 14px;
        }
    }

    @media screen and (max-width: 480px) {
        .tippy-box[data-theme~='loan-info'] {
            padding: 10px;
            gap: 6px;
            max-width: calc(100vw - 24px);
            margin: 0 18px;
            font-size: 13px;
            border-radius: 3px;
        }
    }

    /* Touch device optimizations */
    @media (hover: none) and (pointer: coarse) {
        .tippy-box[data-theme~='loan-info'] {
            min-height: 44px;
            /* Minimum touch target size */
            padding: 12px 16px;
        }
    }
</style>
