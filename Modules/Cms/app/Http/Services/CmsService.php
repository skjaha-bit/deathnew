<?php

namespace Modules\Cms\Http\Services;

use Illuminate\Support\Collection;
use Modules\Cms\Models\Banner;
use Modules\Cms\Models\CmsContent;

class CmsService
{
    /**
     * Active banners currently within their display window, ordered for display.
     */
    public function activeForDisplay(): Collection
    {
        return Banner::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * List active CMS contents of a given type, optionally scoped to a
     * module, translated into the requested locale.
     */
    public function list(string $type, ?string $moduleKey, string $locale): array
    {
        $contents = CmsContent::where('type', $type)
            ->where('is_active', true)
            ->when($moduleKey, fn ($q) => $q->where('module_key', $moduleKey))
            ->orderBy('sort_order')
            ->get();

        return $contents->map(fn (CmsContent $c) => [
            'id' => $c->id,
            'module_key' => $c->module_key,
            'title' => $c->localized('title', $locale),
            'body' => $c->localized('body', $locale),
        ])->all();
    }
}
