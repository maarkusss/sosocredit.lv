@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="text-2xl font-medium text-gray-700">
                @if (!empty($block->id))
                    {{ $page_title ?? 'Edit block - ' . $block->key }}
                @else
                    {{ $page_title ?? 'Create block' }}
                @endif

            </h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form action="{{ !empty($block->id) ? route('admincp.blocks.update', $block) : route('admincp.blocks.store') }}" method="POST">
                @csrf

                @if (!empty($block->id))
                    @method('PUT')
                @endif

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="admin-page-fieldset-wrapper">

                    @if (!empty($block->id))
                        @include('admincp.includes.input', [
                           'id' => 'lang',
                           'name' => 'lang',
                           'label' => 'Lang',
                           'placeholder' => 'Lang',
                           'value' => $block->lang,
                           'readonly' => !empty($block->id),
                       ])
                    @else
                        @include('admincp.includes.select', [
                            'id' => 'lang',
                            'name' => 'lang',
                            'label' => 'Lang',
                            'default_option' => '',
                            'options' => collect(available_locales())
                                ->mapWithKeys(function($locale) {
                                    return [$locale => $locale];
                                })
                                ->toArray(),
                            'value' => app()->getLocale(),
                        ])
                    @endif

                    @include('admincp.includes.input', [
                       'id' => 'key',
                       'name' => 'key',
                       'label' => 'Key',
                       'placeholder' => 'Key',
                       'value' => $block->key,
                       'readonly' => !empty($block->id),
                   ])

                    @include('admincp.includes.input', [
                       'id' => 'tag',
                       'name' => 'tag',
                       'label' => 'Tag',
                       'placeholder' => 'Tag',
                       'value' => $block->tag,
                   ])
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.textarea', [
                        'id' => 'value',
                        'name' => 'value',
                        'label' => 'Value',
                        'placeholder' => '',
                        'required' => true,
                        'rows' => 8,
                        'value' => old('value', $block->value)
                    ])
                </div>

                <div class="button-wrapper">
                    <button type="submit" class="button_submit">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
