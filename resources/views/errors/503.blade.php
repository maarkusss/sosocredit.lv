@extends('layouts.public')

@section('title', __('Not Found'))

@section('content')
    <main class="flex items-center justify-center min-h-screen px-4 py-8 lg:px-0">
        <div class="text-center">
            <h1 class="block text-4xl font-medium text_primary">{{ __('errors.503 | Service Unavailable') }}</h1>
            <span class="block text-sm font-normal text-gray-800">{{ __('errors.Oops.. You ended up too far! 🚀') }}</span>
            <a href="/"
               class="block px-3 py-2 mt-8 text-sm font-medium text-white rounded button_primary">{{ __('errors.Take me back!') }}</a>
        </div>
    </main>
@endsection
