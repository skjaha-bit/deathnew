<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name', 'short_code', 'country_code', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
