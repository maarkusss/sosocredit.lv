<?php

namespace App\Console\Commands;

use App\Services\GoogleReviewService;
use Illuminate\Console\Command;

class ImportGoogleReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:import-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import reviews from Google Places API';

    /**
     * Execute the console command.
     *
     * @return int
     */
public function handle(GoogleReviewService $service)
    { 
        $service->import();
        $this->info('Google reviews imported successfully');
    }
}
