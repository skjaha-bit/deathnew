<?php

namespace Modules\Auth\Models;

use App\Models\User;
use App\Support\BlindIndex;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPhone extends Model
{
    protected $fillable = ['user_id', 'phone_code', 'phone_number', 'verified_at'];

    protected $hidden = ['phone_hash'];

    protected $casts = [
        'phone_code' => 'encrypted',
        'phone_number' => 'encrypted',
        'verified_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (UserPhone $userPhone) {
            if ($userPhone->isDirty(['phone_code', 'phone_number'])) {
                $userPhone->phone_hash = BlindIndex::phone($userPhone->phone_code, $userPhone->phone_number);
            }
        });
    }

    public function scopeWherePhone(Builder $query, ?string $code, ?string $number): Builder
    {
        return $query->where('phone_hash', BlindIndex::phone($code, $number));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
