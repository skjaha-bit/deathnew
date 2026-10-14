<?php

namespace Modules\UserSubscriptions\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_type_id',
        'name',
        'associated_email',
        'billing_cycle',
        'amount',
        'power_of_attorney_termination',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'power_of_attorney_termination' => 'boolean',
        'name' => 'encrypted',
        'associated_email' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionType(): BelongsTo
    {
        return $this->belongsTo(SubscriptionType::class);
    }
}
