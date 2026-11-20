<?php

namespace App\Console\Commands;

use Database\Seeders\UserSeeder;
use Illuminate\Console\Command;

class InitialSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'goodday:initial-setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inital setup of brand new project (local only)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (app()->isProduction()) {
            $this->error('The command can be only run in local environments');
            return 1;
        }

        $this->call('migrate');
        $this->call('db:seed');
        $this->call('permission:give');
        $this->call('storage:link');

        $this->newLine();
        $this->alert('Default adminuser has been created:');
        $this->line('Login: ' . UserSeeder::TEST_LOGIN);
        $this->line('Password: ' . UserSeeder::TEST_PASSWORD);
        $this->newLine();
        $this->warn('Ensure you changed the password upon the first login!');
        $this->newLine();

        return 0;
    }
}
