<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SetCommissionRequest;
use App\Models\CommissionSetting;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CommissionWebController extends Controller
{
    public function index(): View
    {
        $commissions = CommissionSetting::with(['serviceCategory', 'creator'])
            ->orderBy('service_category_id')
            ->get();

        $categories = ServiceCategory::orderBy('sort_order')->get();

        // Categories not yet overridden
        $overriddenIds = $commissions->whereNotNull('service_category_id')->pluck('service_category_id')->toArray();
        $availableCategories = $categories->whereNotIn('id', $overriddenIds);

        return view('admin.commissions.index', compact('commissions', 'categories', 'availableCategories'));
    }

    public function store(SetCommissionRequest $request): RedirectResponse
    {
        $admin = $request->user('admin');

        CommissionSetting::updateOrCreate(
            ['service_category_id' => $request->input('service_category_id')],
            [
                'rate' => $request->input('rate'),
                'is_active' => true,
                'created_by' => $admin->id,
            ]
        );

        return back()->with('success', 'Commission setting saved.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $commission = CommissionSetting::findOrFail($id);

        if ($commission->service_category_id === null) {
            return back()->with('error', 'Cannot delete the global commission. Update it instead.');
        }

        $commission->delete();

        return back()->with('success', 'Commission override removed. Category will use the global rate.');
    }
}
