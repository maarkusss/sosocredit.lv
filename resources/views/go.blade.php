<!DOCTYPE HTML>
<html lang="{{ app()->getLocale() }}">

<head>
    <title>{{ config('app.name') }}</title>
    <meta name="robots" content="noindex, nofollow">

    @if ($head_code_go = \App\Models\Setting::retrieve('head_code_go'))
        {!! $head_code_go !!}
    @endif
    <script>
        window.setTimeout(function() {
            window.location = "{!! $link !!}";
        }, 100);
    </script>
</head>

<body>
    <div style="text-align:center;"><img src="/loading.gif"
             alt="loading"></div>
</body>

</html>
