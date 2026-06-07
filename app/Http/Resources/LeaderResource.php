<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'role_title'    => $this->role_title,
            'bio'           => $this->bio,
            'photo_url'     => $this->photo_url,
            'email'         => $this->email,
            'category'      => $this->category,
            'display_order' => $this->display_order,
            'is_active'     => $this->is_active,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
