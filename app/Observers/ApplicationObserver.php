<?php

namespace App\Observers;

use App\Models\Application;
use App\Services\GoodAff\ApiDataService;
use App\Services\GoodAff\GoodAffDataService;
use Illuminate\Support\Facades\Log;

class ApplicationObserver
{
    public function __construct(
        protected GoodAffDataService $goodaff_data,
        protected ApiDataService $api_data
    )
    {
        $this->use_api_data = config('services.goodaff.use_api_data', false);
    }

    public function created(Application $application): void
    {
        if ($this->use_api_data) {
            $this->api_data->postApplication($application);
        } else {
            $this->goodaff_data->postApplication($application);
        }
    }

    public function updated(Application $application): void
    {
        if ($this->use_api_data) {
            $this->api_data->postApplication($application);
        } else {
            $this->goodaff_data->postApplication($application);
        }
    }
}
