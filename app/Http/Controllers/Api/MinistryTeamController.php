<?php

namespace App\Http\Controllers\Api;

use App\Models\MinistryTeam;
use App\Models\TeamMember;
use App\Http\Resources\MinistryTeamResource;
use App\Http\Resources\TeamMemberResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MinistryTeamController extends BaseController
{
    public function index(): JsonResponse
    {
        $teams = MinistryTeam::active()->with('members')->get();
        return $this->success(MinistryTeamResource::collection($teams));
    }

    public function show(MinistryTeam $ministryTeam): JsonResponse
    {
        return $this->success(new MinistryTeamResource($ministryTeam->load('members')));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255|unique:ministry_teams',
            'description'   => 'nullable|string',
            'icon'          => 'nullable|string|max:100',
            'display_order' => 'integer|min:0',
            'is_active'     => 'boolean',
        ]);

        return $this->success(new MinistryTeamResource(MinistryTeam::create($data)), 'Created', 201);
    }

    public function update(Request $request, MinistryTeam $ministryTeam): JsonResponse
    {
        $ministryTeam->update($request->validate([
            'name'          => 'sometimes|string|max:255|unique:ministry_teams,name,' . $ministryTeam->id,
            'description'   => 'sometimes|nullable|string',
            'icon'          => 'sometimes|nullable|string|max:100',
            'display_order' => 'sometimes|integer|min:0',
            'is_active'     => 'sometimes|boolean',
        ]));

        return $this->success(new MinistryTeamResource($ministryTeam->load('members')), 'Updated');
    }

    public function destroy(MinistryTeam $ministryTeam): JsonResponse
    {
        $ministryTeam->delete();
        return $this->success(null, 'Deleted');
    }

    // ── Team Members ─────────────────────────────────────

    public function addMember(Request $request, MinistryTeam $ministryTeam): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'role_title'    => 'nullable|string|max:255',
            'photo_url'     => 'nullable|url',
            'email'         => 'nullable|email',
            'display_order' => 'integer|min:0',
        ]);

        $member = $ministryTeam->members()->create($data);
        return $this->success(new TeamMemberResource($member), 'Member added', 201);
    }

    public function removeMember(MinistryTeam $ministryTeam, TeamMember $member): JsonResponse
    {
        abort_if($member->team_id !== $ministryTeam->id, 404);
        $member->delete();
        return $this->success(null, 'Member removed');
    }
}
