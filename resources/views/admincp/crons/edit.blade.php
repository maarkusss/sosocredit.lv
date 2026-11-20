@php
/** @var \App\Models\Cron $cron */
@endphp

@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="text-2xl font-medium text-gray-700">{{ $page_title ?? 'Edit CRON - ' . $cron->signature }}</h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST">
                @csrf

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                       'id' => 'signature',
                       'name' => 'signature',
                       'label' => 'Signature',
                       'placeholder' => '',
                       'value' => $cron->signature,
                       'disabled' => true,
                       'readonly' => true,
                   ])
                </div>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                       'id' => 'description',
                       'name' => 'description',
                       'label' => 'Description',
                       'placeholder' => 'Description',
                       'value' => $cron->description,
                       'required' => true,
                   ])
                </div>

                <h2 class="admin-page-fieldset-title">Settings</h2>

                @foreach ($cron->settings as $key => $value)
                    <div class="admin-page-fieldset-wrapper">
                        @include('admincp.includes.input', [
                           'id' => 'settings_' . $key,
                           'name' => 'settings[' . $key . ']',
                           'label' => $key,
                           'value' => $value,
                           'required' => false,
                       ])
                    </div>
                @endforeach

                <h2 class="admin-page-fieldset-title">Options</h2>

                <div>
                    <input type="hidden" name="active" value="0" />
                    @include('admincp.includes.checkbox', [
                       'id' => 'active',
                       'name' => 'active',
                       'value' => 1,
                       'label' => 'Active',
                       'checked' => $cron->active,
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
