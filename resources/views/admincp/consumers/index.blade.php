@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Consumers' }}</h1>
            <div>
                <form>
                    @include('admincp.includes.input', [
                        'id' => 'search',
                        'name' => 'search',
                        'label' => '',
                        'placeholder' => 'Search by phone or email',
                        'value' => request()->input('search'),
                        'class' => 'input-wrap__input_light',
                    ])
                </form>
            </div>
        </div>

        <div class="content-wrapper__form-wrapper">
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table">
                    <thead>
                    <tr class="border-b">
                        @foreach ($columns as $column => $column_name)
                            <th class="table__column">
                                @if (str_starts_with($column, 'utm_') || str_ends_with($column, 'id'))
                                    {{ $column_name }}
                                @else
                                    {{ ucwords(str_replace('_', ' ', $column_name)) }}
                                @endif
                            </th>
                        @endforeach

                        @can('delete consumers')
                            <th class="table__column"></th>
                        @endcan
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($consumers as $consumer)
                        <tr class="table__row">
                            @foreach ($columns as $column => $column_name)
                                <td class="table__cell">
                                    @if ($column === 'id')
                                        <a class="link_default" href="{{ route('admincp.consumers.show', $consumer) }}">
                                            {{ $consumer->id }}
                                        </a>
                                    @elseif (in_array($column, ['confirmed', 'subscribed']))
                                        @if ($consumer->$column)
                                            <svg class="w-5 h-5 mx-auto text-green-500 fill-current">
                                                <path
                                                    d="M15.592 6.008a.833.833 0 0 0-1.184 0L8.2 12.225 5.592 9.608a.852.852 0 0 0-1.184 1.225l3.2 3.2a.833.833 0 0 0 1.184 0l6.8-6.8a.834.834 0 0 0 0-1.225z"/>
                                            </svg>
                                        @endif
                                    @elseif ($column === 'lang')
                                        @if (!empty($consumer->$column))
                                            <img class="inline ml-2"
                                                 width="20"
                                                 height="15"
                                                 src="https://flagicons.lipis.dev/flags/4x3/{{ flag_filename($consumer->$column) }}.svg"/>
                                        @endif
                                    @else
                                        <span class="@if (str_starts_with($column, 'utm_') || str_ends_with($column, 'id')) text-sm @endif">
                                            @if ($column === 'amount')
                                                {{ currency_symbol() }}
                                            @endif

                                            @if (in_array($column, ['gclid', 'fbc']))

                                                @include('admincp.includes.input', [
                                                    'id' => 'gclid',
                                                    'name' => 'gclid',
                                                    'label' => '',
                                                    'placeholder' => '',
                                                    'value' => $consumer->utm_source === 'facebook' ? $consumer->fbc : $consumer->$column,
                                                    'class' => 'text-sm',
                                                ])

                                            @else

                                                <span title="
                                                    @if ($column === 'phone' && !empty($consumer->confirmed_at))Phone confirmed at {{ $consumer->confirmed_at }}@endif
                                                    @if ($column === 'email' && !empty($consumer->email_verified_at))Email confirmed at {{ $consumer->email_verified_at }}@endif
                                                    @if ($column === 'created_at') Updated at {{ $consumer->updated_at }}@endif
                                                ">
                                                    {{ $consumer->$column }}
                                                </span>

                                            @endif

                                            @if ($column === 'phone' && !empty($consumer->$column) && empty($consumer->confirmed))
                                                <br/>
                                                <form method="POST"
                                                      action="{{ route('admincp.consumers.confirm-phone', ['consumer_id' => $consumer->id]) }}">
                                                    @csrf
                                                    @method('PUT')

                                                    <button type="submit"
                                                            class="badge badge-info"
                                                            onclick="return confirm('Are you sure you want to confirm the phone number {{ $consumer->$column }}?');">
                                                        Confirm phone
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($column === 'email' && !empty($consumer->$column) && empty($consumer->email_verified_at))
                                                <br/>
                                                <form method="POST"
                                                          action="{{ route('admincp.consumers.confirm-email', ['consumer_id' => $consumer->id]) }}">
                                                    @csrf
                                                        @method('PUT')

                                                    <button type="submit"
                                                            class="badge badge-info"
                                                            onclick="return confirm('Are you sure you want to confirm the email {{ $consumer->$column }}?');">
                                                        Confirm email
                                                    </button>
                                                </form>
                                            @endif
                                        </span>
                                    @endif
                                </td>
                            @endforeach

                            @can('delete consumers')
                                <td class="table__cell">
                                    <form method="POST"
                                          action="{{ route('admincp.consumers.delete', ['consumer_id' => $consumer->id]) }}">
                                        @csrf

                                        <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this consumer?');">
                                            <svg class="w-5 h-5 text-red-600 fill-current hover:text-red-700">
                                                <path
                                                    d="M8.333 15a.833.833 0 0 0 .834-.833v-5a.833.833 0 1 0-1.667 0v5a.833.833 0 0 0 .833.833zm8.334-10h-3.334v-.833a2.5 2.5 0 0 0-2.5-2.5H9.167a2.5 2.5 0 0 0-2.5 2.5V5H3.333a.833.833 0 0 0 0 1.667h.834v9.166a2.5 2.5 0 0 0 2.5 2.5h6.666a2.5 2.5 0 0 0 2.5-2.5V6.667h.834a.833.833 0 0 0 0-1.667zm-8.334-.833a.833.833 0 0 1 .834-.834h1.666a.833.833 0 0 1 .834.834V5H8.333v-.833zm5.834 11.666a.833.833 0 0 1-.834.834H6.667a.833.833 0 0 1-.834-.834V6.667h8.334v9.166zm-2.5-.833a.833.833 0 0 0 .833-.833v-5a.833.833 0 1 0-1.667 0v5a.833.833 0 0 0 .834.833z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <p class="p-2">
                                    No Consumers found
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{ $consumers->links('admincp.includes.pagination') }}
        </div>
    </div>
@endsection
