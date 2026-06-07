<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'description'      => $this->description,
            'location'         => $this->location,
            'starts_at'        => $this->starts_at,
            'ends_at'          => $this->ends_at,
            'is_featured'      => $this->is_featured,
            'image_url'        => $this->image_url,
            'registration_url' => $this->registration_url,
            'is_active'        => $this->is_active,
            'created_by'       => new UserResource($this->whenLoaded('creator')),
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
