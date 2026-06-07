<?php

namespace App\Http\Controllers\Api;

use App\Models\Sermon;
use App\Http\Resources\SermonResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SermonController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = Sermon::with('creator')->latest('published_at');

        if ($request->boolean('featured')) $query->featured();
        if ($request->filled('speaker'))  $query->where('speaker', $request->speaker);
        if ($request->filled('topic'))    $query->where('topic', $request->topic);

        // Public route: only active sermons
        if (! $request->user()?->isAdmin()) $query->active();

        $sermons = $query->paginate($request->integer('per_page', 12));

        return $this->paginated(SermonResource::collection($sermons));
    }

    public function show(Sermon $sermon): JsonResponse
    {
        $sermon->increment('views_count');
        return $this->success(new SermonResource($sermon->load('creator')));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'              => 'required|string|max:255',
            'speaker'            => 'required|string|max:255',
            'youtube_video_id'   => 'nullable|string|max:30|unique:sermons',
            'topic'              => 'nullable|string|max:255',
            'description'        => 'nullable|string',
            'thumbnail_url'      => 'nullable|url',
            'published_at'       => 'nullable|date',
            'duration_seconds'   => 'nullable|integer|min:0',
            'is_featured'        => 'boolean',
            'is_active'          => 'boolean',
            'is_manual_override' => 'boolean',
        ]);

        $sermon = Sermon::create(array_merge($data, ['created_by' => auth()->id()]));

        return $this->success(new SermonResource($sermon), 'Sermon created', 201);
    }

    public function update(Request $request, Sermon $sermon): JsonResponse
    {
        $data = $request->validate([
            'title'              => 'sometimes|string|max:255',
            'speaker'            => 'sometimes|string|max:255',
            'youtube_video_id'   => 'sometimes|nullable|string|max:30|unique:sermons,youtube_video_id,' . $sermon->id,
            'topic'              => 'sometimes|nullable|string|max:255',
            'description'        => 'sometimes|nullable|string',
            'thumbnail_url'      => 'sometimes|nullable|url',
            'published_at'       => 'sometimes|nullable|date',
            'duration_seconds'   => 'sometimes|nullable|integer|min:0',
            'is_featured'        => 'sometimes|boolean',
            'is_active'          => 'sometimes|boolean',
            'is_manual_override' => 'sometimes|boolean',
        ]);

        $sermon->update($data);

        return $this->success(new SermonResource($sermon->load('creator')), 'Sermon updated');
    }

    public function destroy(Sermon $sermon): JsonResponse
    {
        $sermon->delete();
        return $this->success(null, 'Sermon deleted');
    }
}
