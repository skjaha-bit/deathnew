<?php

namespace App\Models;

use App\Support\BlindIndex;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Modules\Core\Models\Country;
use Modules\Core\Models\Language;
use Modules\Auth\Models\UserEmail;
use Modules\Auth\Models\UserPhone;
use Modules\Relatives\Models\Relative;
use Modules\Subscription\Models\Subscription;
use Modules\UserSubscriptions\Models\UserSubscription;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, LogsActivity, Notifiable, SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_SUSPENDED = 'suspended';

    public const STATUS_DECEASED = 'deceased';

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_code',
        'phone_number',
        'kennitala',
        'avatar',
        'status',
        'preferred_language_id',
        'address',
        'house_number',
        'city',
        'postal_code',
        'country_id',
        'marital_status',
        'gender',
        'newsletter_opt_in',
        'partner_id',
        'promo_code',
        'account_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_hash',
        'kennitala_hash',
        'phone_hash',
    ];

    /**
     * Keep the blind-index hashes in step with the encrypted identity columns
     * they stand in for (see App\Support\BlindIndex).
     */
    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->isDirty('email')) {
                $user->email_hash = BlindIndex::email($user->email);
            }

            if ($user->isDirty('kennitala')) {
                $user->kennitala_hash = BlindIndex::kennitala($user->kennitala);
            }

            if ($user->isDirty(['phone_code', 'phone_number'])) {
                $user->phone_hash = BlindIndex::phone($user->phone_code, $user->phone_number);
            }
        });
    }

    public function scopeWhereEmail(Builder $query, ?string $email): Builder
    {
        return $query->where('email_hash', BlindIndex::email($email));
    }

    public function scopeWhereKennitala(Builder $query, ?string $kennitala): Builder
    {
        return $query->where('kennitala_hash', BlindIndex::kennitala($kennitala));
    }

    public function scopeWherePhone(Builder $query, ?string $code, ?string $number): Builder
    {
        return $query->where('phone_hash', BlindIndex::phone($code, $number));
    }

    /**
     * Admin search: exact email / kennitala / phone via the blind indexes, or
     * a partial name match. Names are encrypted, so that part is matched
     * after decrypting in PHP.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);
        $needle = mb_strtolower($term);

        $nameMatches = static::query()->select(['id', 'name'])->lazyById(500)
            ->filter(fn (User $u) => str_contains(mb_strtolower((string) $u->name), $needle))
            ->pluck('id')
            ->all();

        // A null hash (e.g. a name has no digits) must not become "hash IS NULL".
        $hashes = array_filter([
            'email_hash' => BlindIndex::email($term),
            'kennitala_hash' => BlindIndex::kennitala($term),
            'phone_hash' => BlindIndex::phone('354', $term),
        ]);

        return $query->where(function (Builder $q) use ($nameMatches, $hashes) {
            $q->whereIn('id', $nameMatches);

            foreach ($hashes as $column => $hash) {
                $q->orWhere($column, $hash);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'name' => 'encrypted',
            'email' => 'encrypted',
            'phone_code' => 'encrypted',
            'phone_number' => 'encrypted',
            'kennitala' => 'encrypted',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'newsletter_opt_in' => 'boolean',
            'is_alive' => 'boolean',
            'life_status_checked_at' => 'datetime',
            'deceased_at' => 'datetime',
            'address' => 'encrypted',
            'house_number' => 'encrypted',
            'city' => 'encrypted',
            'postal_code' => 'encrypted',
            'marital_status' => 'encrypted',
            'gender' => 'encrypted',
            'promo_code' => 'encrypted',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // name/email are encrypted at rest; logging them would copy them into activity_log in plaintext.
            ->logOnly(['status'])
            ->logOnlyDirty();
    }

    public function preferredLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'preferred_language_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function relatives(): HasMany
    {
        return $this->hasMany(Relative::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(UserEmail::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(UserPhone::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function fullPhoneNumber(): string
    {
        return trim($this->phone_code.$this->phone_number);
    }
}
