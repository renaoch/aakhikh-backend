<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Http\Resources\EventResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = Event::with('creator')->orderBy('starts_at');

        if (! $request->user()?->isAdmin()) $query->active();
        if ($request->boolean('upcoming'))  $query->upcoming();
        if ($request->boolean('featured'))  $query->where('is_featured', true);

        return $this->paginated(
            EventResource::collection($query->paginate($request->integer('per_page', 10)))
        );
    }

    public function show(Event $event): JsonResponse
    {
        return $this->success(new EventResource($event->load('creator')));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'location'         => 'nullable|string|max:255',
            'starts_at'        => 'required|date',
            'ends_at'          => 'nullable|date|after:starts_at',
            'is_featured'      => 'boolean',
            'image_url'        => 'nullable|url',
            'registration_url' => 'nullable|url',
            'is_active'        => 'boolean',
        ]);

        $event = Event::create(array_merge($data, ['created_by' => auth()->id()]));
        return $this->success(new EventResource($event), 'Event created', 201);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $data = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'description'      => 'sometimes|nullable|string',
            'location'         => 'sometimes|nullable|string|max:255',
            'starts_at'        => 'sometimes|date',
            'ends_at'          => 'sometimes|nullable|date',
            'is_featured'      => 'sometimes|boolean',
            'image_url'        => 'sometimes|nullable|url',
            'registration_url' => 'sometimes|nullable|url',
            'is_active'        => 'sometimes|boolean',
        ]);

        $event->update($data);
        return $this->success(new EventResource($event->load('creator')), 'Event updated');
    }

    public function destroy(Event $event): JsonResponse
    {
        $event->delete();
        return $this->success(null, 'Deleted');
    }
}
