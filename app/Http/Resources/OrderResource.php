<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'order_number'        => $this->order_number,
            'user'                => new UserResource($this->whenLoaded('user')),
            'items'               => OrderItemResource::collection($this->whenLoaded('items')),
            'subtotal'            => $this->subtotal,
            'tax'                 => $this->tax,
            'shipping'            => $this->shipping,
            'total'               => $this->total,
            'currency'            => $this->currency,
            'status'              => $this->status,
            'razorpay_order_id'   => $this->razorpay_order_id,
            'razorpay_payment_id' => $this->when(auth()->check() && auth()->user()->isAdmin(), $this->razorpay_payment_id),
            'shipping_address'    => $this->shipping_address,
            'notes'               => $this->notes,
            'paid_at'             => $this->paid_at,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
        ];
    }
}
