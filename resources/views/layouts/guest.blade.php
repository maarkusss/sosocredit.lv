{{-- Added by and required for Laravel Breeze --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (!app()->isProduction())<meta name="robots" content="noindex, nofollow">@endif
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="body-class public-section {{ config('app.public_css_class') }}">
    <div class="antialiased">
        {{ $slot }}
    </div>
</body>
</html>
