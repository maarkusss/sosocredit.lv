<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserJsonObjectModel extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        $app_instance_id = (string) request()->header('App-Instance-Id');
        $fcm_token_for_this_app_instance_id = null;

        $devices = [];
        foreach ($this->devices as $consumer_device) {
            $fcm_token = !empty($consumer_device->value) ? '[HIDDEN]' : null;
            if ($this->additional['show_fcm_token'] ?? false) {
                $fcm_token = $consumer_device->value;
            }

            $devices[] = [
                'app_instance_id' => $consumer_device->key,
                'fcm_token' => $fcm_token,
            ];

            if ($consumer_device->key === $app_instance_id) {
                $fcm_token_for_this_app_instance_id = $fcm_token;
            }
        }

        return [
            'id' => $this->id,
            'phone_number' => (string) $this->phone,
            'email' => $this->email,
            'fcm_token_for_this_app_instance_id' => $fcm_token_for_this_app_instance_id,
            'devices' => $devices,
        ];
    }
}