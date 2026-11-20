<!DOCTYPE HTML>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <meta name="robots"
          content="noindex, nofollow">
    <style>
        .body {
            background: #f7fafc;
            margin: 0;
            padding: 15px;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
        }

        .logo {
            text-align: center;
            margin: 0 0 15px 0;
        }

        .clear {
            clear: both;
        }

        .wrapper {
            background-color: #ffffff;
            border: 1px solid #cfcfcf;
            border-radius: 4px;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px 30px 30px;
        }

        .h4 {
            font-size: 1.125rem;
            color: #2d3748;
            margin-top: 1rem;
            line-height: 1;
            font-weight: 600;
            margin-bottom: 0;
        }

        .centered {
            text-align: center;
        }

        .p {
            font-size: 1rem;
            color: #718096;
            margin-top: .5rem;
            line-height: 1.25;
            font-weight: 400;
        }

        .p a {
            color: #718096 !important;
            text-decoration: underline !important;
        }

        table {
            margin-top: 10px;
        }

        td {
            padding: 10px;
        }
    </style>
</head>

<body class="body">
    <div class="logo">
        <a href="{{ config('app.url') }}"
           target="_blank">
            <img src="{{ config('app.url') }}/logo.png?v1"
                 width="96"
                 height="24"
                 alt="{{ config('app.name') }} logo">
        </a>
    </div>

    <div class="clear"></div>

    <div class="wrapper">
        <h4 class="h4 centered">
            {{ config('app.name') }} Contact form
        </h4>

        <br>

        <table border="0" cellpadding="4" cellspacing="1" width="100%">
            <tr>
                <td width="25%" valign="top"><b>Name:</b></td>
                <td valign="top">{{ $name }}</td>
            </tr>
            <tr>
                <td width="25%" valign="top"><b>Email:</b></td>
                <td valign="top">{{ $email }}</td>
            </tr>
            <tr>
                <td width="25%" valign="top"><b>Question:</b></td>
                <td valign="top">{!! $question !!}</td>
            </tr>
        </table>
    </div>

</body>

</html>
