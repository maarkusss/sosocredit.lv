<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsumerUpdated extends JsonResource
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
            'message' => 'Consumer updated!',
            'data' => [
                'verified' => [
                    'email' => $this->email_verified_at && $request->email ? true : false,
                    'phone' => $this->confirmed_at && $request->phone ? true : false,
                ],
            ],
        ];

        return $response;
    }
}
