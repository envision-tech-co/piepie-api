<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateServiceCategoryRequest;
use App\Http\Requests\Admin\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceCategoryWebController extends Controller
{
    public function index(): View
    {
        $categories = ServiceCategory::orderBy('sort_order')->paginate(20);

        return view('admin.services.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.services.create');
    }

    public function store(CreateServiceCategoryRequest $request): RedirectResponse
    {
        ServiceCategory::create($request->validated());

        return redirect()->route('admin.services.index')
            ->with('success', 'Service category created.');
    }

    public function edit(int $id): View
    {
        $category = ServiceCategory::findOrFail($id);

        return view('admin.services.edit', compact('category'));
    }

    public function update(UpdateServiceCategoryRequest $request, int $id): RedirectResponse
    {
        $category = ServiceCategory::findOrFail($id);
        $data = $request->validated();
        // Handle is_active checkbox
        $data['is_active'] = $request->boolean('is_active');
        $category->update($data);

        return redirect()->route('admin.services.index')
            ->with('success', 'Service category updated.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $category = ServiceCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);

        return back()->with('success', $category->is_active ? 'Category activated.' : 'Category deactivated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = ServiceCategory::findOrFail($id);

        if ($category->bookings()->count() > 0) {
            return back()->with('error', 'Cannot delete a category with existing bookings. Deactivate it instead.');
        }

        $category->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Category deleted.');
    }
}
