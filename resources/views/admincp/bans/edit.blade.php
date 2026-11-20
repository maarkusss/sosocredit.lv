@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="text-2xl font-medium text-gray-700">{{ $page_title ?? 'Edit ban - ' . $ban->ip }}</h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST">
                @csrf

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                       'id' => 'ip',
                       'name' => 'ip',
                       'label' => 'IP',
                       'placeholder' => 'Banned IP',
                       'value' => $ban->ip,
                       'required' => true,
                   ])

                    @include('admincp.includes.input', [
                        'id' => 'reason',
                        'name' => 'reason',
                        'label' => 'Reason',
                        'placeholder' => 'Reason for banning',
                        'value' => $ban->reason,
                        'required' => false,
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

    @can('delete bans')
        @if ($ban && $ban->id)
            <div class="content-wrapper">
                <div class="content-wrapper__form-wrapper">
                    <h2 class="admin-page-fieldset-title">Delete</h2>
                    <div class="admin-page-fieldset-wrapper_last">
                        <form action="{{ route('admincp.bans.destroy', $ban) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="button_danger" style="margin-right: 0;"
                                    onclick="return confirm('Are you sure you want to delete this ban?');">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endcan

@endsection
