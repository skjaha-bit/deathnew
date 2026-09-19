<?php

namespace Modules\Cms\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cms\Models\CmsContent;
use Modules\Core\Models\Language;

class CmsContentController extends Controller
{
    public const TYPES = ['page', 'help_panel', 'faq', 'education_article'];

    public function index(Request $request)
    {
        $items = CmsContent::query()
            ->when($request->filled('search'), fn ($q) => $q->where('module_key', 'like', '%'.$request->query('search').'%'))
            ->orderBy('sort_order')
            ->paginate(20)
            ->withQueryString();

        $languages = Language::query()->where('is_active', true)->orderByDesc('is_default')->get();

        return view('cms::admin.cms-contents.index', [
            'items' => $items,
            'languages' => $languages,
            'types' => self::TYPES,
        ]);
    }

    protected function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:'.implode(',', self::TYPES)],
            'module_key' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9_\-]+$/i'],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.*' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'array'],
            'body.en' => ['required', 'string'],
            'body.*' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());
        $data['is_active'] = $request->boolean('is_active', true);

        $item = CmsContent::query()->create($data);

        return response()->json(['res' => true, 'msg' => 'Created successfully.', 'item' => $item]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $item = CmsContent::query()->findOrFail($id);
        $data = $request->validate($this->rules());
        $data['is_active'] = $request->boolean('is_active', $item->is_active);

        $item->update($data);

        return response()->json(['res' => true, 'msg' => 'Updated successfully.', 'item' => $item]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $item = CmsContent::query()->findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);

        return response()->json(['res' => true, 'msg' => $item->is_active ? 'Activated.' : 'Deactivated.', 'is_active' => $item->is_active]);
    }

    public function destroy(int $id): JsonResponse
    {
        CmsContent::query()->findOrFail($id)->delete();

        return response()->json(['res' => true, 'msg' => 'Deleted successfully.']);
    }
}
