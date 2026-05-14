@csrf
@isset($category)
    @method('PUT')
@endisset

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Name (English) *</label>
        <input type="text" name="name_en" value="{{ old('name_en', $category->name_en ?? '') }}" required
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Name (Arabic) *</label>
        <input type="text" name="name_ar" value="{{ old('name_ar', $category->name_ar ?? '') }}" required dir="rtl"
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Name (Kurdish) *</label>
        <input type="text" name="name_ku" value="{{ old('name_ku', $category->name_ku ?? '') }}" required dir="rtl"
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description (English)</label>
        <textarea name="description_en" rows="2" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('description_en', $category->description_en ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description (Arabic)</label>
        <textarea name="description_ar" rows="2" dir="rtl" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('description_ar', $category->description_ar ?? '') }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description (Kurdish)</label>
        <textarea name="description_ku" rows="2" dir="rtl" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('description_ku', $category->description_ku ?? '') }}</textarea>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Icon Key *</label>
        <input type="text" name="icon" value="{{ old('icon', $category->icon ?? '') }}" required
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"
            placeholder="e.g. wrench, truck, battery">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Base Price ({{ config('pippip.currency') }}) *</label>
        <input type="number" step="0.01" min="0" name="base_price" value="{{ old('base_price', $category->base_price ?? '') }}" required
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}"
            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
    </div>
</div>

<div class="mt-4">
    <label class="inline-flex items-center">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1"
            {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}
            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
        <span class="ml-2 text-sm text-gray-700">Active</span>
    </label>
</div>
