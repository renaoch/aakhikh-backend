<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends BaseController
{
    /**
     * Verify a Supabase JWT and return (or create) the local user + Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate(['access_token' => 'required|string']);

        // Verify token against Supabase user endpoint
        $supabaseUrl  = config('services.supabase.url');
        $supabaseKey  = config('services.supabase.service_role_key');

        $response = Http::withHeaders([
            'apikey'        => $supabaseKey,
            'Authorization' => 'Bearer ' . $request->access_token,
        ])->get("{$supabaseUrl}/auth/v1/user");

        if (! $response->ok()) {
            return $this->error('Invalid or expired Supabase token', 401);
        }

        $supabaseUser = $response->json();

        $user = User::updateOrCreate(
            ['supabase_uid' => $supabaseUser['id']],
            [
                'email'             => $supabaseUser['email'],
                'name'              => $supabaseUser['user_metadata']['full_name']
                                       ?? $supabaseUser['email'],
                'email_verified_at' => $supabaseUser['email_confirmed_at']
                                       ? now() : null,
                'last_login_at'     => now(),
            ]
        );

        $token = $user->createToken('api')->plainTextToken;

        return $this->success([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Authenticated');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'Logged out');
    }
}
