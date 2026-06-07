<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'team_id'       => $this->team_id,
            'name'          => $this->name,
            'role_title'    => $this->role_title,
            'photo_url'     => $this->photo_url,
            'email'         => $this->email,
            'display_order' => $this->display_order,
            'is_active'     => $this->is_active,
        ];
    }
}
