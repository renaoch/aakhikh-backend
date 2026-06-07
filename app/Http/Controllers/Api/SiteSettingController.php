<?php

namespace App\Http\Controllers\Api;

use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteSettingController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $query = SiteSetting::query();

        // Non-admins only see public settings
        if (! $request->user()?->isAdmin()) {
            $query->where('is_public', true);
        }

        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        return $this->success($query->get()->keyBy('key'));
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'settings'         => 'required|array',
            'settings.*.key'   => 'required|string',
            'settings.*.value' => 'present',
        ]);

        foreach ($data['settings'] as $setting) {
            SiteSetting::set($setting['key'], $setting['value']);
        }

        return $this->success(null, 'Settings saved');
    }
}
