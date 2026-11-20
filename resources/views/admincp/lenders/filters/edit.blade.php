@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="text-2xl font-medium text-gray-700">
                {{ $page_title ?? 'Edit Lender Filter - ' . $filter->utm_source . ($filter->utm_campaign ? ':' . $filter->utm_campaign : '') }}
                @if (!empty($filter->source))
                    <span class="ml-2 badge badge-secondary">
                        {{ $filter->source }}
                    </span>
                @endif
            </h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST">
                @csrf

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                       'id' => 'utm_source',
                       'name' => 'utm_source',
                       'label' => 'UTM Source',
                       'placeholder' => 'utm_source',
                       'value' => old('utm_source', $filter->utm_source),
                       'required' => true,
                       'disabled' => !empty($filter->source) && $filter->source === 'api',
                   ])

                    @include('admincp.includes.input', [
                       'id' => 'utm_campaign',
                       'name' => 'utm_campaign',
                       'label' => 'UTM Campaign',
                       'placeholder' => 'utm_campaign',
                       'value' => old('utm_campaign', $filter->utm_campaign),
                       'required' => false,
                       'disabled' => !empty($filter->source) && $filter->source === 'api',
                   ])

                    @include('admincp.includes.input', [
                       'id' => 'token',
                       'name' => 'token',
                       'label' => 'Token',
                       'placeholder' => 'token',
                       'value' => old('token', $filter->token),
                       'required' => false,
                       'disabled' => !empty($filter->source) && $filter->source === 'api',
                   ])
                </div>

                <h2 class="admin-page-fieldset-title mb-2">❌ Lenders: Exclusion List</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($lenders as $lender)
                        @include('admincp.includes.checkbox', [
                           'id' => 'rules_exclude_lender_' . $lender->id,
                           'name' => 'rules[exclude_campaigns][]',
                           'value' => $lender->campaign_id,
                           'label' => $lender->name . (empty($lender->active) ? ' (Inactive)' : ''),
                           'checked' => !empty($filter->rules['exclude_campaigns']) ? in_array($lender->campaign_id, (array) $filter->rules['exclude_campaigns']) : false,
                           'class' => !empty($lender->active) ? '' : 'checkbox-wrap__label_muted',
                       ])
                    @endforeach
                </div>

                <h2 class="admin-page-fieldset-title mt-8 mb-2">🔁 Lenders: Frequencies</h2>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-x-12 gap-y-1">
                    @foreach ($lenders as $lender)
                        @include('admincp.includes.input', [
                            'id' => 'rules_set_campaign_frequency_' . $lender->id,
                            'name' => 'rules[set_campaigns_frequencies][' . $lender->campaign_id . ']',
                            'value' => !empty($filter->rules['set_campaigns_frequencies']) ? ($filter->rules['set_campaigns_frequencies']->{$lender->campaign_id} ?? null) : null,
                            'label' => $lender->name . (empty($lender->active) ? ' (Inactive)' : ''),
                            'class' => !empty($lender->active) ? '' : 'checkbox-wrap__label_muted',
                            'label_class' => !empty($lender->active) ? '' : 'checkbox-wrap__label_muted',
                       ])
                    @endforeach
                </div>

                <h2 class="admin-page-fieldset-title mt-8">Options</h2>

                @include('admincp.includes.checkbox', [
                   'id' => 'active',
                   'name' => 'active',
                   'value' => 1,
                   'label' => 'Active',
                   'checked' => $filter->active,
               ])

                <div class="button-wrapper">
                    @if (!empty($filter) && !empty($filter->id) && !empty($filter->url) && $filter->active)
                        <a href="{{ $filter->url }}"
                           class="button_secondary mr-2"
                           target="_blank">
                            View Offer page
                        </a>
                    @endif

                    <button type="submit" class="button_submit">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    @can('delete lenders')
        @if ($filter && $filter->id)
            <div class="content-wrapper">
                <div class="content-wrapper__form-wrapper">
                    <h2 class="admin-page-fieldset-title">Delete</h2>
                    <div class="admin-page-fieldset-wrapper_last">
                        <form action="{{ route('admincp.lenders.filters.destroy', $filter) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="button_danger" style="margin-right: 0;"
                                    onclick="return confirm('Are you sure you want to delete this lender filter?');">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endcan

@endsection
