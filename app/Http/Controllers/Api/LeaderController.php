<?php

namespace App\Http\Controllers\Api;

use App\Models\Leader;
use App\Http\Resources\LeaderResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaderController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = Leader::active();
        if ($request->filled('category')) $query->where('category', $request->category);

        return $this->success(LeaderResource::collection($query->get()));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'role_title'    => 'required|string|max:255',
            'bio'           => 'nullable|string',
            'photo_url'     => 'nullable|url',
            'email'         => 'nullable|email',
            'category'      => 'required|in:pastor,elder,deacon,staff',
            'display_order' => 'integer|min:0',
            'is_active'     => 'boolean',
        ]);

        return $this->success(new LeaderResource(Leader::create($data)), 'Created', 201);
    }

    public function update(Request $request, Leader $leader): JsonResponse
    {
        $leader->update($request->validate([
            'name'          => 'sometimes|string|max:255',
            'role_title'    => 'sometimes|string|max:255',
            'bio'           => 'sometimes|nullable|string',
            'photo_url'     => 'sometimes|nullable|url',
            'email'         => 'sometimes|nullable|email',
            'category'      => 'sometimes|in:pastor,elder,deacon,staff',
            'display_order' => 'sometimes|integer|min:0',
            'is_active'     => 'sometimes|boolean',
        ]));

        return $this->success(new LeaderResource($leader), 'Updated');
    }

    public function destroy(Leader $leader): JsonResponse
    {
        $leader->delete();
        return $this->success(null, 'Deleted');
    }
}
