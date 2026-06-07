<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'donor_name'          => $this->is_anonymous ? 'Anonymous' : $this->donor_name,
            'donor_email'         => $this->when(auth()->check() && auth()->user()->isAdmin(), $this->donor_email),
            'amount'              => $this->amount,
            'currency'            => $this->currency,
            'type'                => $this->type,
            'category'            => $this->category,
            'status'              => $this->status,
            'is_anonymous'        => $this->is_anonymous,
            'message'             => $this->message,
            'razorpay_order_id'   => $this->razorpay_order_id,
            'paid_at'             => $this->paid_at,
            'created_at'          => $this->created_at,
        ];
    }
}
