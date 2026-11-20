@extends('layouts.admin')

@section('content')

    <div class="content-wrapper"
         x-data="{ isProvider: {{ $lender->is_provider ? 'true' : 'false' }} }"
    >
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Edit lender - ' . $lender->name }}</h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST" enctype="multipart/form-data">
                @csrf

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="flex items-center justify-start h-48 my-4"
                     id="lender-image-container">
                    <img class="object-contain h-full max-w-full max-h-full"
                         src="{{ $lender->image }}"
                         alt="Logo"
                         id="lender-image">
                </div>

                <div class="admin-page-fieldset-wrapper">

                    @include('admincp.includes.input', [
                        'id' => 'name',
                        'name' => 'name',
                        'label' => 'Name',
                        'placeholder' => 'Lender\'s name',
                        'value' => $lender->name,
                        'required' => true,
                    ])

                    <label class="flex flex-col w-full">
                        <span class="mb-2 text-sm font-medium text-gray-600">Image link</span>
                        <input
                                class="previewUploadableImage px-3 py-2 text-xs text-gray-700 bg-gray-100 border border-gray-300 rounded focus:border-primary-dark focus:ring-primary-dark"
                                name="image"
                                type="file"
                                accept="image/*"
                                data-preview-input="lender-image"
                                data-preview-container="lender-image-container"
                                id="fileInput">
                    </label>

                    @include('admincp.includes.input', [
                        'id' => 'affLink',
                        'name' => 'affLink',
                        'label' => 'Aff link',
                        'placeholder' => 'Lender\'s affiliate link',
                        'value' => $lender->affiliate_link,
                        'required' => true,
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.checkbox', [
                         'id' => 'is_broker',
                         'name' => 'is_broker',
                         'value' => 1,
                         'label' => 'Broker',
                         'checked' => $lender->is_broker,
                         'hint' => 'This lender will not be reflected in application results. Does not affect providers.'
                      ])
                    @include('admincp.includes.checkbox', [
                       'id' => 'is_provider',
                       'name' => 'is_provider',
                       'value' => 1,
                       'label' => 'Provider',
                       'checked' => $lender->is_provider,
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
                            'value' => $lender->abbreviation,
                            'alpine_required' => 'isProvider'
                        ])
                        @include('admincp.includes.input', [
                            'id' => 'slug',
                            'name' => 'slug',
                            'label' => 'Slug',
                            'placeholder' => 'Lender\'s slug',
                            'value' => $lender->slug,
                            'alpine_required' => 'isProvider'
                        ])
                        @include('admincp.includes.input', [
                            'id' => 'offer_expiration',
                            'name' => 'offer_expiration',
                            'label' => 'Offer expires in (hours)',
                            'placeholder' => 'Offer expiration',
                            'value' => $lender->offer_expiration,
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
                                   'checked' => json_decode($lender->rejected_statuses, true)[$status] ?? false
                                ])
                            @endforeach
                        </div>
                    </div>
                </div>

                <h2 class="admin-page-fieldset-title">Display information</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                       'id' => 'slogan',
                       'name' => 'slogan',
                       'label' => 'Slogan (' . strtoupper(app()->getLocale()) . ')',
                       'placeholder' => 'Lender\'s slogan',
                       'value' => $lender_data ? $lender_data->slogan : null,
                   ])

                    @include('admincp.includes.input', [
                       'id' => 'firstLoan',
                       'name' => 'firstLoan',
                       'label' => 'First loan',
                       'placeholder' => 'Lender\'s first loan amount',
                       'value' => $lender->first_loan,
                   ])

                    @include('admincp.includes.input', [
                        'id' => 'maxAmount',
                        'name' => 'maxAmount',
                        'label' => 'Max amount',
                        'placeholder' => 'Lender\'s max loan amount',
                        'value' => $lender->max_amount,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'minTerm',
                        'name' => 'minTerm',
                        'label' => 'Min term in days',
                        'placeholder' => 'Lender\'s min term in days',
                        'value' => $lender->min_term,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'maxTerm',
                        'name' => 'maxTerm',
                        'label' => 'Max term',
                        'placeholder' => 'Lender\'s max term in days',
                        'value' => $lender->max_term,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'minYears',
                        'name' => 'minYears',
                        'label' => 'Min years',
                        'placeholder' => 'Lender\'s min years',
                        'value' => $lender->min_years,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'maxYears',
                        'name' => 'maxYears',
                        'label' => 'Max years',
                        'placeholder' => 'Lender\'s max years',
                        'value' => $lender->max_years,

                    ])

                    @include('admincp.includes.input', [
                        'id' => 'receivingTime',
                        'name' => 'receivingTime',
                        'label' => 'Receiving time in minutes',
                        'placeholder' => 'Lender\'s receiving time in minutes',
                        'value' => $lender->receiving_time,
                    ])
                </div>

                <h2 class="admin-page-fieldset-title">Benefits ({{ strtoupper(app()->getLocale()) }})</h2>
                <div class="admin-page-fieldset-wrapper">
                    <div class="form-group"
                         x-data="{
                             benefits: {{ json_encode($lender_data->benefits ?? ['']) }}.length > 0
                                      ? {{ json_encode($lender_data->benefits ?? ['']) }}
                                      : ['']
                         }">
                        <template x-for="(benefit, index) in benefits" :key="index">
                            <div class="input-group mb-2 flex gap-4">
                                <input
                                    type="text"
                                    name="benefits[]"
                                    class="input-wrap__input"
                                    x-model="benefits[index]"
                                    :placeholder="`Benefit ${index + 1}`"
                                >
                                <button
                                    type="button"
                                    class="button_danger"
                                    @click="benefits.length > 1 && benefits.splice(index, 1)"
                                    :disabled="benefits.length <= 1"
                                >
                                    ×
                                </button>
                            </div>
                        </template>

                        <button
                            type="button"
                            class="button_submit"
                            @click="benefits.push('')"
                            x-show="benefits.length < 4"
                        >
                            Add
                        </button>
                    </div>
                </div>

                <h2 class="admin-page-fieldset-title">Categories</h2>
                <div class="flex flex-wrap mt-2 mb-6 -mx-2 -my-1">
                    @foreach ($languages as $language)
                        <div class="flex flex-col w-1/2 md:w-1/4 lg:flex-1">
                        <span
                                class="text-base font-semibold text-center text-gray-800">{{ strtoupper($language->value) }}</span>
                            @forelse($categories->where('lang', $language->value) as $category)
                                @include('admincp.includes.checkbox', [
                                   'id' => 'category_' . $language->value . '_' . $category->id,
                                   'name' => 'category[' . $category->id . ']',
                                   'value' => 1,
                                   'label' => $category->name,
                                   'checked' => $lender->categories->where('loan_type_id', $category->id)->first(),
                               ])
                            @empty
                                <label class="flex items-center mx-2 my-1 text-gray-700 text-normal">
                                    No loan types on this language
                                </label>
                            @endforelse
                        </div>
                    @endforeach
                </div>

                <h2 class="admin-page-fieldset-title">Information boxes</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                        'id' => 'companysName',
                        'name' => 'companysName',
                        'label' => 'Company\'s name',
                        'placeholder' => 'Lender\'s company\'s name',
                        'value' => $lender_data ? $lender_data->company_name : null,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'address',
                        'name' => 'address',
                        'label' => 'Address',
                        'placeholder' => 'Lender\'s address',
                        'value' => $lender_data ? $lender_data->address : null,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'phone',
                        'name' => 'phone',
                        'label' => 'Phone',
                        'placeholder' => $lender_data ? $lender_data->phone : null,
                        'value' => $lender_data ? $lender_data->phone : null,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'email',
                        'name' => 'email',
                        'label' => 'E-mail',
                        'placeholder' => 'Lender\'s e-mail address',
                        'value' => $lender_data ? $lender_data->email : null,
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper_half">
                    <label class="flex flex-col flex-auto w-full mx-2 mt-3 sm:w-1/3 md:w-1/3 lg:w-1/5">
                        <span class="mb-2 text-sm font-medium text-gray-600">Max APR %</span>
                        <input
                                class="px-3 py-2 text-gray-700 bg-gray-100 border-gray-300 rounded focus:border-primary-dark focus:ring-primary-dark"
                                name="maxAPR"
                                type="text"
                                value="{{ $lender_data ? $lender_data->max_apr : null }}"
                                placeholder="Lender's max APR percentage">
                    </label>
                    <label class="flex flex-col flex-auto w-full mx-2 mt-3 sm:w-1/3 md:w-1/3 lg:w-1/5">
                        <span class="mb-2 text-sm font-medium text-gray-600">APR example</span>
                        <input
                                class="px-3 py-2 text-gray-700 bg-gray-100 border-gray-300 rounded focus:border-primary-dark focus:ring-primary-dark"
                                name="aprExample"
                                type="text"
                                value="{{ $lender_data ? $lender_data->apr_example : null }}"
                                placeholder="Lender's APR example">
                    </label>
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.input', [
                        'id' => 'lendersTitle',
                        'name' => 'lendersTitle',
                        'label' => 'Lender\'s title',
                        'placeholder' => 'Lender\'s title',
                        'value' => $lender_data ? $lender_data->title : null,
                    ])

                    @include('admincp.includes.textarea', [
                        'id' => 'lendersDescription',
                        'name' => 'lendersDescription',
                        'label' => 'Lender\'s description',
                        'placeholder' => 'Lender\'s description...',
                        'value' => $lender_data ? $lender_data->description : null,
                        'cols' => 30,
                        'rows' => 6,
                    ])
                </div>

                <h2 class="admin-page-fieldset-title">Options</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                        'id' => 'zero_percent_value',
                        'name' => 'zero_percent_value',
                        'label' => '0% value',
                        'placeholder' => '',
                        'value' => $lender->zero_percent_value,
                    ])
                </div>

                <div>
                    @include('admincp.includes.checkbox', [
                       'id' => 'zero_percent',
                       'name' => 'zero_percent',
                       'value' => 1,
                       'label' => '0% Lender',
                       'checked' => $lender->zero_percent,
                   ])

                    @include('admincp.includes.checkbox', [
                       'id' => 'active',
                       'name' => 'active',
                       'value' => 1,
                       'label' => 'Active',
                       'checked' => $lender->active,
                   ])
                </div>

                <div class="button-wrapper">
                    <button type="submit" class="button_submit mr-2" name="action" value="save">
                        Save
                    </button>
                    @can('delete lenders')
                        <button type="submit" class="button_danger" name="action" value="delete"
                                onclick="return confirm('Are you sure you want to delete this lender {{ $lender->name }}?');">
                            Delete
                        </button>
                    @endcan
                </div>
            </form>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="content-wrapper__form-wrapper">
            <form action="{{ route('admincp.lenders.update-campaign-id', $lender->id) }}" method="POST">
                @csrf
                @method('PUT')

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="admin-page-fieldset-wrapper">
                    <select name="campaign_id" id="campaign_id"
                            class="px-3 py-2 pr-8 mt-2 text-gray-700 bg-white border border-gray-300 rounded sm:mt-0 sm:mr-2 focus:border-primary-dark focus:ring-primary-dark">
                        <option value="">Select campaign</option>
                        @foreach ($campaigns as $campaign)
                            <option value="{{ $campaign->id }}" @if (!empty($lender->campaign_id) && $lender->campaign_id == $campaign->id) selected @endif>
                                {{ $campaign->name }} ({{ $campaign->campaign_domain }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4">
                    <button type="submit"
                            class="flex flex-row items-center order-2 px-3 py-2 text-sm font-bold text-white transition-all rounded bg-primary-normal hover:bg-primary-dark"
                            style="margin-right: 0;"
                            onclick="return confirm('Are you sure you want to update Lender campaign?');">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
