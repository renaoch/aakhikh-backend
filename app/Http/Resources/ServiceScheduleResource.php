<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'day_of_week'   => $this->day_of_week,
            'start_time'    => $this->start_time,
            'end_time'      => $this->end_time,
            'location'      => $this->location,
            'description'   => $this->description,
            'is_active'     => $this->is_active,
            'display_order' => $this->display_order,
        ];
    }
}
