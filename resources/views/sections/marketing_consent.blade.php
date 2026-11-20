@extends('layouts.public')

@section('content')

    <main class="flex flex-col max-w-screen-xl px-4 py-8 mx-auto">
        <h1 class="content__heading">{{ $section->name }}</h1>
        <div class="content__content">
            @if ($section->children()->where('active', 1)->count() > 0)
                @foreach ($section->children()->with('parent')->where('active', 1)->get() as $subsection)
                    <div class="px-2 mx-auto md:px-4">
                        <a class="my-4 text-2xl hover:underline"
                            href="{{ route('section.deep', ['name' => $subsection->parent->route_name, 'deep_name' => $subsection->route_name]) }}">
                            {{ $subsection->name }}
                        </a>
                    </div>
                @endforeach
            @endif

            <article class="max-w-full prose text-gray-800">
                {!! $section->text !!}
            </article>
        </div>
    </main>

@endsection
