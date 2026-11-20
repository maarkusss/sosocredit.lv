@php
use App\Models\Block;
use Illuminate\Support\Str;

/** @var array<Block> $blocks */
@endphp

@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Blocks' }}</h1>

            <div class="flex flex-col items-start sm:flex-row sm:items-center">
                <a href="{{ route('admincp.blocks.create') }}"
                   class="button_secondary">
                    <svg class="w-5 h-5 mr-1 fill-current">
                        <path d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z"/>
                    </svg>
                    <span>Add block</span>
                </a>
            </div>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form action="?" method="GET">
                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                       'id' => 'key',
                       'name' => 'key',
                       'label' => 'Key',
                       'value' => old('key', request()->get('key') ?? ''),
                   ])

                    @include('admincp.includes.input', [
                        'id' => 'tag',
                        'name' => 'tag',
                        'label' => 'Tag',
                        'value' => old('tag', request()->get('tag') ?? ''),
                    ])

                    <div class="flex flex-col justify-end pb-1">
                        <button type="submit" class="button_secondary w-fit">
                            Search
                        </button>
                    </div>
                </div>

                <div>
                    <p><strong>Blocks found:</strong> {{ $blocks->total() }}</p>
                </div>
            </form>
        </div>

        <div class="content-wrapper__form-wrapper">
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table">
                    <thead>
                    <tr class="border-b">
                        <th class="table__column" width="200">Key</th>
                        <th class="table__column">Value</th>
                        <th class="table__column">Tag</th>
                        <th class="table__column"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($blocks as $block)
                        <tr class="table__row">
                            <td class="table__cell">
                                <a href="{{ route('admincp.blocks.edit', $block) }}" class="underline">
                                    {{ $block->key }}
                                </a>
                            </td>
                            <td class="table__cell">
                                @if (Str::length($block->value) < 70)
                                    {{ $block->value }}
                                @else
                                    {{ Str::substr($block->value, 0, 75) }}..
                                @endif
                            </td>
                            <td class="table__cell">
                                @if (!empty($block->tag))
                                    <span class="badge badge-secondary">
                                        {{ $block->tag }}
                                    </span>
                                @endif
                            </td>
                            <td class="table__cell">
                                <form action="{{ route('admincp.blocks.destroy', $block) }}"
                                      method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to delete this block?');">
                                        <svg class="w-5 h-5 text-red-600 fill-current hover:text-red-700">
                                            <path d="M8.333 15a.833.833 0 0 0 .834-.833v-5a.833.833 0 1 0-1.667 0v5a.833.833 0 0 0 .833.833zm8.334-10h-3.334v-.833a2.5 2.5 0 0 0-2.5-2.5H9.167a2.5 2.5 0 0 0-2.5 2.5V5H3.333a.833.833 0 0 0 0 1.667h.834v9.166a2.5 2.5 0 0 0 2.5 2.5h6.666a2.5 2.5 0 0 0 2.5-2.5V6.667h.834a.833.833 0 0 0 0-1.667zm-8.334-.833a.833.833 0 0 1 .834-.834h1.666a.833.833 0 0 1 .834.834V5H8.333v-.833zm5.834 11.666a.833.833 0 0 1-.834.834H6.667a.833.833 0 0 1-.834-.834V6.667h8.334v9.166zm-2.5-.833a.833.833 0 0 0 .833-.833v-5a.833.833 0 1 0-1.667 0v5a.833.833 0 0 0 .834.833z"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <p class="p-2">
                                    No Blocks uploaded yet.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                {{ $blocks->links('admincp.includes.pagination') }}
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        const activeSection = document.getElementById("sidebar-blocks");
        activeSection.classList.add("text-stone-900", "bg-primary-normal/40", "font-semibold");
    </script>
@endsection
