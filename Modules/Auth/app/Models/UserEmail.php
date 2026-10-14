<?php

namespace Modules\Auth\Models;

use App\Models\User;
use App\Support\BlindIndex;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEmail extends Model
{
    protected $fillable = ['user_id', 'email', 'verified_at'];

    protected $hidden = ['email_hash'];

    protected $casts = [
        'email' => 'encrypted',
        'verified_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (UserEmail $userEmail) {
            if ($userEmail->isDirty('email')) {
                $userEmail->email_hash = BlindIndex::email($userEmail->email);
            }
        });
    }

    public function scopeWhereEmail(Builder $query, ?string $email): Builder
    {
        return $query->where('email_hash', BlindIndex::email($email));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
