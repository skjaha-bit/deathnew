<?php

namespace Modules\CommercialBank\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    /** Choosing this bank asks the user for the bank's SWIFT code. */
    public const SWIFT_REQUIRED_ID = 6;

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
