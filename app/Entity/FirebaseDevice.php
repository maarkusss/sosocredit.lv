<?php

namespace App\Entity;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class FirebaseDevice implements Arrayable, Jsonable
{
    public $app_instance_id;
    public $fcm_token;

    public static function fromJson(string $json): FirebaseDevice
    {
        $decoded = json_decode($json);

        $self = new FirebaseDevice();
        $self->app_instance_id = $decoded->app_instance_id;
        $self->fcm_token = $decoded->fcm_token;

        return $self;
    }

    public function toArray(): array
    {
        return [
            'app_instance_id' => $this->app_instance_id,
            'fcm_token' => $this->fcm_token,
        ];
    }

    /**
     * @return false|string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
