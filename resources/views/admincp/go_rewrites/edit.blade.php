@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="text-2xl font-medium text-gray-700">{{ $page_title ?? 'Edit Rewrite - ' . $go_rewrite->name }}</h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST">
                @csrf

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                       'id' => 'name',
                       'name' => 'name',
                       'label' => 'Name',
                       'placeholder' => 'Name',
                       'value' => $go_rewrite->name,
                       'required' => true,
                   ])
                </div>

                <div class="mb-8">
                    <div class="col-start-1 col-end-4 flex flex-row justify-start items-center text-xs text-gray-400"
                         title="Sample Go link">
                        <span class="mr-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                 stroke="currentColor" class="w-6 h-6">
                              <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                            </svg>
                        </span>

                        <div class="flex flex-col">
                            <p class="mb-1">
                                https://go.goodaff.com/zaimer.kz/sch?s1={{ s1() }}&s2={utm_campaign}&s3={gclid}&s4={utm_content}&s5={utm_source}&s6={position}
                            </p>

                            <p class="text-gray-300">
                                https://go.goodaff.com/zaimer.kz/<span class="text-slate-500">{path}</span>?s1=<span
                                    class="text-gray-500">{s1}</span>&s2=<span
                                    class="text-gray-500">{s2}</span>&s3=<span
                                    class="text-gray-500">{s3}</span>&s4=<span
                                    class="text-gray-500">{s4}</span>&s5=<span
                                    class="text-gray-500">{s5}</span>&s6=<span class="text-gray-500">{s6}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <h2 class="admin-page-fieldset-title">Conditions</h2>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.textarea', [
                        'id' => 'params_from',
                        'name' => 'params_from',
                        'label' => 'Params Conditions for Rewrite',
                        'rows' => 6,
                        'value' => $go_rewrite->params_from ? json_encode($go_rewrite->params_from, JSON_PRETTY_PRINT) : '',
                        'required' => true,
                    ])
                </div>

                <h2 class="admin-page-fieldset-title">Rewriting</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                       'id' => 'domain_from',
                       'name' => 'domain_from',
                       'label' => 'Domain from (Condition)',
                       'placeholder' => '',
                       'value' => $go_rewrite->domain_from,
                       'required' => false,
                   ])

                    @include('admincp.includes.input', [
                       'id' => 'domain_to',
                       'name' => 'domain_to',
                       'label' => 'Domain to (Action)',
                       'placeholder' => '',
                       'value' => $go_rewrite->domain_to,
                       'required' => false,
                   ])
                </div>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                       'id' => 'path_ending_from',
                       'name' => 'path_ending_from',
                       'label' => 'Path Ending from (Condition)',
                       'placeholder' => '',
                       'value' => $go_rewrite->path_ending_from,
                       'required' => false,
                   ])

                    @include('admincp.includes.input', [
                       'id' => 'path_ending_to',
                       'name' => 'path_ending_to',
                       'label' => 'Path Ending to (Action)',
                       'placeholder' => '',
                       'value' => $go_rewrite->path_ending_to,
                       'required' => false,
                   ])
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.textarea', [
                        'id' => 'params_to',
                        'name' => 'params_to',
                        'label' => 'Rewrite Params to (Action)',
                        'rows' => 6,
                        'value' => $go_rewrite->params_to ? json_encode($go_rewrite->params_to, JSON_PRETTY_PRINT) : '',
                        'required' => true,
                    ])
                </div>

                @include('admincp.includes.checkbox', [
                   'id' => 'active',
                   'name' => 'active',
                   'value' => 1,
                   'label' => 'Active',
                   'checked' => old('active', $go_rewrite->active),
               ])

                <div class="button-wrapper">
                    @if ($go_rewrite && $go_rewrite->id)
                        <a href="{{ route('admincp.go-rewrites.preview', $go_rewrite->id) }}"
                           class="button_secondary mr-2"
                           target="_blank">
                            Preview
                        </a>
                    @endif

                    <button type="submit" class="button_submit">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    @can('delete go-rewrites')
        @if ($go_rewrite && $go_rewrite->id)
            <div class="content-wrapper">
                <div class="content-wrapper__form-wrapper">
                    <h2 class="admin-page-fieldset-title">Delete</h2>
                    <div class="admin-page-fieldset-wrapper_last">
                        <form action="{{ route('admincp.go-rewrites.destroy', $go_rewrite) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="button_danger" style="margin-right: 0;"
                                    onclick="return confirm('Are you sure you want to delete this Rewrite?');">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endcan

@endsection
