@extends('layouts.public')

@section('content')
    @include('admincp.includes.infobox')

    <main class="px-4 py-8 lg:px-0">
        <div class="container flex flex-col mx-auto">
            <h1 class="content__heading">{{ $section->name }}</h1>
            <div class="container flex flex-col items-start justify-center mx-auto xl:flex-row lg:px-4">
                <div class="flex flex-col items-start justify-start w-full mb-8 xl:mb-0 xl:w-1/5">
                    {!! $section->text !!}
                </div>
                <div class="flex flex-col items-start justify-start w-full xl:ml-16 xl:w-1/2">
                    <form class="flex flex-wrap w-full"
                          action="{{ route('contactus.post') }}"
                          method="POST">
                        @csrf
                        <input type="hidden"
                               id="recaptchaResponse"
                               name="recaptcha">
                        <label class="w-full mt-0 md:pr-2 md:w-1/2">
                            <span class="block text-sm font-medium">{{ __('main.name') }}</span>
                            <input class="w-full px-3 py-2 mt-2 text-sm text-gray-700 bg-white border rounded appearance-none"
                                   id="name"
                                   name="name"
                                   type="text"
                                   value="{{ old('name') }}"
                                   required>
                        </label>
                        <label class="w-full mt-4 md:pl-2 md:mt-0 md:w-1/2">
                            <span class="block text-sm font-medium">{{ __('main.e-mail') }}</span>
                            <input class="w-full px-3 py-2 mt-2 text-sm text-gray-700 bg-white border rounded appearance-none"
                                   id="email"
                                   name="email"
                                   type="email"
                                   value="{{ old('email') }}"
                                   required>
                        </label>
                        <label class="w-full mt-4">
                            <span class="block text-sm font-medium">{{ __('main.question') }}</span>
                            <textarea class="w-full px-3 py-2 mt-2 text-sm text-gray-700 bg-white border rounded appearance-none resize-none"
                                      id="question"
                                      name="question"
                                      rows="5"
                                      required>{{ old('question') }}</textarea>
                        </label>
                        <input type="hidden" name="answer" value="" />
                        <button class="w-full px-3 py-2 mt-4 ml-auto text-sm font-medium text-white rounded bg-primary-normal hover:bg-primary-dark md:w-auto"
                                type="submit">
                            {{ __('main.send') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('javascript')

    @if (config('recaptcha.enabled'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.api_key') }}"></script>
        <script>
            grecaptcha.ready(function() {
                grecaptcha.execute('{{ config('recaptcha.api_key') }}', {
                    action: 'homepage'
                }).then(function(token) {
                    var recaptchaResponse = document.getElementById('recaptchaResponse');
                    recaptchaResponse.value = token;
                });
            });
        </script>
    @endif

@endsection
