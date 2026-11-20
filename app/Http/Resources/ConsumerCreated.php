<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsumerCreated extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $response = [
            'message' => 'Consumer created!',
            'data' => [
                'verified' => [
                    'email' => false,
                    'phone' => false,
                ],
            ],
        ];

        return $response;
    }
}
