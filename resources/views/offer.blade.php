@extends('layouts.public', ['canonical_href' => request()->url()])

@section('head')
    <meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
    <main class="flex items-start justify-center max-w-screen-xl px-4 py-2 mx-auto">
        <div class="flex flex-col items-center justify-center w-full text-center">
            <div class="flex flex-row flex-wrap my-2">
                {{-- Alerts --}}
                @if (request()->has('email_verified') && request()->input('email_verified') === 'true')
                    <div class="py-0.5 sm:px-1 xl:px-2 w-full">
                        <div class="mb-0 font-semibold text-center alert alert-success">
                            @lang('Thanks, your email address has been confirmed!')
                        </div>
                    </div>
                @endif

                @if (request()->has('phone_verified') && request()->input('phone_verified') === 'true')
                    <div class="py-0.5 sm:px-1 xl:px-2 w-full">
                        <div class="mb-0 font-semibold text-center alert alert-success">
                            @lang('Thanks, your phone number has been confirmed!')
                        </div>
                    </div>
                @endif

                <div class="w-full">
                    <p class="p-2 text-base font-semibold leading-tight text-gray-800 rounded sm:text-lg">
                        @lang('main.fill_enquiry_wait_and_get_money')
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap w-full -mx-1 sm:-mx-2">
                @foreach ($lenders as $position => $lender)
                    {{-- Loan box --}}
                    <div class="flex-shrink-0 w-full py-1 loan-container sm:p-2 sm:w-1/2 lg:w-1/3 xl:w-1/4">
                        <a onclick="logEvent('OfferOpened', {})"
                            href="{{ route('go', ['lender_id' => $lender->id, 'lender_position' => $position]) }}"
                            rel="nofollow" target="_blank"
                            class="relative pt-6 p-2 flex flex-col justify-between items-center h-full bg-white shadow rounded-lg hover:bg-gray-100 transition-all duration-200 @if ($loop->iteration <= 3) border border-green-500 hover:bg-green-100 @endif">
                            <div class="flex items-center justify-center w-full h-12 px-2 mb-4">
                                @if (!empty($lender->image))
                                    <img src="{{ $lender->image }}" alt="Lender {{ $lender->id }}"
                                        class="max-w-full max-h-full" />
                                @else
                                    {{ $lender->name }}
                                @endif
                            </div>
                            <div class="flex flex-col justify-between w-full h-full px-1 text-center">
                                @if ($loop->iteration < 4 || (!empty($lender->data) && !empty($lender->data->slogan)))
                                    <span
                                        class="absolute top-0 left-0 p-1 block text-sm font-semibold leading-none shadow rounded-tl rounded-br
                                        @if ($loop->iteration <= 3) text-white bg-green-500 @else text-gray-800 bg-gray-300 @endif">
                                        @if ($loop->iteration == 1)
                                            🥇
                                        @elseif($loop->iteration == 2)
                                            🥈
                                        @elseif($loop->iteration == 3)
                                            🥉
                                        @endif
                                        {{ $lender->data->slogan ?? '' }}
                                    </span>
                                @endif
                                <div class="flex flex-wrap">
                                    <span
                                        class="block w-7/12 mb-3 text-sm font-normal leading-none text-left text-gray-600">
                                        @lang('main.credit')
                                        <span class="block mt-1 mr-1 text-lg font-semibold leading-none text-gray-800">
                                            @if ($lender->zero_percent)
                                                <span class="text_primary">0%</span> {{ __('main.till') }}
                                            @else
                                                {{ __('main.till') }}
                                            @endif
                                            {{ $lender->first_loan }}€
                                        </span>
                                    </span>
                                    <span class="block w-5/12 text-sm font-normal leading-none text-left text-gray-600">
                                        @lang('main.term')
                                        <span class="block mt-1 text-lg font-semibold leading-none text-gray-800">
                                            @if ($lender->name != 'Bino.lv')
                                                @lang('main.till')
                                            @endif
                                            @if ($lender->max_term == 1 || ($lender->max_term > 20 && $lender->max_term % 10 == 1))
                                                {{ $lender->max_term }} @lang('main.day')
                                            @elseif(
                                                ($lender->max_term >= 2 && $lender->max_term <= 4) ||
                                                    ($lender->max_term > 20 && $lender->max_term % 10 == 2) ||
                                                    ($lender->max_term > 20 && $lender->max_term % 10 == 3) ||
                                                    ($lender->max_term > 20 && $lender->max_term % 10 == 4))
                                                {{ $lender->max_term }} @lang('main.days1')
                                            @elseif($lender->max_term >= 5 && $lender->max_term <= 90)
                                                {{ $lender->max_term }}
                                                @if ($lender->name != 'Bino.lv')
                                                    @lang('main.days2')
                                                @else
                                                    @lang('main.days3')
                                                @endif
                                            @elseif($lender->max_term < 360)
                                                {{ number_format($lender->max_term / 30, 0) }}
                                                @lang('main.month')
                                            @elseif($lender->max_term == 360)
                                                {{ number_format($lender->max_term / 360, 0) }} @lang('main.year')
                                            @elseif($lender->max_term == 720 || $lender->max_term == 1080 || $lender->max_term == 1440)
                                                {{ number_format($lender->max_term / 360, 0) }} @lang('main.years1')
                                            @else
                                                {{ number_format($lender->max_term / 360, 0) }} @lang('main.years2')
                                            @endif
                                        </span>
                                    </span>
                                    <span class="block w-1/2 text-sm font-normal leading-none text-left text-gray-600">
                                        @lang('main.age')
                                        <span class="block mt-1 text-lg font-semibold leading-none text-gray-800">
                                            {{ $lender->min_years }} @lang('main.till') {{ $lender->max_years }}
                                        </span>
                                    </span>
                                </div>
                                <button type="button"
                                    class="flex items-center justify-center w-full px-4 py-3 mt-4 text-base font-semibold leading-none text-center text-white transition-all duration-200 rounded-lg appearance-none focus:outline-none button_primary">
                                    @lang('main.take_micro_credit')
                                </button>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
    <section class="py-12">

        @if (!empty($loantype))
            <div class="max-w-screen-xl px-4 mx-auto">
                {{ $loantype->text }}
            </div>
        @else
            <div class="max-w-screen-xl px-4 mx-auto">
                <div class="relative text-left lg:px-2">
                    <h4 class="mt-4 text-lg font-semibold leading-none text-gray-800">@lang('offers.line1')</h4>
                    <p class="mt-2 text-base font-normal leading-tight text-gray-600">- @lang('offers.line2')</p>
                    <p class="mt-2 text-base font-normal leading-tight text-gray-600">- @lang('offers.line3')</p>
                    <p class="mt-2 text-base font-normal leading-tight text-gray-600">- @lang('offers.line4')</p>
                    <p class="mt-2 text-base font-normal leading-tight text-gray-600">- @lang('offers.line5')</p>
                    <p class="mt-2 text-base font-normal leading-tight text-gray-600">- @lang('offers.line6')</p>
                </div>

                <div class="relative text-left lg:px-2">
                    <h4 class="mt-4 text-lg font-semibold leading-none text-gray-800">@lang('offers.line7')</h4>
                    <p class="mt-2 text-base font-normal leading-tight text-gray-600">@lang('offers.line8')</p>
                </div>
                <div class="relative text-left lg:px-2">
                    <h4 class="mt-4 text-lg font-semibold leading-none text-gray-800">@lang('offers.line9')</h4>
                    <p class="mt-2 text-base font-normal leading-tight text-gray-600">@lang('offers.line10')</p>
                </div>
                <div class="relative text-left lg:px-2">
                    <h4 class="mt-4 text-lg font-semibold leading-none text-gray-800">@lang('offers.line11')</h4>
                    <p class="mt-2 text-base font-normal leading-tight text-gray-600">@lang('offers.line12')</p>
                </div>
                <div class="relative text-left lg:px-2">
                    <h4 class="mt-4 text-lg font-semibold leading-none text-gray-800">@lang('offers.line13')</h4>
                    <p class="mt-2 text-base font-normal leading-tight text-gray-600">@lang('offers.line14')</p>
                </div>
                <div class="relative text-left lg:px-2">
                    <h4 class="mt-4 text-lg font-semibold leading-none text-gray-800">@lang('offers.line15')</h4>
                    <p class="mt-2 text-base font-normal leading-tight text-gray-600">@lang('offers.line16')</p>
                </div>
                <div class="relative text-left lg:px-2">
                    {{--                    @if (config('landing_form.require_email') || config('app.broker_enabled')) --}}
                    <p class="mt-8 text-base font-normal leading-tight">
                        <a class="underline text_primary link_primary" href="{{ route('unsubscribe.email') }}">
                            @lang('main.unsubscribe_from_email')
                        </a>
                    </p>
                    {{--                    @endif --}}
                    @if (config('landing_form.require_phone'))
                        <p class="mt-8 text-base font-normal leading-tight">
                            <a class="underline text_primary link_primary" href="{{ route('unsubscribe.phone') }}">
                                @lang('main.unsubscribe_from_sms')
                            </a>
                        </p>
                    @endif
                    @if (config('app.rc_enabled'))
                        <p class="mt-8 text-base font-normal leading-tight">
                            <a class="underline text_primary link_primary" href="{{ route('unsubscribe.rc') }}">
                                @lang('main.unsubscribe_from_rc')
                            </a>
                        </p>
                    @endif
                </div>
            </div>
        @endif
    </section>
    <footer class="py-8">
        <div class="flex flex-col items-center justify-center px-4 text-center">
            <x-application-logo></x-application-logo>
            <x-application-copyright-line></x-application-copyright-line>
        </div>
    </footer>
@endsection

@section('javascript')
    <script>
        function logEvent(name, params) {
            if (!name) {
                return;
            }

            if (window.AnalyticsWebInterface) {
                // Call Android interface
                window.AnalyticsWebInterface.logEvent(name, JSON.stringify(params));
            } else if (window.webkit &&
                window.webkit.messageHandlers &&
                window.webkit.messageHandlers.firebase) {
                // Call iOS interface
                var message = {
                    command: 'logEvent',
                    name: name,
                    parameters: params
                };
                window.webkit.messageHandlers.firebase.postMessage(message);
            } else {
                // No Android or iOS interface found
                console.log("No native APIs found.");
            }
        }
    </script>
@endsection
