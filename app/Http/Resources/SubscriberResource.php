<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'email'           => $this->email,
            'name'            => $this->name,
            'confirmed_at'    => $this->confirmed_at,
            'unsubscribed_at' => $this->unsubscribed_at,
            'source'          => $this->source,
            'created_at'      => $this->created_at,
        ];
    }
}
