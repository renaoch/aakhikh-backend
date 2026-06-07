<?php

namespace App\Http\Controllers\Api;

use App\Models\Donation;
use App\Http\Resources\DonationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = Donation::with('user')->latest();

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        } else {
            if ($request->filled('status'))   $query->where('status', $request->status);
            if ($request->filled('category')) $query->where('category', $request->category);
        }

        return $this->paginated(
            DonationResource::collection($query->paginate($request->integer('per_page', 15)))
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'donor_name'   => 'required|string|max:255',
            'donor_email'  => 'nullable|email',
            'amount'       => 'required|numeric|min:1',
            'currency'     => 'string|max:5',
            'type'         => 'in:one_time,recurring',
            'category'     => 'in:tithe,mission,general,partnership',
            'is_anonymous' => 'boolean',
            'message'      => 'nullable|string',
        ]);

        $donation = Donation::create(array_merge($data, [
            'user_id' => auth()->id(),
            'status'  => 'pending',
        ]));

        return $this->success(new DonationResource($donation), 'Donation initiated', 201);
    }

    public function confirm(Request $request, Donation $donation): JsonResponse
    {
        $data = $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
        ]);

        $donation->update(array_merge($data, [
            'status'  => 'paid',
            'paid_at' => now(),
        ]));

        return $this->success(new DonationResource($donation), 'Payment confirmed');
    }
}
