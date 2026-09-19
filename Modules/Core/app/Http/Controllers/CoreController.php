<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Services\CoreService;
use Modules\Core\Traits\ApiResponse;

class CoreController extends Controller
{
    use ApiResponse;

    public function __construct(protected CoreService $coreService) {}

    /**
     * Public list of active languages for the client's language switcher.
     */
    public function languages()
    {
        return $this->success($this->coreService->activeLanguages());
    }

    /**
     * Public list of active countries for registration/profile country dropdowns.
     */
    public function countries()
    {
        return $this->success($this->coreService->activeCountries());
    }

    /**
     * Public string table for the client app: GET /api/v1/translations?locale=is&group=app
     */
    public function translations(Request $request)
    {
        $locale = $request->query('locale', 'en');
        $group = $request->query('group');

        $language = $this->coreService->findActiveLanguage($locale);

        if (! $language) {
            return $this->error('Language not found or inactive.', 404);
        }

        return $this->success($this->coreService->translationsForLanguage($language, $group));
    }

    /**
     * All active languages' string tables in one response, e.g. { "en": {...}, "is": {...} }.
     * Lets a client cache the full set (English + Icelandic) in a single call.
     */
    public function allTranslations(Request $request)
    {
        $group = $request->query('group');

        return $this->success($this->coreService->allTranslations($group));
    }
}
