@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="text-2xl font-medium text-gray-700">{{ $page_title ?? 'Edit Connected URL' }}</h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form action="{{ $connected_url && $connected_url->id ? route('admincp.connected-urls.update', $connected_url->id) : route('admincp.connected-urls.store') }}"
                  method="POST">
                @csrf

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="flex flex-row justify-start mb-4">
                    <div class="w-16 mr-2">
                        @include('admincp.includes.input', [
                            'id' => 'current_locale',
                            'name' => 'current_locale',
                            'value' => $connected_url->lang,
                            'class' => 'input-wrap__input_dark cursor-disabled',
                            'label' => 'Lang',
                            'readonly' => true,
                        ])
                    </div>

                    <div class="w-1/2 mr-2">
                        @include('admincp.includes.input', [
                            'id' => 'url',
                            'name' => 'url',
                            'value' => old('url', $connected_url->url),
                            'label' => 'URL',
                            'required' => true,
                        ])
                    </div>
                </div>

                @if (!empty($connected_url->url))
                    <div class="flex flex-row justify-start mb-4">
                        <div class="w-16 mr-2 pt-2 text-sm text-center">
                            <span>Full:</span>
                        </div>
                        <div class="w-1/2">
                            @include('admincp.includes.input', [
                                'id' => 'url',
                                'name' => 'url',
                                'value' => config('app.url') . $connected_url->url,
                                'label' => '',
                                'readonly' => true,
                                'class' => 'opacity-80',
                            ])
                        </div>
                    </div>
                @endif

                <h4 class="mb-4">Related URLs</h4>

                @foreach ($related_urls as $locale => $related_url)
                    <div class="flex flex-row justify-start mb-4">
                        <div class="w-16 mr-2">
                            @include('admincp.includes.input', [
                                'id' => 'locale_' . $locale,
                                'name' => 'related_locales[' . $locale . ']',
                                'value' => $locale,
                                'class' => 'input-wrap__input_dark',
                                'readonly' => true,
                            ])
                        </div>

                        <div class="w-1/2">
                            @include('admincp.includes.input', [
                                'id' => 'locale_' . $locale,
                                'name' => 'related_urls[' . $locale . ']',
                                'value' => $related_urls[$locale] ?? '',
                            ])
                        </div>
                    </div>

                    @if (!empty($related_urls[$locale] ?? ''))
                        <div class="flex flex-row justify-start mb-4">
                            <div class="w-16 mr-2 pt-2 text-sm text-center">
                                <span>Full:</span>
                            </div>
                            <div class="w-1/2">
                                @include('admincp.includes.input', [
                                    'id' => 'url',
                                    'name' => 'url',
                                    'value' => config('app.url') . $related_urls[$locale],
                                    'label' => '',
                                    'readonly' => true,
                                    'class' => 'opacity-80',
                                ])
                            </div>
                        </div>
                    @endif
                @endforeach

                <div class="button-wrapper">
                    <button type="submit" class="button_submit">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($connected_url && $connected_url->id)
        <div class="content-wrapper">
            <div class="content-wrapper__form-wrapper">
                <h2 class="admin-page-fieldset-title">Delete</h2>
                <div class="admin-page-fieldset-wrapper_last">
                    <form action="{{ route('admincp.connected-urls.destroy', $connected_url) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="button_danger" style="margin-right: 0;"
                                onclick="return confirm('Are you sure you want to delete this connected URL?');">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection
