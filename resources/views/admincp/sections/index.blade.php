@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Sections' }}</h1>

            @can('add sections')
                <a href="{{ route('admincp.sections.create') }}"
                    class="flex flex-row items-center px-3 py-2 text-sm font-medium text-white bg-gray-700 rounded hover:bg-gray-800">
                    <svg class="w-5 h-5 mr-1 fill-current">
                        <path
                            d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z" />
                    </svg>
                    <span>Add section</span>
                </a>
            @endcan
        </div>

        <div class="content-wrapper__form-wrapper">
            <div class="overflow-x-auto whitespace-nowrap">
                <form method="POST">
                    <table class="table">
                        <thead>
                            <tr class="border-b">
                                <th class="table__column">Order</th>
                                <th class="table__column">Name</th>
                                <th class="table__column">Route name</th>
                                <th class="table__column">Main Page</th>
                                <th class="table__column">Last edited</th>
                                <th class="table__column">Created</th>
                                <th class="table__column">View</th>
                                <th class="table__column">Active</th>
                                @can('edit sections')
                                    <th class="table__column">Action</th>
                                @endcan
                            </tr>
                        </thead>

                        @csrf
                        @forelse($sections as $section)
                            <tbody>
                                <tr class="table__row">
                                    <td class="table__cell">
                                        <label>
                                            <input class="input-wrap__input_short"
                                                name="section[order][{{ $section->id }}]" type="number" step="1"
                                                autocomplete="off" value="{{ $section->order }}" />
                                        </label>
                                    </td>
                                    <td class="table__cell">
                                        <div class="flex flex-col items-center justify-center">
                                            <a class="underline hover:text-primary-dark"
                                                href="{{ route('admincp.sections.edit', $section->id) }}">
                                                {{ $section->name }}
                                            </a>
                                            @if (\App\Models\Section::where('parent_section_id', $section->id)->count() > 0)
                                                <a href="#"
                                                    onclick="openSubsections('subsections_{{ $section->id }}');">
                                                    <svg class="w-5 h-5 ml-1 fill-current">
                                                        <path
                                                            d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z" />
                                                    </svg>
                                                </a>
                                            @endif

                                            @if (!empty($section->template))
                                                <span class="mt-1 badge opacity-60"
                                                    title="Template">[{{ $section->template }}]</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="table__cell">
                                        <span class="text-gray-400">{{ $section->route_name }}</span>
                                    </td>
                                    <td class="table__cell">
                                        @if ($section->main_page)
                                            <svg class="w-5 h-5 mx-auto text-green-500 fill-current">
                                                <path
                                                    d="M15.592 6.008a.833.833 0 0 0-1.184 0L8.2 12.225 5.592 9.608a.852.852 0 0 0-1.184 1.225l3.2 3.2a.833.833 0 0 0 1.184 0l6.8-6.8a.834.834 0 0 0 0-1.225z" />
                                            </svg>
                                        @endif
                                    </td>
                                    <td class="table__cell">
                                        <span>{{ $section->updated_at->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="table__cell">
                                        <span>{{ $section->created_at->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="table__cell">
                                        @if ($section->active && !$section->isHome())
                                            <a href="{{ route('section', ['name' => $section->route_name]) }}"
                                                title="View section on frontend" target="_blank">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="inline w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                                </svg>
                                            </a>
                                        @endif
                                    </td>
                                    <td class="table__cell">
                                        <label class="flex justify-center">
                                            <input type="hidden" name="section[active][{{ $section->id }}]"
                                                value="0">
                                            <input
                                                class="w-5 h-5 border-gray-300 rounded focus:ring-primary-dark text-primary-normal"
                                                name="section[active][{{ $section->id }}]" value="1" type="checkbox"
                                                @if ($section->active) checked @endif>
                                        </label>
                                    </td>
                                    @can('edit sections')
                                        <td class="text-center table__cell">
                                            <button
                                                type="button"
                                                class="text-red-600 hover:text-red-800"
                                                data-delete-url="{{ route('admincp.sections.destroy', $section->id) }}"
                                                onclick="deleteSection(this)"
                                            >
                                                <svg class="w-6 h-6 mt-1 shrink-0" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </td>
                                    @endcan
                                </tr>
                                @if ($subsections = $section->children()->with('parent')->get())
                                    @foreach ($subsections as $subsection)
                                        <tr class="hidden text-sm bg-gray-100 border-b hover:bg-gray-300"
                                            id="subsections_{{ $section->id }}">
                                            <td class="table__cell">
                                                <label>
                                                    <input class="w-16 px-2 py-1 text-center border rounded"
                                                        name="subsection[order][{{ $subsection->id }}]" type="number"
                                                        step="1" value="{{ $subsection->order }}"
                                                        autocomplete="off" />
                                                </label>
                                            </td>
                                            <td class="table__cell">
                                                <a class="underline hover:text-primary-dark"
                                                    href="{{ route('admincp.sections.edit', $subsection->id) }}">
                                                    {{ $subsection->name }}
                                                </a>

                                                @if (!empty($subsection->template))
                                                    <br />
                                                    <span class="mt-1 badge opacity-60"
                                                        title="Template">[{{ $subsection->template }}]</span>
                                                @endif
                                            </td>
                                            <td class="table__cell">
                                                <span class="text-gray-400">{{ $subsection->route_name }}</span>
                                            </td>
                                            <td class="table__cell">
                                                @if ($subsection->main_page)
                                                    <svg class="w-5 h-5 mx-auto text-green-500 fill-current">
                                                        <path
                                                            d="M15.592 6.008a.833.833 0 0 0-1.184 0L8.2 12.225 5.592 9.608a.852.852 0 0 0-1.184 1.225l3.2 3.2a.833.833 0 0 0 1.184 0l6.8-6.8a.834.834 0 0 0 0-1.225z" />
                                                    </svg>
                                                @endif
                                            </td>
                                            <td class="table__cell">
                                                <span>{{ $subsection->updated_at->format('d/m/Y H:i') }}</span>
                                            </td>
                                            <td class="table__cell">
                                                <span>{{ $subsection->created_at->format('d/m/Y H:i') }}</span>
                                            </td>
                                            <td class="table__cell">
                                                @if ($subsection->active && !$subsection->isHome())
                                                    <a href="{{ route('section.deep', [
                                                        'name' => $subsection->parent->route_name,
                                                        'deep_name' => $subsection->route_name,
                                                    ]) }}"
                                                        title="View subection on frontend" target="_blank">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="inline w-6 h-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                                        </svg>
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="table__cell">
                                                <label class="flex justify-center">
                                                    <input type="hidden" value="0"
                                                        name="subsection[active][{{ $subsection->id }}]">
                                                    <input
                                                        class="border-gray-300 rounded focus:ring-primary-dark text-primary-normal"
                                                        name="subsection[active][{{ $subsection->id }}]" value="1"
                                                        type="checkbox" @if ($subsection->active) checked @endif>
                                                </label>
                                            </td>
                                            @can('edit sections')
                                                <td class="text-center table__cell">
                                                    <button
                                                        type="button"
                                                        class="text-red-600 hover:text-red-800"
                                                        data-delete-url="{{ route('admincp.sections.destroy', $subsection->id) }}"
                                                        onclick="deleteSection(this)"
                                                    >
                                                        <svg class="w-6 h-6 mt-1 shrink-0"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        @empty
                            <tbody>
                                <tr>
                                    <td colspan="3">
                                        <p class="p-2">
                                            No Sections found.
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        @endforelse
                    </table>

                    @can('edit sections')
                        @if ($sections->count() > 0)
                            <div class="button-wrapper">
                                <button type="submit" class="button_submit">
                                    Save
                                </button>
                            </div>
                        @endif
                    @endcan

                </form>
            </div>
        </div>
    </div>

    <form id="delete-section-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('javascript')
    <script>
        function openSubsections(e) {
            const subSections = document.querySelectorAll("#" + e);
            for (let i = 0; i < subSections.length; i++) {
                subSections[i].classList.toggle("hidden");
            }
        }
        function deleteSection(button) {
            if (!confirm('Are you sure you want to delete this section?')) {
                return;
            }

            const form = document.getElementById('delete-section-form');

            form.action = button.dataset.deleteUrl;
            form.submit();
        }
    </script>
@endsection
