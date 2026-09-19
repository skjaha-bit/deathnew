<?php

namespace Modules\AfterMyDay\Models;

use Illuminate\Database\Eloquent\Model;

class ArrangementType extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
