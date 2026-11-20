@extends('layouts.public')

@section('content')
    @include('admincp.includes.infobox')

    <main class="flex items-start justify-center max-w-screen-sm px-4 py-24 mx-auto">
        <div class="text-center">
            <h1 class="text-2xl font-semibold leading-none text-gray-800">@lang('main.unsubscribe_news_and_info')</h1>
            <p class="mt-4 text-base font-normal leading-tight text-gray-600">@lang('main.if_you_click_unsubscribe_you_will_unsubscribe_rc')</p>
            <form method="POST">
                @csrf

                <label class="block mt-4">
                    <input class="w-full px-5 py-4 bg-white border-gray-200 rounded border_primary_dark ring_primary_dark applyPhoneMask"
                           id="phone"
                           name="phone"
                           type="text"
                           inputmode="tel"
                           autocomplete="off"
                           placeholder="+{{ phone_extension() }}{{ config('landing_form.phone_placeholder') }}"
                           required>
                </label>

                <button type="submit"
                        class="w-full px-4 py-3 mt-4 text-lg font-semibold text-center text-white transition-all duration-200 rounded button_primary focus:outline-none focus:shadow-outline">
                    @lang('main.unsubscribe')
                </button>
            </form>
        </div>
    </main>

@endsection
