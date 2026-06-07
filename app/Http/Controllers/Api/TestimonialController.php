<?php

namespace App\Http\Controllers\Api;

use App\Models\Testimonial;
use App\Http\Resources\TestimonialResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestimonialController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = Testimonial::with(['submitter', 'reviewer'])->latest();

        if (! $request->user()?->isAdmin()) {
            $query->approved();
        } elseif ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $this->paginated(
            TestimonialResource::collection($query->paginate($request->integer('per_page', 12)))
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'author_name'  => 'required|string|max:255',
            'author_email' => 'nullable|email',
            'content'      => 'required|string|min:20',
        ]);

        $testimonial = Testimonial::create(array_merge($data, [
            'submitted_by' => auth()->id(),
            'status'       => 'pending',
        ]));

        return $this->success(new TestimonialResource($testimonial), 'Submitted for review', 201);
    }

    public function review(Request $request, Testimonial $testimonial): JsonResponse
    {
        $data = $request->validate([
            'action'         => 'required|in:approve,reject',
            'rejection_note' => 'required_if:action,reject|nullable|string',
        ]);

        $testimonial->update([
            'status'         => $data['action'] === 'approve' ? 'approved' : 'rejected',
            'reviewed_by'    => auth()->id(),
            'reviewed_at'    => now(),
            'rejection_note' => $data['rejection_note'] ?? null,
        ]);

        return $this->success(new TestimonialResource($testimonial->load(['submitter', 'reviewer'])));
    }

    public function destroy(Testimonial $testimonial): JsonResponse
    {
        $testimonial->delete();
        return $this->success(null, 'Deleted');
    }
}
