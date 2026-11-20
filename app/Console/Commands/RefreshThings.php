<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RefreshThings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'goodday:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh caches on the site';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call('cache:clear');
        $this->info(Artisan::output());

        Artisan::call('config:clear');
        Artisan::call('config:cache');
        $this->info(Artisan::output());

        Artisan::call('event:clear');
        Artisan::call('event:cache');
        $this->info(Artisan::output());

        Artisan::call('view:clear');
        Artisan::call('view:cache');
        $this->info(Artisan::output());

        return Command::SUCCESS;
    }
}
