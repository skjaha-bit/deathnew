<?php

namespace Modules\Cms\Models;

use Illuminate\Database\Eloquent\Model;

class CmsContent extends Model
{
    protected $fillable = [
        'type',
        'module_key',
        'title',
        'body',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'title' => 'array',
        'body' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Return the given translatable field's value in the requested locale,
     * falling back to English when the requested locale is missing.
     */
    public function localized(string $field, string $locale): ?string
    {
        return $this->{$field}[$locale] ?? $this->{$field}['en'] ?? null;
    }
}
