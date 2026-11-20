<?php

namespace App\Events;

use App\Models\Consumer;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class ConsumerEvent implements Arrayable
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Consumer $consumer)
    {
    }

    public function toArray()
    {
        return [
            'consumer' => $this->consumer->toArray(),
        ];
    }
}
