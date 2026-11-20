@extends('layouts.public')

@section('content')
    @include('admincp.includes.infobox')

    <main class="flex items-start justify-center max-w-screen-sm px-4 py-24 mx-auto">
        <div class="w-full text-center">
            <h1 class="text-2xl font-semibold leading-none text-gray-800">
                @lang('main.unsubscribe_news_and_info')
            </h1>

            <p id="unsubscribe-text" class="mt-4 text-base font-normal leading-tight text-gray-600">
                @lang('main.if_you_click_unsubscribe_you_will_unsubscribe_email')
            </p>

            <form method="POST" id="unsubscribe-form" action="{{ route('unsubscribe.email') }}">
                @csrf

                <label class="block mt-6 mb-4">
                    <select id="unsubscribe_type" name="type"
                        class="w-full px-5 py-4 border-gray-300 rounded border_primary_dark ring_primary_dark"
                        onchange="toggleUnsubscribeFields()">

                        <option value="email">
                            @lang('main.unsubscribe_from_email')
                        </option>

                        <option value="phone">
                            @lang('main.unsubscribe_from_sms')
                        </option>

                        @if (config('app.rc_enabled'))
                            <option value="rc">
                                @lang('main.unsubscribe_from_rc')
                            </option>
                        @endif
                    </select>
                </label>


                <label id="email-field" class="block">
                    <input class="w-full px-5 py-4 border-gray-300 rounded border_primary_dark ring_primary_dark"
                        name="email" type="email" value="{{ old('email', $email_value ?? '') }}" inputmode="email"
                        autocomplete="email" placeholder="@lang('main.your_email_address')">
                </label>

                <label id="phone-field" class="hidden">
                    <input
                        class="w-full px-5 py-4 bg-white border-gray-200 rounded border_primary_dark ring_primary_dark applyPhoneMask"
                        name="phone" type="text" inputmode="tel" autocomplete="off"
                        placeholder="+{{ phone_extension() }}{{ config('landing_form.phone_placeholder') }}">
                </label>

                <button type="submit"
                    class="w-full px-4 py-3 mt-6 text-lg font-semibold text-white transition-all duration-200 rounded button_primary focus:outline-none focus:shadow-outline">
                    @lang('main.unsubscribe')
                </button>
            </form>
        </div>
    </main>
@endsection
@section('javascript')
    <script>
        const rcEnabled = @json(config('app.rc_enabled'));

        const texts = {
            email: @json(__('main.if_you_click_unsubscribe_you_will_unsubscribe_email')),
            phone: @json(__('main.if_you_click_unsubscribe_you_will_unsubscribe_phone')),
            @if (config('app.rc_enabled'))
                rc: @json(__('main.if_you_click_unsubscribe_you_will_unsubscribe_rc')),
            @endif
        };

        const actions = {
            email: "{{ route('unsubscribe.email') }}",
            phone: "{{ route('unsubscribe.phone') }}",
            @if (config('app.rc_enabled'))
                rc: "{{ route('unsubscribe.rc') }}",
            @endif
        };

        function toggleUnsubscribeFields() {
            const select = document.getElementById('unsubscribe_type');
            const form = document.getElementById('unsubscribe-form');

            let type = select.value;

            if (type === 'rc' && !rcEnabled) {
                type = 'phone';
                select.value = 'phone';
            }

            const emailWrapper = document.getElementById('email-field');
            const phoneWrapper = document.getElementById('phone-field');

            const emailInput = emailWrapper.querySelector('input');
            const phoneInput = phoneWrapper.querySelector('input');

            const text = document.getElementById('unsubscribe-text');

            emailWrapper.classList.add('hidden');
            phoneWrapper.classList.add('hidden');

            emailInput.disabled = true;
            phoneInput.disabled = true;

            emailInput.required = false;
            phoneInput.required = false;

            form.action = actions[type];
            text.innerText = texts[type];

            if (type === 'email') {
                emailWrapper.classList.remove('hidden');
                emailInput.disabled = false;
                emailInput.required = true;
            } else {
                phoneWrapper.classList.remove('hidden');
                phoneInput.disabled = false;
                phoneInput.required = true;
            }
        }

        document.addEventListener('DOMContentLoaded', toggleUnsubscribeFields);
    </script>
@endsection
