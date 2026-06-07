<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'price'          => $this->price,
            'stock_quantity' => $this->stock_quantity,
            'image_url'      => $this->image_url,
            'images'         => $this->images,
            'category'       => $this->category,
            'is_active'      => $this->is_active,
            'is_featured'    => $this->is_featured,
            'created_by'     => new UserResource($this->whenLoaded('creator')),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
