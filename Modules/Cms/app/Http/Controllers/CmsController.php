<?php

namespace Modules\Cms\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Cms\Http\Services\CmsService;
use Modules\Cms\Models\Banner;
use Modules\Core\Traits\ApiResponse;

class CmsController extends Controller
{
    use ApiResponse;

    public function __construct(protected CmsService $cmsService) {}

    /**
     * Public listing of active marketing banners.
     */
    public function banners()
    {
        $banners = $this->cmsService->activeForDisplay()->map(fn (Banner $banner) => [
            'id' => $banner->id,
            'title' => $banner->title,
            'image_url' => $banner->image_path ? asset('storage/'.$banner->image_path) : null,
            'link_url' => $banner->link_url,
        ])->all();

        return $this->success($banners, 'Banners retrieved successfully.');
    }

    /**
     * Public listing of CMS contents (help panels, FAQs, education articles,
     * pages), optionally scoped to a module and translated into a locale.
     */
    public function contents(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:page,help_panel,faq,education_article'],
            'module_key' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9_\-]+$/i'],
            'locale' => ['nullable', 'string', 'regex:/^[a-z]{2}(-[A-Z]{2})?$/'],
        ]);

        $contents = $this->cmsService->list(
            $validated['type'],
            $validated['module_key'] ?? null,
            $validated['locale'] ?? 'en',
        );

        return $this->success($contents, 'Content retrieved successfully.');
    }
}
