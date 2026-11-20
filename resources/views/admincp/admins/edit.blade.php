@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">
        <div class="admin-page-title-wrap">
            <h1 class="admin-page-title-wrap__title">{{ $page_title ?? 'Edit admin - ' . $user->first_name . ' ' . $user->last_name . ' (' . $user->username . ')' }}</h1>
        </div>

        <div class="content-wrapper__form-wrapper">
            <form method="POST">
                @csrf

                <h2 class="admin-page-fieldset-title">Main information</h2>

                <div class="admin-page-fieldset-wrapper">
                    @include('admincp.includes.input', [
                        'id' => 'first_name',
                        'name' => 'first_name',
                        'label' => 'First Name',
                        'placeholder' => 'Admin\'s name',
                        'value' => old('first_name', $user->first_name),
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'last_name',
                        'name' => 'last_name',
                        'label' => 'Last name',
                        'placeholder' => 'Admin\'s surname',
                        'value' => old('last_name', $user->last_name),
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'password',
                        'name' => 'password',
                        'label' => 'Password (only if changing)',
                        'placeholder' => 'Admin\'s password',
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'username',
                        'name' => 'username',
                        'label' => 'Username',
                        'placeholder' => 'Admin\'s username',
                        'value' => old('username', $user->username),
                        'required' => true,
                    ])

                    @include('admincp.includes.input', [
                        'id' => 'email',
                        'name' => 'email',
                        'label' => 'Email',
                        'placeholder' => 'Admin\'s email address',
                        'value' => old('email', $user->email),
                        'required' => true,
                    ])
                </div>

                <h2 class="admin-page-fieldset-title">Permissions</h2>

                <div class="mb-4">
                    <span id="select_all" class="cursor-pointer">Select All</span>
                </div>

                <div class="flex flex-wrap mt-2 mb-6">
                    @if (!empty($available_permissions))
                        @foreach ($available_permissions as $permission_group => $permissions)
                            <div class="flex flex-col w-full sm:w-2/4 lg:w-1/4 mb-10">
                                <span class="font-medium text-gray-700">{{ ucfirst($permission_group) }}</span>

                                @foreach ($permissions as $key => $permission)
                                    @include('admincp.includes.checkbox', [
                                       'id' => 'permissions_' . str_replace(' ', '_', $permission) . '_' . $key,
                                       'name' => 'permissions[' . $permission . ']',
                                       'class' => 'permissionCheckbox',
                                       'label' => ucfirst($permission),
                                       'checked' => $user->getPermissionNames()->contains($permission),
                                   ])
                                @endforeach
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="button-wrapper">
                    <button type="submit" class="button_submit">
                        Save
                    </button>
                </div>
            </form>

            @if ($user && !empty($user->id))
                <form action="{{ route('admincp.admins.destroy', $user->id) }}" method="POST">
                    @csrf

                    @if (auth()->id() !== $user->id)
                        <button class="button_danger"
                                type="submit"
                                onclick="return confirm('Are you sure you want to delete this admin user?');">
                            Delete forever
                        </button>
                    @endif
                </form>
            @endif
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        function toggle(source) {
            var checkboxes = document.querySelectorAll('.permissionCheckbox');

            if (checkboxes) {
                for (let checkbox of checkboxes) {
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                }
            }
        }

        const selectAllButton = document.getElementById('select_all');

        selectAllButton.addEventListener("click", (e) => {
            toggle();
        });
    </script>
@endsection
