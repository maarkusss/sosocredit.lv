@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="text-2xl font-medium text-gray-700">{{ $page_title ?? 'Edit Review - ' . $review->name }}</h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST">
                @csrf

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.select', [
                        'id' => 'section_id',
                        'name' => 'section_id',
                        'label' => 'Section',
                        'options' => $sections,
                        'value' => $review->section_id,
                        'required' => true,
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper_full mb-4">
                    <div class="px-4">
                        <label for="image" class="input-wrap__label">
                            Image *
                        </label>

                        <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-12 gap-x-4 gap-y-10">
                            @foreach ($reviews_images as $review_image_index => $review_image)
                                <div class="w-12 h-20 mr-2 flex flex-col justify-content-center items-center">
                                    <label for="review_image_{{ $review_image_index }}" class="w-12 h-12 mb-2">
                                        <img src="{{ $review_image }}"
                                             alt="" />
                                    </label>

                                    @include('admincp.includes.radio', [
                                       'id' => 'review_image_' . $review_image_index,
                                       'name' => 'image',
                                       'value' => $review_image,
                                       'class' => 'mb-4',
                                       'checked' => $review->image && $review->image == $review_image
                                   ])

                                    <span class="text-gray-500 text-sm -my-5">{{ basename($review_image)  }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="admin-page-fieldset-wrapper_full mt-4">
                    @include('admincp.includes.input', [
                        'id' => 'name',
                        'name' => 'name',
                        'label' => 'Name',
                        'placeholder' => 'Name',
                        'required' => true,
                        'value' => old('name', $review->name)
                    ])
                </div>

                <div class="mb-4">
                    @include('admincp.includes.textarea', [
                        'id' => 'text',
                        'name' => 'text',
                        'label' => 'Text',
                        'placeholder' => 'Text',
                        'cols' => 30,
                        'rows' => 3,
                        'required' => true,
                        'value' => old('text', $review->text),
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

    @can('delete reviews')
        @if ($review && $review->id)
            <div class="content-wrapper">
                <div class="content-wrapper__form-wrapper">
                    <h2 class="admin-page-fieldset-title">Delete</h2>
                    <div class="admin-page-fieldset-wrapper_last">
                        <form action="{{ route('admincp.reviews.destroy', $review) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="button_danger" style="margin-right: 0;"
                                    onclick="return confirm('Are you sure you want to delete this Review?');">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endcan

@endsection
