@extends('layouts.admin')

@section('head')
    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/4.18.0/full-all/ckeditor.js"></script>
@endsection

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Edit loan type - ' . $loantype->name }}</h1>


            @if (!empty(request()->history) || (!empty($history) && $history->count() > 0))
                @if (request()->history)
                    <span class="font-bold text-red-400">
                        Ierakstu izveidoja - {{ $loantype->user->first_name }} {{ $loantype->user->last_name }}
                    </span>
                @endif

                <form>
                    <select
                        class="px-3 py-2 mt-2 text-gray-700 bg-white border-gray-300 rounded sm:mt-0 focus:border-primary-dark focus:ring-primary-dark"
                        name="history" onchange="this.form.submit()">
                        <option value="">History</option>
                        @foreach ($history as $his)
                            <option value="{{ $his->id }}" @if (request()->history == $his->id) selected @endif>
                                {{ date('d.m.Y H:i:s', strtotime($his->created_at)) }}</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST">
                @csrf

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                        'id' => 'name',
                        'name' => 'name',
                        'label' => 'Name',
                        'placeholder' => 'Loan type\'s name',
                        'value' => old('name', $loantype->name),
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'route_name',
                        'name' => 'route_name',
                        'label' => 'Routing name (URL slug)',
                        'placeholder' => 'Loan type\'s route name',
                        'value' => old('route_name', $loantype->route_name),
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'title',
                        'name' => 'title',
                        'label' => '<title>',
                        'placeholder' => 'Loan type\'s title',
                        'value' => old('title', $loantype->title),
                        'required' => true,
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.textarea', [
                        'id' => 'description',
                        'name' => 'description',
                        'label' => 'Google description',
                        'placeholder' => 'Loan type\'s Google description',
                        'value' => old('description', $loantype->description),
                        'required' => false,
                        'class' => 'auto_expand',
                        'container_class' => 'relative',
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'keywords',
                        'name' => 'keywords',
                        'label' => 'Google keywords',
                        'placeholder' => 'Loan type\'s Google keywords',
                        'value' => old('keywords', $loantype->keywords),
                        'required' => false,
                    ])
                </div>

                <h2 class="admin-page-fieldset-title">Loantype H1, H1 description</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                        'id' => 'h1',
                        'name' => 'h1',
                        'label' => 'H1 tag',
                        'placeholder' => '<h1> tag',
                        'value' => old('h1', $loantype->h1),
                        'required' => false,
                    ])

                    @include('admincp.includes.textarea', [
                        'id' => 'h1_description',
                        'name' => 'h1_description',
                        'label' => 'H1 Description',
                        'placeholder' => 'Loantype H1 Description',
                        'value' => old('h1_description', $loantype->h1_description),
                        'required' => false,
                        'class' => 'auto_expand',
                        'container_class' => 'relative',
                    ])
                </div>

                <div class="mb-4">
                    @include('admincp.includes.textarea', [
                        'id' => 'text',
                        'name' => 'text',
                        'label' => 'Section text',
                        'placeholder' => 'Loan type text...',
                        'cols' => 30,
                        'rows' => 6,
                        'value' => old('text', $loantype->text),
                    ])
                </div>

                <h2 class="admin-page-fieldset-title">Options</h2>
                @include('admincp.includes.checkbox', [
                    'id' => 'active',
                    'name' => 'active',
                    'value' => 1,
                    'label' => 'Active',
                    'checked' => $loantype->active && !request()->history,
                ])

                <div class="button-wrapper">
                    @if ($loantype->active && !empty($loantype->canonical_url()))
                        <a href="{{ $loantype->canonical_url() }}" class="button_secondary mr-2" target="_blank">
                            View loantype landing
                        </a>
                    @endif

                    <button type="submit" class="button_submit">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (!empty($loantype) && !empty($loantype->id) && count(available_locales()) > 1)
        @include('admincp.connected_urls.includes.loantype')
    @endif

    @can('delete loantypes')
        @if ($loantype && $loantype->id)
            <div class="content-wrapper">
                <div class="content-wrapper__form-wrapper">
                    <h2 class="admin-page-fieldset-title">Delete</h2>
                    <div class="admin-page-fieldset-wrapper_last">
                        <form action="{{ route('admincp.loantypes.destroy', $loantype) }}" method="POST">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="button_danger" style="margin-right: 0;"
                                onclick="return confirm('Are you sure you want to delete this loantype?');">
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
        CKEDITOR.replace('text', {
            disallowedContent: '*[bis_size]',
            height: 600,
        });

        const textareaElements = document.querySelectorAll(".auto_expand");

        textareaElements.forEach((element) => {
            element.style.cssText = "height: 42px; position: absolute; top: 28px; left: 0px; right: 0px;";
            element.parentNode.style.cssText = "height: 42px;";

            element.addEventListener("focus", (e) => {
                if (e.target.scrollHeight < 42) {
                    e.target.style.height = "42px";
                } else {
                    e.target.style.height = `${e.target.scrollHeight}px`;
                }

            })

            element.addEventListener("blur", (e) => {
                e.target.style.height = "42px";
            })
        })
    </script>
@endsection
