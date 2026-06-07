<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyBreadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'body'             => $this->body,
            'bible_reference'  => $this->bible_reference,
            'image_url'        => $this->image_url,
            'published_date'   => $this->published_date,
            'scheduled_sent_at'=> $this->scheduled_sent_at,
            'is_published'     => $this->is_published,
            'created_by'       => new UserResource($this->whenLoaded('creator')),
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
