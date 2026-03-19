<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class SystemSettingsController extends Controller
{
    /**
     * GET /api/settings
     * Returns non-sensitive system settings (public-safe).
     */
    public function index(Request $request): JsonResponse
    {
        $group = $request->query('group');

        $query = SystemSetting::query()
            ->where('is_encrypted', false); // Never expose encrypted fields via API

        if ($group) {
            $query->where('group', $group);
        }

        $settings = $query->orderBy('group')->orderBy('sort_order')->get()
            ->mapWithKeys(fn ($s) => [$s->key => $s->value]);

        return response()->json([
            'success' => true,
            'data'    => $settings,
        ]);
    }

    /**
     * PUT /api/settings
     * Update system settings (admin only, auth:sanctum).
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key'   => 'required|string',
            'settings.*.value' => 'nullable',
        ]);

        $updated = [];

        foreach ($request->input('settings') as $item) {
            $setting = SystemSetting::where('key', $item['key'])->first();

            if (!$setting) {
                continue;
            }

            $oldValue = $setting->value;
            $value = $item['value'];

            // Handle toggle
            if ($setting->type === 'toggle') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            }

            // Encrypt if needed
            if ($setting->is_encrypted && $value !== null && $value !== '') {
                $value = Crypt::encryptString((string) $value);
            }

            $setting->update(['value' => $value]);

            // Log
            if ($oldValue !== $value) {
                SystemSetting::logChangePublic($item['key'], $oldValue, $value);
            }

            $updated[] = $item['key'];
        }

        SystemSetting::clearCache();

        return response()->json([
            'success' => true,
            'message' => count($updated) . ' settings updated.',
            'updated' => $updated,
        ]);
    }

    /**
     * GET /api/settings/audit
     * Return recent settings change log (admin only).
     */
    public function auditLog(Request $request): JsonResponse
    {
        $logs = \Illuminate\Support\Facades\DB::table('settings_audit_logs')
            ->leftJoin('users', 'settings_audit_logs.user_id', '=', 'users.id')
            ->select('settings_audit_logs.*', 'users.name as changed_by')
            ->orderByDesc('settings_audit_logs.created_at')
            ->limit($request->query('limit', 50))
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $logs,
        ]);
    }
}
