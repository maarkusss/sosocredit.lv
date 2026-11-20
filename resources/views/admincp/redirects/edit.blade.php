@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="text-2xl font-medium text-gray-700">
                {{ $page_title ?? 'Edit redirect - ' . $redirect->from }}
            </h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form action="{{ $redirect && $redirect->id ? route('admincp.redirects.update', $redirect) : route('admincp.redirects.store') }}" method="POST">
                @csrf

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                       'id' => 'from',
                       'name' => 'from',
                       'label' => 'From',
                       'value' => old('from', $redirect->from),
                       'required' => true,
                   ])

                    @include('admincp.includes.input', [
                        'id' => 'to',
                        'name' => 'to',
                        'label' => 'To',
                        'value' => old('to', $redirect->to),
                        'required' => true,
                    ])

                    <div class="input-wrap">
                        <label for="code" class="input-wrap__label">
                            Code
                        </label>
                        <select class="w-full px-3 py-2 mt-2 text-gray-700 bg-white border-gray-300 rounded sm:mt-0 focus:border-primary-dark focus:ring-primary-dark"
                                name="code" id="code">
                            @foreach($codes as $code)
                                <option value="{{ $code }}"
                                        @if (old('code', $redirect->code) == $code) selected @endif>
                                    {{ $code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="button-wrapper">
                    <button type="submit" class="button_submit">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    @can('delete redirects')
        @if ($redirect && $redirect->id)
            <div class="content-wrapper">
                <div class="content-wrapper__form-wrapper">
                    <h2 class="admin-page-fieldset-title">Delete</h2>
                    <div class="admin-page-fieldset-wrapper_last">
                        <form action="{{ route('admincp.redirects.destroy', $redirect) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="button_danger" style="margin-right: 0;"
                                    onclick="return confirm('Are you sure you want to delete this redirect?');">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endcan

@endsection
