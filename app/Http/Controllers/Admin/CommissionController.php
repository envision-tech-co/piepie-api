<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SetCommissionRequest;
use App\Models\CommissionSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    /**
     * List all commission settings.
     */
    public function index(): JsonResponse
    {
        $commissions = CommissionSetting::with('serviceCategory')->get();

        return response()->json([
            'success' => true,
            'commissions' => $commissions->map(function ($commission) {
                return [
                    'id' => $commission->id,
                    'service_category_id' => $commission->service_category_id,
                    'service_category_name' => $commission->serviceCategory?->name ?? 'Global Default',
                    'rate' => (float) $commission->rate,
                    'is_active' => $commission->is_active,
                    'created_at' => $commission->created_at?->toISOString(),
                ];
            }),
        ]);
    }

    /**
     * Create or update a commission setting (upsert).
     */
    public function store(SetCommissionRequest $request): JsonResponse
    {
        $admin = $request->user('admin');
        $categoryId = $request->input('service_category_id');

        $commission = CommissionSetting::updateOrCreate(
            ['service_category_id' => $categoryId],
            [
                'rate' => $request->input('rate'),
                'is_active' => true,
                'created_by' => $admin->id,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Commission setting saved.',
            'commission' => [
                'id' => $commission->id,
                'service_category_id' => $commission->service_category_id,
                'rate' => (float) $commission->rate,
                'is_active' => $commission->is_active,
            ],
        ]);
    }

    /**
     * Delete a commission setting (falls back to global).
     */
    public function destroy(int $id): JsonResponse
    {
        $commission = CommissionSetting::findOrFail($id);

        if ($commission->service_category_id === null) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the global commission setting. Update it instead.',
            ], 422);
        }

        $commission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commission setting removed. Category will use global rate.',
        ]);
    }
}
