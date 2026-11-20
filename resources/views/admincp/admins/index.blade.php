@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Admins' }}</h1>

            @can('add admins')
                <a href="{{ route('admincp.admins.create') }}"
                   class="button_secondary">
                    <svg class="w-5 h-5 mr-1 fill-current">
                        <path d="M15.833 9.167h-5v-5a.833.833 0 1 0-1.666 0v5h-5a.833.833 0 1 0 0 1.666h5v5a.833.833 0 0 0 1.666 0v-5h5a.833.833 0 0 0 0-1.666z"/>
                    </svg>
                    <span>Add admin</span>
                </a>
            @endcan
        </div>

        <div class="w-full p-4 bg-white rounded">
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="table">
                    <thead>
                    <tr class="border-b">
                        <th class="table__column">Name</th>
                        <th class="table__column">Username</th>
                        <th class="table__column">Last logged in</th>
                        <th class="table__column"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                        <tr class="table__row">
                            <td class="table__cell">
                                @can('edit admins')
                                    <a class="link_default"
                                       href="{{ route('admincp.admins.edit', $user->id) }}">
                                        @endcan
                                        {{ $user->first_name }} {{ $user->last_name }}
                                        @can('edit admins')
                                    </a>
                                @endcan
                            </td>
                            <td class="table__cell">
                                <span>{{ $user->username }}</span>
                            </td>
                            <td class="table__cell">
                                @if (!empty($user->logInAttempts()->orderBy('created_at', 'DESC')->first()->created_at))
                                    <span>{{ $user->logInAttempts()->orderBy('created_at', 'DESC')->first()->created_at }}</span>
                                @else
                                    <span>No data</span>
                                @endif
                            </td>
                            @if (auth()->id() !== $user->id)
                                @can('delete admins')
                                    <td class="table__cell">
                                        <form action="{{ route('admincp.admins.destroy', $user->id) }}"
                                              method="POST">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('Are you sure you want to delete this Admin?');">
                                                <svg class="w-5 h-5 text-red-600 fill-current hover:text-red-700">
                                                    <path d="M8.333 15a.833.833 0 0 0 .834-.833v-5a.833.833 0 1 0-1.667 0v5a.833.833 0 0 0 .833.833zm8.334-10h-3.334v-.833a2.5 2.5 0 0 0-2.5-2.5H9.167a2.5 2.5 0 0 0-2.5 2.5V5H3.333a.833.833 0 0 0 0 1.667h.834v9.166a2.5 2.5 0 0 0 2.5 2.5h6.666a2.5 2.5 0 0 0 2.5-2.5V6.667h.834a.833.833 0 0 0 0-1.667zm-8.334-.833a.833.833 0 0 1 .834-.834h1.666a.833.833 0 0 1 .834.834V5H8.333v-.833zm5.834 11.666a.833.833 0 0 1-.834.834H6.667a.833.833 0 0 1-.834-.834V6.667h8.334v9.166zm-2.5-.833a.833.833 0 0 0 .833-.833v-5a.833.833 0 1 0-1.667 0v5a.833.833 0 0 0 .834.833z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                @endcan
                            @else
                                <td class="table__cell"></td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
