<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class GiveAllPermissionsToFirstUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:give';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Give permissions to first user (id 1)';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $USER_ID = 1;
        $user = User::query()
            ->where('id', $USER_ID)
            ->first();

        if (!$user) {
            $this->error('User with id `' . $USER_ID . '` has not been found');
            return;
        }

        $array = collect(available_permissions('edit'))
            ->flatten()
            ->toArray();

        foreach ($array as $perm) {
            if (Permission::query()->where('name', $perm)->exists()) {
                $this->warn('Permission `' . $perm . '` exists');
                continue;
            }

            Permission::create(['name' => $perm]);
            $user->givePermissionTo($perm);
            $this->info('Assigning permission `'
                . $perm . '` to ' . $user->email
                . ' (user_id ' . $user->id . ')');
        }
    }
}
