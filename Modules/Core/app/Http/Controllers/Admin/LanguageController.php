<?php

namespace Modules\Core\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\Core\Models\Language;
use Modules\Core\Support\ValidationPatterns;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = Language::query()->orderByDesc('is_default')->get();

        return view('core::admin.languages.index', compact('languages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:5', 'regex:/^[a-z]{2}(-[A-Z]{2})?$/', 'unique:languages,code'],
            'name' => ['required', 'string', 'max:255', 'regex:'.ValidationPatterns::NAME],
        ], [
            'code.regex' => 'The code must be a valid ISO language code, e.g. en or is.',
            'name.regex' => 'The name may only contain letters, spaces, apostrophes, hyphens and periods.',
        ]);

        Language::query()->create($data + ['is_active' => true]);

        return back()->with('status', 'Language added.');
    }

    public function toggleActive(Language $language)
    {
        if ($language->is_default && $language->is_active) {
            return back()->with('error', 'Cannot disable the default language.');
        }

        $language->update(['is_active' => ! $language->is_active]);

        return back()->with('status', 'Language updated.');
    }

    public function makeDefault(Language $language)
    {
        Language::query()->where('id', '!=', $language->id)->update(['is_default' => false]);
        $language->update(['is_default' => true, 'is_active' => true]);

        return back()->with('status', 'Default language updated.');
    }

    public function destroy(Language $language)
    {
        if ($language->is_default) {
            return back()->with('error', 'Cannot delete the default language.');
        }

        $language->delete();

        return back()->with('status', 'Language removed.');
    }
}
