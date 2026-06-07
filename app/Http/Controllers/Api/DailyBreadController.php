<?php

namespace App\Http\Controllers\Api;

use App\Models\DailyBread;
use App\Http\Resources\DailyBreadResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyBreadController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = DailyBread::with('creator')->orderByDesc('published_date');

        if (! $request->user()?->isAdmin()) $query->published();

        return $this->paginated(
            DailyBreadResource::collection($query->paginate($request->integer('per_page', 10)))
        );
    }

    public function today(): JsonResponse
    {
        $bread = DailyBread::published()->whereDate('published_date', today())->first();
        if (! $bread) return $this->error('No daily bread for today', 404);
        return $this->success(new DailyBreadResource($bread));
    }

    public function show(DailyBread $dailyBread): JsonResponse
    {
        return $this->success(new DailyBreadResource($dailyBread->load('creator')));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'body'            => 'required|string',
            'bible_reference' => 'nullable|string|max:255',
            'image_url'       => 'nullable|url',
            'published_date'  => 'required|date|unique:daily_breads',
            'is_published'    => 'boolean',
        ]);

        $bread = DailyBread::create(array_merge($data, ['created_by' => auth()->id()]));
        return $this->success(new DailyBreadResource($bread), 'Daily bread created', 201);
    }

    public function update(Request $request, DailyBread $dailyBread): JsonResponse
    {
        $data = $request->validate([
            'title'           => 'sometimes|string|max:255',
            'body'            => 'sometimes|string',
            'bible_reference' => 'sometimes|nullable|string|max:255',
            'image_url'       => 'sometimes|nullable|url',
            'published_date'  => 'sometimes|date|unique:daily_breads,published_date,' . $dailyBread->id,
            'is_published'    => 'sometimes|boolean',
        ]);

        $dailyBread->update($data);
        return $this->success(new DailyBreadResource($dailyBread->load('creator')), 'Updated');
    }

    public function destroy(DailyBread $dailyBread): JsonResponse
    {
        $dailyBread->delete();
        return $this->success(null, 'Deleted');
    }
}
