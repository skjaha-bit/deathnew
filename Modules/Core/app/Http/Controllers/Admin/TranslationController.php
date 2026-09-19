<?php

namespace Modules\Core\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\Core\Models\Language;
use Modules\Core\Models\Translation;
use Modules\Core\Models\TranslationValue;

class TranslationController extends Controller
{
    public function index(Request $request)
    {
        $languages = Language::query()->where('is_active', true)->orderByDesc('is_default')->get();

        $translations = Translation::query()
            ->with('values')
            ->when($request->filled('search'), fn ($q) => $q->where('key', 'like', '%'.$request->query('search').'%'))
            ->orderBy('key')
            ->paginate(50)
            ->withQueryString();

        return view('core::admin.translations.index', compact('translations', 'languages'));
    }

    /**
     * Bulk save every row of the language-strings table in one request
     * (mirrors the "Save All" pattern: rows without an id are inserted,
     * rows with an id are updated; each row carries one value per active language).
     */
    public function saveAll(Request $request)
    {
        $rows = $request->input('rows', []);
        $languages = Language::query()->where('is_active', true)->get()->keyBy('code');

        foreach ($rows as $row) {
            $key = trim($row['key'] ?? '');

            if ($key === '' && empty($row['id'])) {
                continue;
            }

            if (! empty($row['id'])) {
                $translation = Translation::query()->find($row['id']);

                if (! $translation) {
                    continue;
                }

                if ($key !== '') {
                    $translation->update(['key' => $key, 'group' => $row['group'] ?? $translation->group]);
                }
            } else {
                $translation = Translation::query()->create([
                    'key' => $key,
                    'group' => $row['group'] ?? 'app',
                ]);
            }

            foreach ($row['values'] ?? [] as $code => $value) {
                $language = $languages->get($code);

                if (! $language) {
                    continue;
                }

                TranslationValue::query()->updateOrCreate(
                    ['translation_id' => $translation->id, 'language_id' => $language->id],
                    ['value' => $value]
                );
            }
        }

        return response()->json(['res' => true, 'msg' => 'Saved successfully']);
    }

    public function destroy(Translation $translation)
    {
        $translation->delete();

        return back()->with('status', 'Translation key removed.');
    }
}
