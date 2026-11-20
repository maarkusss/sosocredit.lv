@extends('layouts.admin')

@section('head')
    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/4.18.0/full-all/ckeditor.js"></script>
@endsection

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="text-2xl font-medium text-gray-700">{{ $page_title ?? 'Edit News - ' . $news->title }}</h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST" enctype="multipart/form-data">
                @csrf

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.select', [
                        'id' => 'category_id',
                        'name' => 'category_id',
                        'label' => 'Category',
                        'options' => $categories,
                        'value' => $news->category_id,
                        'required' => true,
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.input', [
                        'id' => 'title',
                        'name' => 'title',
                        'label' => 'Title',
                        'value' => $news->title,
                        'required' => true,
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.input', [
                        'id' => 'url',
                        'name' => 'url',
                        'label' => 'Url',
                        'value' => $news->url,
                        'required' => true,
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    <label for="news-image" class="input-wrap__label">Image</label>

                    <div class="flex -mt-4">
                        <input class="px-3 py-2 mr-2 text-xs text-gray-700 bg-white border rounded border-primary-normal"
                            name="image" type="file" accept="image/*" id="news-image"
                            style="{{ $news->image ? 'display:none' : 'display:block' }}" />

                        <input class="hidden" name="image_changed" id="image-changed" value="0" />

                        <img src="{{ asset($news->image) }}" alt="news image" id="preview-image"
                            class="object-cover object-center max-h-[40px]"
                            style="{{ $news->image ? 'display:block' : 'display:none;' }}" />

                        <!-- Delete/clear icon -->
                        <button id="delete-image" type="button"
                            class="p-1 text-red-500 transition-all duration-200 bg-white hover:text-red-900"
                            title="Delete image" onclick="deleteNewsImage()"
                            style="{{ $news->image ? 'display:block' : 'display:none' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.input', [
                        'id' => 'image_alt_text',
                        'name' => 'image_alt_text',
                        'label' => 'Image\'s alt text',
                        'value' => $news->image_alt_text,
                        'required' => false,
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.textarea', [
                        'id' => 'tiny_description',
                        'name' => 'tiny_description',
                        'label' => 'Tiny description',
                        'required' => false,
                        'value' => old('tiny_description', $news->tiny_description),
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.textarea', [
                        'id' => 'text',
                        'name' => 'text',
                        'label' => 'Content',
                        'required' => false,
                        'value' => old('description', $news->text),
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.input', [
                        'id' => 'meta_title',
                        'name' => 'meta_title',
                        'label' => 'Meta title',
                        'value' => $news->meta_title,
                        'required' => false,
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.textarea', [
                        'id' => 'meta_description',
                        'name' => 'meta_description',
                        'label' => 'Meta description',
                        'value' => $news->meta_description,
                        'required' => false,
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.textarea', [
                        'id' => 'source',
                        'name' => 'source',
                        'label' => 'Source (Specific HTML in head tag)',
                        'placeholder' => 'Source text',
                        'value' => $news->source,
                        'required' => false,
                    ])
                </div>

                <h2 class="admin-page-fieldset-title">Options</h2>

                <div>
                    @include('admincp.includes.checkbox', [
                        'id' => 'active',
                        'name' => 'active',
                        'value' => 1,
                        'label' => 'Active',
                        'checked' => old('active', $news->active),
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

    <!-- Connected urls block -->
    @if ($news && $news->id)
        @include('admincp.connected_urls.includes.section', ['section' => $news])
    @endif

    <!-- Delete block -->
    @can('delete news')
        @if ($news && $news->id)
            <div class="content-wrapper">
                <div class="content-wrapper__form-wrapper">
                    <h2 class="admin-page-fieldset-title">Delete</h2>
                    <div class="admin-page-fieldset-wrapper_last">
                        <form action="{{ route('admincp.news.destroy', $news) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="button_danger" style="margin-right: 0;"
                                onclick="return confirm('Are you sure you want to delete this News item?');">
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
        const input = document.getElementById('news-image');
        const clearBtn = document.getElementById('delete-image');

        input.addEventListener('change', () => {
            clearBtn.style.display = input.files.length ? 'block' : 'none';
            document.getElementById('image-changed').value = 1;
        });

        function deleteNewsImage() {
            document.getElementById('news-image').value = '';
            document.getElementById('news-image').style.display = 'block';
            document.getElementById('delete-image').style.display = 'none';
            document.getElementById('preview-image').style.display = 'none';
            document.getElementById('image-changed').value = 1;
        }

        CKEDITOR.replace('text', {
            disallowedContent: '*[bis_size]'
        });
    </script>
@endsection
