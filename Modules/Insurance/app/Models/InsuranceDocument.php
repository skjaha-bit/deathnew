<?php

namespace Modules\Insurance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsuranceDocument extends Model
{
    protected $fillable = [
        'insurance_policy_id',
        'file_path',
        'original_name',
        'size',
    ];

    protected $casts = [
        'original_name' => 'encrypted',
    ];

    public function insurancePolicy(): BelongsTo
    {
        return $this->belongsTo(InsurancePolicy::class);
    }
}
