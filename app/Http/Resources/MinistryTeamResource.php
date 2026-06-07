<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MinistryTeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'description'   => $this->description,
            'icon'          => $this->icon,
            'display_order' => $this->display_order,
            'is_active'     => $this->is_active,
            'members'       => TeamMemberResource::collection($this->whenLoaded('members')),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
