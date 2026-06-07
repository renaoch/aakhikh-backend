<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'author_name'    => $this->author_name,
            'author_email'   => $this->when(auth()->check() && auth()->user()->isAdmin(), $this->author_email),
            'content'        => $this->content,
            'status'         => $this->status,
            'reviewed_at'    => $this->reviewed_at,
            'rejection_note' => $this->when(auth()->check() && auth()->user()->isAdmin(), $this->rejection_note),
            'submitted_by'   => new UserResource($this->whenLoaded('submitter')),
            'reviewed_by'    => new UserResource($this->whenLoaded('reviewer')),
            'created_at'     => $this->created_at,
        ];
    }
}
