<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = User::latest();
        if ($request->filled('role')) $query->where('role', $request->role);
        if ($request->filled('q'))    $query->where(fn($q) => $q->where('name', 'ilike', '%' . $request->q . '%')->orWhere('email', 'ilike', '%' . $request->q . '%'));

        return $this->paginated(
            UserResource::collection($query->paginate($request->integer('per_page', 20)))
        );
    }

    public function show(User $user): JsonResponse
    {
        return $this->success(new UserResource($user));
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'role' => 'required|in:super_admin,admin,editor,media_staff,member',
        ]);

        $user->update($data);
        return $this->success(new UserResource($user), 'Role updated');
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'phone'      => 'sometimes|nullable|string|max:30',
            'avatar_url' => 'sometimes|nullable|url',
            'bio'        => 'sometimes|nullable|string',
        ]);

        $request->user()->update($data);
        return $this->success(new UserResource($request->user()), 'Profile updated');
    }
}
