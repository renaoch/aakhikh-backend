<?php

namespace App\Http\Controllers\Api;

use App\Models\Subscriber;
use App\Http\Resources\SubscriberResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriberController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = Subscriber::latest();
        if ($request->boolean('active')) $query->active();

        return $this->paginated(
            SubscriberResource::collection($query->paginate($request->integer('per_page', 20)))
        );
    }

    public function subscribe(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'  => 'required|email',
            'name'   => 'nullable|string|max:255',
            'source' => 'nullable|string|max:100',
        ]);

        $subscriber = Subscriber::firstOrCreate(
            ['email' => $data['email']],
            array_merge($data, [
                'confirmation_token' => Str::random(40),
                'unsubscribe_token'  => Str::random(40),
            ])
        );

        if ($subscriber->wasRecentlyCreated) {
            // TODO: dispatch SendConfirmationEmail job
        }

        return $this->success(null, 'Check your email to confirm subscription', 201);
    }

    public function confirm(Request $request): JsonResponse
    {
        $subscriber = Subscriber::where('confirmation_token', $request->token)->firstOrFail();

        if ($subscriber->confirmed_at) {
            return $this->success(null, 'Already confirmed');
        }

        $subscriber->update([
            'confirmed_at'       => now(),
            'confirmation_token' => null,
        ]);

        return $this->success(null, 'Email confirmed! You are now subscribed.');
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $subscriber = Subscriber::where('unsubscribe_token', $request->token)->firstOrFail();
        $subscriber->update(['unsubscribed_at' => now()]);
        return $this->success(null, 'You have been unsubscribed.');
    }
}
