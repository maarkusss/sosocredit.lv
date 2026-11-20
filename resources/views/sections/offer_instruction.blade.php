@php
    /** @var \App\Models\Section $section */

    $offer = [
        'provider' => request('provider', 'N/A'),
        'amount' => request('amount', 0),
        'installment' => request('installment', 0),
        'esigning_url' => request('esigning_url', '#'),
        'veriff_url' => request('veriff_url', '#'),
        'comments' => request('comments', '#'),
    ];

    $variables = [
        '{provider}' => $offer['provider'],
        '{amount}' => number_format((float)$offer['amount'], 2, ',', ' '),
        '{installment}' => number_format((float)$offer['installment'], 2, ',', ' '),
        '{Esigning Link}' => '<a href="' . $offer['esigning_url'] . '" target="_blank">' . __('Sign contract') . '</a>',
        '{Veriff Link}' => '<a href="' . $offer['veriff_url'] . '" target="_blank">' . __('Verify identity') . '</a>',
        '{comments}' => $offer['comments'],
    ];

    $content = str_replace(array_keys($variables), array_values($variables), $section->text);
@endphp

@extends('layouts.public')

@section('content')

    <main class="flex flex-col max-w-screen-xl px-4 py-8 mx-auto">
        <h1 class="content__heading">{{ $section->name }}</h1>
        <div class="content__content">

            <article class="max-w-full prose" id="article_section_text">
                {!! $content !!}
            </article>

            @if ($section->children()->where('active', 1)->count() > 0)
                <ul class="list-disc pl-4 mt-2">
                    @foreach ($section->children()->with('parent')->where('active', 1)->get() as $subsection)
                        <li class="my-2 md:my-0">
                            <a class="hover:underline"
                               href="{{ route('section.deep', ['name' => $subsection->parent->route_name, 'deep_name' => $subsection->route_name]) }}">
                                {{ $subsection->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if (!empty($section->json) && !empty($section->json['bottom_button_link']))
            <div class="w-full -mt-4">
                <div class="h-8 bg-white w-full">&nbsp;</div>
                <a href="{{ $section->json['bottom_button_link'] }}"
                   class="w-full block landing-form__button">
                    {{ $section->json['bottom_button_text'] }}
                </a>
            </div>
        @endif
    </main>

@endsection

@section('javascript')
    <link rel="stylesheet" href="/css/lightbox.min.css" />
    <script src="/js/lightbox-plus-jquery.min.js"></script>

    <script>
        const section_text = document.getElementById("article_section_text");

        if (section_text) {
            const imgs = section_text.getElementsByTagName("img");
            if (imgs) {
                for (const img of imgs) {
                    img.parentNode.dataset.lightbox = "gallery";
                }
            }
        }

        lightbox.option({
            'resizeDuration': 200,
            'imageFadeDuration': 350,
            'showImageNumberLabel': false,
        })
    </script>
@endsection
