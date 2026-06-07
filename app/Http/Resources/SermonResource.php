<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SermonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'youtube_video_id'   => $this->youtube_video_id,
            'title'              => $this->title,
            'speaker'            => $this->speaker,
            'topic'              => $this->topic,
            'description'        => $this->description,
            'thumbnail_url'      => $this->thumbnail_url,
            'published_at'       => $this->published_at,
            'duration_seconds'   => $this->duration_seconds,
            'is_featured'        => $this->is_featured,
            'views_count'        => $this->views_count,
            'is_active'          => $this->is_active,
            'is_manual_override' => $this->is_manual_override,
            'created_by'         => new UserResource($this->whenLoaded('creator')),
            'created_at'         => $this->created_at,
            'updated_at'         => $this->updated_at,
        ];
    }
}
