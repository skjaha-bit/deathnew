<?php

namespace Modules\Core\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\Core\Models\Setting;
use Modules\Core\Support\ValidationPatterns;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::query()->orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('core::admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string', 'max:2000', 'regex:'.ValidationPatterns::SAFE_TEXT],
        ], [
            'settings.*.regex' => 'Setting values may not contain < or > characters.',
        ]);

        foreach ($data['settings'] as $key => $value) {
            Setting::query()->where('key', $key)->update(['value' => $value]);
        }

        return back()->with('status', 'Settings updated.');
    }
}
