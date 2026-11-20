@php
    /** @var \App\Models\Section $section */
@endphp

@extends('layouts.public')

@section('content')

    <main class="flex flex-col max-w-screen-xl px-4 py-8 mx-auto">
        <h1 class="content__heading">{{ $section->name }}</h1>
        <div class="content__content">
            {{--            @if (count($section->children()->where('active', true)) > 0) --}}
            {{--                @foreach ($section->children()->where('active', true) as $subsection) --}}
            {{--                    <div class="px-2 mx-auto md:px-4"> --}}
            {{--                        <a class="my-4 text-2xl hover:underline" --}}
            {{--                           href="{{ route('section.deep', ['name' => $subsection->parent()->route_name, 'deep_name' => $subsection->route_name]) }}"> --}}
            {{--                            {{ $subsection->name }} --}}
            {{--                        </a> --}}
            {{--                    </div> --}}
            {{--                @endforeach --}}
            {{--            @endif --}}

            <article class="max-w-full prose" id="article_section_text">
                {!! $section->text !!}
            </article>

            @if ($section->children()->where('active', 1)->count() > 0)
                <ul class="pl-4 mt-2 list-disc">
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

            @if ($section->common_name == 'offer-expired')
                <div class="max-w-[300px]">
                    <a class="flex flex-row items-center justify-center mt-4 text-lg applicant-form__button disabled:opacity-50 disabled:cursor-not-allowed"
                        href="{{ route('home.index', ['utm_campaign' => 'offer-expired']) }}">
                        @lang('Fill new application')
                    </a>
                </div>
            @endif
        </div>

        @if (!empty($section->json) && !empty($section->json['bottom_button_link']))
            <div class="w-full -mt-4">
                <div class="w-full h-8 bg-white">&nbsp;</div>
                <a href="{{ $section->json['bottom_button_link'] }}" class="block w-full landing-form__button">
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
