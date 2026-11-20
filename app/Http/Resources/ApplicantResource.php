<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApplicantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'subscribed' => $this->subscribed,
            'agree_to_terms' => $this->agree_to_terms,
            'has_bank_attachment' => $this->has_bank_attachment,
        ];
    }
}
