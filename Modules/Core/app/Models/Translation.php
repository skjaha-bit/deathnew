<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Translation extends Model
{
    protected $fillable = ['key', 'group'];

    public function values(): HasMany
    {
        return $this->hasMany(TranslationValue::class);
    }
}
