<?php

namespace App\Http\Controllers\Api;

use App\Models\ServiceSchedule;
use App\Http\Resources\ServiceScheduleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceScheduleController extends BaseController
{
    public function index(): JsonResponse
    {
        return $this->success(
            ServiceScheduleResource::collection(ServiceSchedule::active()->get())
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'day_of_week'   => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'nullable|date_format:H:i|after:start_time',
            'location'      => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'is_active'     => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        return $this->success(new ServiceScheduleResource(ServiceSchedule::create($data)), 'Created', 201);
    }

    public function update(Request $request, ServiceSchedule $serviceSchedule): JsonResponse
    {
        $serviceSchedule->update($request->validate([
            'title'         => 'sometimes|string|max:255',
            'day_of_week'   => 'sometimes|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time'    => 'sometimes|date_format:H:i',
            'end_time'      => 'sometimes|nullable|date_format:H:i',
            'location'      => 'sometimes|nullable|string|max:255',
            'description'   => 'sometimes|nullable|string',
            'is_active'     => 'sometimes|boolean',
            'display_order' => 'sometimes|integer|min:0',
        ]));

        return $this->success(new ServiceScheduleResource($serviceSchedule), 'Updated');
    }

    public function destroy(ServiceSchedule $serviceSchedule): JsonResponse
    {
        $serviceSchedule->delete();
        return $this->success(null, 'Deleted');
    }
}
