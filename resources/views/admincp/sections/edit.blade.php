@extends('layouts.admin')

@section('head')
    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/4.18.0/full-all/ckeditor.js"></script>
@endsection

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Edit section - ' . $section->name }}</h1>

            @if (request()->history)
                <span class="font-bold text-red-400">Ierakstu izveidoja - {{ $section->user->first_name }}
                    {{ $section->user->last_name }}</span>
            @endif

            <div>
                <form>
                    <select
                        class="px-3 py-2 mt-2 text-gray-700 bg-white border-gray-300 rounded sm:mt-0 focus:border-primary-dark focus:ring-primary-dark"
                        name="history" onchange="this.form.submit()">
                        <option selected value="">History
                        </option>
                        @foreach ($history as $his)
                            <option value="{{ $his->id }}" @if (request()->history == $his->id) selected @endif>
                                {{ date('d.m.Y H:i:s', strtotime($his->created_at)) }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST">
                @csrf

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="admin-page-fieldset-wrapper" style="margin-bottom: 1rem;">
                    <label class="flex flex-col w-full">
                        <span class="mb-2 text-sm font-medium text-gray-600 appearance-none">Parent Section</span>
                        <select
                            class="px-3 py-2 text-gray-700 bg-gray-100 border-gray-300 rounded focus:border-primary-dark focus:ring-primary-dark"
                            name="section">
                            <option value="0">Select a section</option>
                            @foreach ($sections as $sec)
                                <option value="{{ $sec->id }}" @if ($section->parent_section_id == $sec->id) selected @endif>
                                    {{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </label>

                    @include('admincp.includes.select', [
                        'id' => 'template',
                        'name' => 'template',
                        'label' => 'Template',
                        'default_option' => 'Default',
                        'options' => $templates,
                        'value' => $section->template,
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'name',
                        'name' => 'name',
                        'label' => 'Name',
                        'placeholder' => 'Section\'s name',
                        'value' => $section->name,
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'routing_name',
                        'name' => 'routing_name',
                        'label' => 'Routing name (URL slug)',
                        'placeholder' => 'Section\'s routing name',
                        'value' => $section->route_name,
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'title',
                        'name' => 'title',
                        'label' => '<title>',
                        'placeholder' => 'Section\'s title',
                        'value' => $section->title,
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'link',
                        'name' => 'link',
                        'label' => 'Redirect link',
                        'placeholder' => 'Section\'s redirect link',
                        'value' => $section->redirect_link,
                        'required' => false,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'common_name',
                        'name' => 'common_name',
                        'label' => 'Common name',
                        'placeholder' => 'Common name - for localizations',
                        'value' => $section->common_name,
                        'required' => false,
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.textarea', [
                        'id' => 'description',
                        'name' => 'description',
                        'label' => 'Google description',
                        'placeholder' => 'Section\'s Google description',
                        'value' => $section->description,
                        'required' => false,
                        'class' => 'auto_expand',
                        'container_class' => 'relative',
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'keywords',
                        'name' => 'keywords',
                        'label' => 'Google keywords',
                        'placeholder' => 'Section\'s Google keywords',
                        'value' => $section->keywords,
                        'required' => false,
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    <h2 class="admin-page-fieldset-title">Section source (Section specific HTML in head tag)</h2>

                    @include('admincp.includes.textarea', [
                        'id' => 'sectionSource',
                        'name' => 'sectionSource',
                        'label' => '',
                        'placeholder' => 'Section source text',
                        'value' => $section->source,
                        'required' => false,
                    ])
                </div>

                <h2 class="admin-page-fieldset-title">Section H1, H1 description</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                        'id' => 'h1',
                        'name' => 'h1',
                        'label' => 'H1 tag',
                        'placeholder' => '<h1> tag',
                        'value' => $section->h1,
                        'required' => false,
                    ])

                    @include('admincp.includes.textarea', [
                        'id' => 'h1_description',
                        'name' => 'h1_description',
                        'label' => 'H1 Description',
                        'placeholder' => 'Section H1 Description',
                        'value' => $section->h1_description,
                        'required' => false,
                        'class' => 'auto_expand',
                        'container_class' => 'relative',
                    ])
                </div>

                <div class="admin-page-fieldset-wrapper_full">
                    @include('admincp.includes.textarea', [
                        'id' => 'sectionText',
                        'name' => 'sectionText',
                        'label' => 'Section text',
                        'placeholder' => 'Section text',
                        'value' => $section->text,
                        'required' => false,
                    ])
                </div>

                @if (!empty($section->json))
                    <h2 class="admin-page-fieldset-title">Blocks</h2>

                    @foreach ($section->json as $key => $value)
                        @if ($key === 'block1_set')
                            @if (!empty($applicant_form_block1_sets))
                                <div class="admin-page-fieldset-wrapper">
                                    <div>
                                        <label class="input-wrap__label">{{ $key }}</label>
                                        <br />
                                        @foreach ($applicant_form_block1_sets as $set)
                                            <input id="set_{{ $set }}" type="radio"
                                                name="json[{{ $key }}]" value="{{ $set }}"
                                                @if ($section->json[$key] == $set) checked @endif />
                                            <label for="set_{{ $set }}">{{ $set }}</label>
                                            <br />
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @elseif ($key === 'block0_options')
                            @if (!empty($value))
                                <label class="input-wrap__label">{{ $section->h1_tag() }}</label>
                                <br />
                                <div class="pl-4">
                                    @foreach ($value as $i => $option)
                                        <div class="admin-page-fieldset-wrapper">
                                            @include('admincp.includes.input', [
                                                'name' => 'json[' . $key . '][]',
                                                'id' => 'block0_option_' . $i,
                                                'class' => 'text-sm',
                                                'label' => '',
                                                'value' => $option,
                                                'required' => false,
                                            ])
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @elseif ($key === 'block1_options')
                            @if (!empty($value))
                                <div class="pl-4">
                                    @foreach ($value as $i => $option)
                                        <div class="admin-page-fieldset-wrapper">
                                            @include('admincp.includes.input', [
                                                'name' => 'json[' . $key . '][]',
                                                'id' => 'block1_option_' . $i,
                                                'class' => 'text-sm',
                                                'label' => '',
                                                'value' => $option,
                                                'required' => false,
                                            ])
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="admin-page-fieldset-wrapper">
                                @include('admincp.includes.input', [
                                    'id' => 'json_' . $key,
                                    'name' => 'json[' . $key . ']',
                                    'label' => $key,
                                    'value' => $value,
                                    'required' => false,
                                ])
                            </div>
                        @endif
                    @endforeach
                @endif

                <h2 class="admin-page-fieldset-title">Options</h2>

                <div>
                    @include('admincp.includes.checkbox', [
                        'id' => 'active',
                        'name' => 'active',
                        'value' => 1,
                        'label' => 'Active',
                        'checked' => !request()->history && $section->active,
                    ])

                    @include('admincp.includes.checkbox', [
                        'id' => 'main_page',
                        'name' => 'main_page',
                        'value' => 1,
                        'label' => 'Main Page',
                        'checked' => !request()->history && $section->main_page,
                    ])

                    {{--                    @include('admincp.includes.checkbox', [ --}}
                    {{--                       'id' => 'in_header_menu', --}}
                    {{--                       'name' => 'in_header_menu', --}}
                    {{--                       'value' => 1, --}}
                    {{--                       'label' => 'In header menu', --}}
                    {{--                       'checked' => $section->in_header_menu, --}}
                    {{--                   ]) --}}

                    {{--                    @include('admincp.includes.checkbox', [ --}}
                    {{--                       'id' => 'in_footer_menu', --}}
                    {{--                       'name' => 'in_footer_menu', --}}
                    {{--                       'value' => 1, --}}
                    {{--                       'label' => 'In footer menu', --}}
                    {{--                       'checked' => $section->in_footer_menu, --}}
                    {{--                   ]) --}}
                </div>

                <div class="button-wrapper">
                    @if ($section->active && !$section->isHome() && !empty($section->canonical_url()))
                        <a href="{{ $section->canonical_url() }}" class="button_secondary mr-2" target="_blank">
                            View section page
                        </a>
                    @endif

                    <button type="submit" class="button_submit">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (!empty($section) && !empty($section->id) && $section->template != 'home' && count(available_locales()) > 1)
        @include('admincp.connected_urls.includes.section')
    @endif

@endsection

@section('javascript')
    <script>
        CKEDITOR.replace('sectionText', {
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
