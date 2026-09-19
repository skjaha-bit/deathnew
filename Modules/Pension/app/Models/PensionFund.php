<?php

namespace Modules\Pension\Models;

use Illuminate\Database\Eloquent\Model;

class PensionFund extends Model
{
    protected $fillable = [
        'name',
        'email',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
