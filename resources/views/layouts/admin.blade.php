@php

    $meta_title = 'Admin - ' . config('app.name');

    if (!empty($meta) && !empty($meta['title'])) {
        $meta_title = $meta['title'];
    }

@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@hasSection('title')@yield('title')@else{{ $meta_title }}@endif</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (config('app.name') === 'Sosocredit.lv')
        <link rel="shortcut icon" href="/favicon2.png">
    @else
        <link rel="shortcut icon" href="/favicon.png">
    @endif

    @vite(['resources/css/admin.scss', 'resources/js/admin.js'])

    @yield('head')

</head>
<body class="body-class admin-section">
    @auth
        <div class="site-wrapper">
            @include('admincp.includes.header')
    @endauth

        @include('admincp.includes.infobox')

        <main class="main">
            @auth
                @include('admincp.includes.sidebar')
            @endauth

            @yield('content')
        </main>

    @auth
        </div>
    @endauth

    @yield('javascript')
</body>
</html>
