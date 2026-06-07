<?php

namespace App\Http\Controllers\Api;

use App\Models\Announcement;
use App\Http\Resources\AnnouncementResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = Announcement::with('creator')->latest();
        if (! $request->user()?->isAdmin()) $query->active();

        return $this->paginated(
            AnnouncementResource::collection($query->paginate($request->integer('per_page', 10)))
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'body'         => 'required|string',
            'is_active'    => 'boolean',
            'published_at' => 'nullable|date',
            'expires_at'   => 'nullable|date|after:published_at',
        ]);

        $announcement = Announcement::create(array_merge($data, ['created_by' => auth()->id()]));
        return $this->success(new AnnouncementResource($announcement), 'Created', 201);
    }

    public function update(Request $request, Announcement $announcement): JsonResponse
    {
        $data = $request->validate([
            'title'        => 'sometimes|string|max:255',
            'body'         => 'sometimes|string',
            'is_active'    => 'sometimes|boolean',
            'published_at' => 'sometimes|nullable|date',
            'expires_at'   => 'sometimes|nullable|date',
        ]);

        $announcement->update($data);
        return $this->success(new AnnouncementResource($announcement->load('creator')), 'Updated');
    }

    public function destroy(Announcement $announcement): JsonResponse
    {
        $announcement->delete();
        return $this->success(null, 'Deleted');
    }
}
