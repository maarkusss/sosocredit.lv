<?php

namespace App\Events;

use App\Http\Resources\ApplicantResource;
use App\Models\Application;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class ApplicationEvent implements Arrayable
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Application $application)
    {
    }

    public function toArray()
    {
        return [
            'application' => new ApplicantResource($this->application),
        ];
    }
}
