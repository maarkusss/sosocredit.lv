@extends('layouts.admin')

@section('content')

    <div class="content-wrapper"
         x-data="{ isProvider: false }"
    >
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Add a new lender' }}</h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST" enctype="multipart/form-data">
                @csrf

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="flex items-center justify-start my-4 hidden"
                     id="lender-image-container">
                    <img class="object-contain max-w-full max-h-full"
                         alt="Lender image"
                         id="lender-image">
                </div>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                        'id' => 'name',
                        'name' => 'name',
                        'label' => 'Name',
                        'placeholder' => 'Lender\'s name',
                        'required' => true,
                    ])

                    <label class="flex flex-col w-full">
                        <span class="mb-2 text-sm font-medium text-gray-600">Image *</span>
                        <input
                                class="previewUploadableImage px-3 py-2 text-xs text-gray-700 bg-gray-100 border border-gray-300 rounded focus:border-primary-dark focus:ring-primary-dark"
                                name="image"
                                type="file"
                                accept="image/*"
                                data-preview-input="lender-image"
                                data-preview-input-container="lender-image-container"
                                id="fileInput"
                                required>
                    </label>

                    @include('admincp.includes.input', [
                        'id' => 'affLink',
                        'name' => 'affLink',
                        'label' => 'Aff link',
                        'placeholder' => 'Lender\'s affiliate link',
                        'required' => true,
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.checkbox', [
                       'id' => 'is_broker',
                       'name' => 'is_broker',
                       'value' => 1,
                       'label' => 'Broker',
                       'hint' => 'This lender will not be reflected in application results. Does not affect providers.'
                    ])
                    @include('admincp.includes.checkbox', [
                       'id' => 'is_provider',
                       'name' => 'is_provider',
                       'value' => 1,
                       'label' => 'Provider',
                       'x_model' => 'isProvider',
                       'hint' => 'API integrated lender. Only check or uncheck if You know what You are doing!'
                    ])
                </div>

                <div id="provider-settings" class="admin-page-fieldset-wrapper" :class="{ '!hidden': !isProvider }">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @include('admincp.includes.input', [
                             'id' => 'abbreviation',
                             'name' => 'abbreviation',
                             'label' => 'Abbreviation',
                             'placeholder' => 'Lender\'s short call',
                             'alpine_required' => 'isProvider',
                         ])
                        @include('admincp.includes.input', [
                            'id' => 'slug',
                            'name' => 'slug',
                            'label' => 'Slug',
                            'placeholder' => 'Lender\'s slug',
                            'alpine_required' => 'isProvider',
                        ])
                        @include('admincp.includes.input', [
                            'id' => 'offer_expiration',
                            'name' => 'offer_expiration',
                            'label' => 'Offer expires in (hours)',
                            'placeholder' => 'Offer expiration',
                        ])
                    </div>

                    <div class="flex flex-col lg:flex-1">
                        <span class="text-[14px] font-semibold text-center text-gray-600">Rejected statuses</span>
                        <div class="grid grid-cols-2 gap-x-4 p-2">
                            @foreach(['Error', 'Rejected', 'Expired', 'Cancelled', 'Refused'] as $status)

                                @include('admincp.includes.checkbox', [
                                   'id' => 'rejected_status_' . $status,
                                   'name' => 'rejected_statuses[' . $status . ']',
                                   'value' => 1,
                                   'label' => $status,
                                ])

                            @endforeach
                        </div>
                    </div>
                </div>

                <h2 class="admin-page-fieldset-title">Display information</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                        'id' => 'firstLoan',
                        'name' => 'firstLoan',
                        'label' => 'First loan',
                        'placeholder' => 'Lender\'s first loan amount',
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'maxAmount',
                        'name' => 'maxAmount',
                        'label' => 'Max amount',
                        'placeholder' => 'Lender\'s max loan amount',
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'minTerm',
                        'name' => 'minTerm',
                        'label' => 'Min term in days',
                        'placeholder' => 'Lender\'s min term in days',
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'maxTerm',
                        'name' => 'maxTerm',
                        'label' => 'Max term in days',
                        'placeholder' => 'Lender\'s max term in days',
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'minYears',
                        'name' => 'minYears',
                        'label' => 'Min years',
                        'placeholder' => 'Lender\'s min years',
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'maxYears',
                        'name' => 'maxYears',
                        'label' => 'Max years',
                        'placeholder' => 'Lender\'s max years',
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'receivingTime',
                        'name' => 'receivingTime',
                        'label' => 'Receiving time in minutes',
                        'placeholder' => 'Lender\'s receiving time in minutes',
                        'required' => true,
                    ])
                </div>

                <h2 class="admin-page-fieldset-title">Categories</h2>

                <div class="flex flex-wrap mt-2 mb-6 -mx-2 -my-">
                    @foreach ($languages as $language)
                        <div class="flex flex-col w-1/2 md:w-1/4 lg:flex-1">
                            <span class="text-base font-semibold text-center text-gray-800">{{ strtoupper($language->value) }}</span>
                            @forelse($categories->where('lang', $language->value) as $category)
                                @include('admincp.includes.checkbox', [
                                   'id' => 'category_' . $language->value . '_' . $category->id,
                                   'name' => 'category[' . $category->id . ']',
                                   'value' => 1,
                                   'label' => $category->name,
                               ])
                            @empty
                                <label class="flex items-center mx-2 my-1 text-gray-700 text-normal">
                                    No loan types on this language
                                </label>
                            @endforelse
                        </div>
                    @endforeach
                </div>

                <h2 class="admin-page-fieldset-title">Options</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                        'id' => 'zero_percent_value',
                        'name' => 'zero_percent_value',
                        'label' => '0% value',
                        'placeholder' => '',
                        'value' => old('zero_percent_value'),
                    ])
                </div>

                <div>
                    @include('admincp.includes.checkbox', [
                       'id' => 'zero_percent',
                       'name' => 'zero_percent',
                       'value' => 1,
                       'label' => '0% Lender',
                   ])

                    @include('admincp.includes.checkbox', [
                       'id' => 'active',
                       'name' => 'active',
                       'value' => 1,
                       'label' => 'Active',
                   ])
                </div>

                <div class="button-wrapper">
                    <a href="{{ route('admincp.lenders.index') }}">
                        <button type="submit" class="button_submit">
                            Add
                        </button>
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
