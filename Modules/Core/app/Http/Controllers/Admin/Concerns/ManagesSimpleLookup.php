<?php

namespace Modules\Core\Http\Controllers\Admin\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Support\ValidationPatterns;

/**
 * Generic admin CRUD for "name + is_active [+ a few scalar fields]" lookup
 * tables (banks, insurance types, pension funds, ...).
 *
 * The consuming controller must declare these properties itself (PHP does
 * not allow a trait to declare a property with a different default than
 * the composing class's declaration of the same property):
 *
 *   protected string $model;             // e.g. Bank::class
 *   protected string $viewPrefix;        // e.g. 'commercialbank::admin.banks.index'
 *   protected string $routeName;         // e.g. 'admin.banks'
 *   protected array $extraFields = [];   // extra validation rules, e.g. ['email' => ['nullable','email']]
 *   protected array $extraColumns = [];  // extra table columns, e.g. ['email' => 'Email']
 */
trait ManagesSimpleLookup
{
    public function index(Request $request)
    {
        $items = $this->model::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->query('search').'%'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view($this->viewPrefix, [
            'items' => $items,
            'routeName' => $this->routeName,
            'extraColumns' => $this->extraColumns ?? [],
        ]);
    }

    protected function rules(?int $ignoreId = null): array
    {
        $table = (new $this->model)->getTable();

        $uniqueRule = 'unique:'.$table.',name'.($ignoreId ? ','.$ignoreId : '');

        return array_merge([
            'name' => ['required', 'string', 'max:255', 'regex:'.ValidationPatterns::ORG_NAME, $uniqueRule],
            'is_active' => ['nullable', 'boolean'],
        ], $this->extraFields ?? []);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->rules());
        $data['is_active'] = $request->boolean('is_active', true);

        $item = $this->model::query()->create($data);

        return response()->json(['res' => true, 'msg' => 'Created successfully.', 'item' => $item]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $item = $this->model::query()->findOrFail($id);
        $data = $request->validate($this->rules($item->id));
        $data['is_active'] = $request->boolean('is_active', $item->is_active);

        $item->update($data);

        return response()->json(['res' => true, 'msg' => 'Updated successfully.', 'item' => $item]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $item = $this->model::query()->findOrFail($id);
        $item->update(['is_active' => ! $item->is_active]);

        return response()->json(['res' => true, 'msg' => $item->is_active ? 'Activated.' : 'Deactivated.', 'is_active' => $item->is_active]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->model::query()->findOrFail($id)->delete();

        return response()->json(['res' => true, 'msg' => 'Deleted successfully.']);
    }
}
