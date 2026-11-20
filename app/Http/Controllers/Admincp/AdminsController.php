<?php

namespace App\Http\Controllers\Admincp;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminsController extends AdminController
{
    public function index(): Renderable
    {
        $per_page = 20;

        return view('admincp.admins.index')
            ->with([
                'users' => User::paginate($per_page),
                'meta' => $this->metaTitle('Admins'),
            ]);
    }

    public function create(): Renderable
    {
        return view('admincp.admins.edit')
            ->with([
                'available_permissions' => available_permissions(),
                'meta' => $this->metaTitle('Add an admin'),
                'page_title' => 'Add an Admin',
                'roles' => Role::get(),
                'user' => new User(),
            ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required',
            'password' => 'required',
            'email' => 'required|email',
        ]);

        $user = new User();
        $user->first_name = $data['first_name'];
        $user->email = $data['email'];
        $user->last_name = $data['last_name'];
        $user->username = $data['username'];
        $user->password = Hash::make($request->input('password'));
        $user->save();

        if (!empty($request->input('permissions'))) {
            foreach ($request->input('permissions') as $group => $active) {
                $permission = Permission::firstOrCreate(['name' => $group]);
                $user->givePermissionTo($permission);
            }
        }

        $this->logAdminAction('Added admin "' . $data['first_name'] . ' ' . $data['last_name'] . '"');

        return redirect(route('admincp.admins.index'))
            ->with('success', 'Admin has been added!');
    }

    public function edit(User $user): Renderable
    {
        return view('admincp.admins.edit')->with([
            'user' => $user,
            'roles' => Role::get(),
            'available_permissions' => available_permissions('edit'),
            'meta' => $this->metaTitle('Edit an admin'),
        ]);
    }

    public function update(User $user, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required',
            'email' => 'required',
        ]);

        $permissions = $user->getPermissionNames();
        foreach ($permissions as $permission) {
            $user->revokePermissionTo($permission);
        }

        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
        ]);

        if ($request->input('permissions')) {
            foreach ($request->input('permissions') as $group => $active) {
                $permission = Permission::firstOrCreate(['name' => $group]);
                $user->givePermissionTo($permission);
            }
        }

        if ($request->input('password')) {
            $user->update([
                'password' => Hash::make($request->input('password')),
            ]);
        }

        $this->logAdminAction('Updated admin "' . $user->first_name . ' ' . $user->last_name . '"');

        return redirect()
            ->back()
            ->with('success', 'Admin has been updated!');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() !== $user->id) {
            $this->logAdminAction('Deleted admin "' . $user->first_name . ' ' . $user->last_name . '"');
            $user->delete();
        }

        return redirect(route('admincp.admins.index'))
            ->with('success', 'Admin has been deleted!');
    }
}
