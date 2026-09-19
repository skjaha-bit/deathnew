<?php

namespace Modules\Cms\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cms\Models\Banner;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $items = Banner::query()
            ->when($request->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$request->query('search').'%'))
            ->orderBy('sort_order')
            ->paginate(20)
            ->withQueryString();

        return view('cms::admin.banners.index', ['items' => $items]);
    }

    protected function rules(?int $ignoreId = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'link_url' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'image' => [$ignoreId ? 'nullable' : 'required', 'image', 'max:4096'],
        ];
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());
        $data['is_active'] = $request->boolean('is_active', true);
        $data['image_path'] = $request->file('image')->store('banners', 'public');
        unset($data['image']);

        $banner = Banner::query()->create($data);

        return response()->json(['res' => true, 'msg' => 'Created successfully.', 'item' => $banner]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $banner = Banner::query()->findOrFail($id);
        $data = $request->validate($this->rules($banner->id));
        $data['is_active'] = $request->boolean('is_active', $banner->is_active);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('banners', 'public');
        } else {
            unset($data['image']);
        }

        $banner->update($data);

        return response()->json(['res' => true, 'msg' => 'Updated successfully.', 'item' => $banner]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $banner = Banner::query()->findOrFail($id);
        $banner->update(['is_active' => ! $banner->is_active]);

        return response()->json(['res' => true, 'msg' => $banner->is_active ? 'Activated.' : 'Deactivated.', 'is_active' => $banner->is_active]);
    }

    public function destroy(int $id): JsonResponse
    {
        Banner::query()->findOrFail($id)->delete();

        return response()->json(['res' => true, 'msg' => 'Deleted successfully.']);
    }
}
