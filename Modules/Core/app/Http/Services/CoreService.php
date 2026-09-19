<?php

namespace Modules\Core\Http\Services;

use Illuminate\Support\Collection;
use Modules\Core\Models\Country;
use Modules\Core\Models\Language;
use Modules\Core\Models\TranslationValue;

class CoreService
{
    /**
     * Active languages for the client's language switcher.
     */
    public function activeLanguages(): Collection
    {
        return Language::query()->where('is_active', true)->get();
    }

    /**
     * Active countries for registration/profile country dropdowns.
     */
    public function activeCountries(): Collection
    {
        return Country::query()->where('is_active', true)->orderBy('name')->get();
    }

    /**
     * Find a single active language by locale code, or null.
     */
    public function findActiveLanguage(string $locale): ?Language
    {
        return Language::query()->where('code', $locale)->where('is_active', true)->first();
    }

    /**
     * Flat key => value string table for one language, e.g. { "welcome": "Welcome" }.
     */
    public function translationsForLanguage(Language $language, ?string $group = null): Collection
    {
        return TranslationValue::query()
            ->where('language_id', $language->id)
            ->whereHas('translation', function ($q) use ($group) {
                if ($group) {
                    $q->where('group', $group);
                }
            })
            ->with('translation')
            ->get()
            ->mapWithKeys(fn ($value) => [$value->translation->key => $value->value]);
    }

    /**
     * All active languages' string tables in one response, e.g. { "en": {...}, "is": {...} }.
     */
    public function allTranslations(?string $group = null): array
    {
        $languages = $this->activeLanguages();

        $values = TranslationValue::query()
            ->whereIn('language_id', $languages->pluck('id'))
            ->whereHas('translation', function ($q) use ($group) {
                if ($group) {
                    $q->where('group', $group);
                }
            })
            ->with(['translation', 'language'])
            ->get();

        $result = $languages->mapWithKeys(fn ($language) => [$language->code => []])->toArray();

        foreach ($values as $value) {
            $result[$value->language->code][$value->translation->key] = $value->value;
        }

        return $result;
    }
}
