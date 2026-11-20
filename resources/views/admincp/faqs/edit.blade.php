@extends('layouts.admin')

@section('head')
    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/4.18.0/full-all/ckeditor.js"></script>
@endsection

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="text-2xl font-medium text-gray-700">{{ $page_title ?? 'Edit FAQ - ' . $faq->question }}</h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST">
                @csrf

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.textarea', [
                        'id' => 'question',
                        'name' => 'question',
                        'label' => 'Question',
                        'placeholder' => 'Question',
                        'required' => true,
                        'value' => old('question', $faq->question),
                    ])
                </div>

                <div class="mb-4">
                    @include('admincp.includes.textarea', [
                        'id' => 'answer',
                        'name' => 'answer',
                        'label' => 'Answer',
                        'placeholder' => 'Answer',
                        'cols' => 30,
                        'rows' => 6,
                        'value' => old('answer', $faq->answer),
                    ])
                </div>

                <h2 class="admin-page-fieldset-title">Options</h2>

                <div>
                    @include('admincp.includes.checkbox', [
                        'id' => 'active',
                        'name' => 'active',
                        'value' => 1,
                        'label' => 'Active',
                        'checked' => old('active', $faq->active),
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

    @can('delete faqs')
        @if ($faq && $faq->id)
            <div class="content-wrapper">
                <div class="content-wrapper__form-wrapper">
                    <h2 class="admin-page-fieldset-title">Delete</h2>
                    <div class="admin-page-fieldset-wrapper_last">
                        <form action="{{ route('admincp.faqs.destroy', $faq) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="button_danger" style="margin-right: 0;"
                                onclick="return confirm('Are you sure you want to delete this FAQ?');">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endcan

@endsection

@section('javascript')
    <script>
        CKEDITOR.replace('answer', {
            disallowedContent: '*[bis_size]'
        });
    </script>
@endsection
