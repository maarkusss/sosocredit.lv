<?php

namespace App\Observers;

use App\Models\Consumer;
use App\Services\GoodAff\ApiDataService;
use App\Services\GoodAff\GoodAffDataService;
use GuzzleHttp\Exception\GuzzleException;

class ConsumerObserver
{
    private bool $use_api_data;

    public function __construct(protected GoodAffDataService $goodaff_data, protected ApiDataService $api_data) {
        $this->use_api_data = config('services.goodaff.use_api_data', false);
    }

    /**
     * Handle the consumers "created" event.
     *
     * @param Consumer $consumer
     * @return void
     * @throws GuzzleException
     */
    public function created(Consumer $consumer)
    {
        if($this->use_api_data) {
            $this->api_data->postConsumer($consumer);
        } else {
            $this->goodaff_data->postConsumer($consumer);
        }
    }

    /**
     * Handle the consumers "updated" event.
     *
     * @param Consumer $consumer
     * @return void
     * @throws GuzzleException
     */
    public function updated(Consumer $consumer)
    {
        if($this->use_api_data) {
            $this->api_data->postConsumer($consumer);
        } else {
            $this->goodaff_data->postConsumer($consumer);
        }
    }
}
