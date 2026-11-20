@extends('layouts.admin')

@section('head')
    <link rel="stylesheet"
          href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/styles/default.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.6.0/highlight.min.js"></script>
@endsection

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Go Rewrites' }}</h1>

            @can('add go-rewrites')
                <a class="button_secondary"
                   href="{{ route('admincp.go-rewrites.create') }}">
                    <svg class="w-5 h-5 mr-1 fill-current">
                        <path d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z"/>
                    </svg>

                    <span>Add Rewrite</span>
                </a>
            @endcan
        </div>

        @if (config('app.go_rewrites_enabled') !== true)
            <p class="alert alert-danger w-full">
                Go Rewrites functionality is disabled. Please enable it in config.
            </p>
        @endif

        <p class="mt-1 mb-4">
            Go Rewrites section allows adding rules to the generated <code>go link</code> before customer gets redirected to it.
            Like for certain <code>s2</code> value you can override the <code>s1</code> value submitted to the final link.
        </p>

        <div class="content-wrapper__form-wrapper">
            <form method="POST">
                @csrf
                <div class="overflow-x-auto whitespace-nowrap">
                    <table class="table">
                        <thead>
                        <tr class="border-b">
                            <th class="table__column text-left">Name</th>
                            <th class="table__column text-left">Conditions (Params)</th>
                            <th class="table__column text-left">Rewrite Params To</th>
                            <th class="table__column text-left">Domain/Path Ending From</th>
                            <th class="table__column text-left">Domain/Path Ending To</th>
                            <th class="table__column">Active</th>
                            <th class="table__column">Edit</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($go_rewrites as $go_rewrite)
                            <tr class="table__row">
                                <td class="table__cell text-left">
                                    <a href="{{ route('admincp.go-rewrites.edit', $go_rewrite->id) }}"
                                       class="link_default">
                                        {{ $go_rewrite->name }}
                                    </a>
                                </td>
                                <td class="table__cell text-left text-xs">
                                    <pre><code
                                            class="highlightJs language-json">{{ json_encode($go_rewrite->params_from, JSON_PRETTY_PRINT) }}</code></pre>
                                </td>
                                <td class="table__cell text-left text-xs">
                                    @if (!empty($go_rewrite->params_to))
                                        <pre><code
                                                class="highlightJs language-json">{{ json_encode($go_rewrite->params_to, JSON_PRETTY_PRINT) }}</code></pre>
                                    @endif
                                </td>
                                <td class="table__cell text-left text-xs">
                                    <span class="block mt-2">{{ $go_rewrite->domain_from ?: '' }}</span>
                                    <span class="block mt-0">{{ $go_rewrite->path_ending_from ? '/' . $go_rewrite->path_ending_from : '' }}</span>
                                </td>
                                <td class="table__cell text-left text-xs">
                                    <span class="block mt-2">{{ $go_rewrite->domain_to ?: '' }}</span>
                                    <span class="block mt-0">{{ $go_rewrite->path_ending_to ? '/' . $go_rewrite->path_ending_to : '' }}</span>
                                </td>
                                <td class="table__cell">
                                    <label class="flex justify-center">
                                        <input type="hidden"
                                               name="active[{{ $go_rewrite->id }}]"
                                               value="0">
                                        <input
                                            class="w-5 h-5 border-gray-300 rounded text-primary-normal focus:ring-primary-dark"
                                            name="active[{{ $go_rewrite->id }}]"
                                            type="checkbox"
                                            value="1"
                                            @if ($go_rewrite->active) checked @endif>
                                    </label>
                                </td>
                                <td class="table__cell">
                                    <a href="{{ route('admincp.go-rewrites.edit', $go_rewrite->id) }}"
                                       class="underline">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mx-auto">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <p class="p-2">
                                        No Go Rewrites found.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    {{ $go_rewrites->links('admincp.includes.pagination') }}
                </div>

                @if (!empty($go_rewrites) && $go_rewrites->isNotEmpty())
                    <div class="flex justify-end mt-6">
                        <a href="{{ route('admincp.go-rewrites.preview-all-active') }}"
                           class="button_secondary mr-2"
                           target="_blank">
                            Preview All Active
                        </a>

                        <button class="button_submit"
                                type="submit"
                                name="action"
                                value="save">
                            Save
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        hljs.configure({
            cssSelector: '.highlightJs'
        });

        hljs.highlightAll();</script>
@endsection
