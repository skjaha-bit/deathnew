<?php

namespace Modules\CommercialBank\Models;

use Illuminate\Database\Eloquent\Model;

class BondType extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
