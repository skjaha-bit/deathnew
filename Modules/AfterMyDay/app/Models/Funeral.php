<?php

namespace Modules\AfterMyDay\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Funeral extends Model
{
    protected $fillable = [
        'user_id',
        'ceremony_type_id',
        'arrangement_type_id',
        'other_ceremony_options',
        'funeral_service_type_id',
        'other_thing',
        'location',
        'laying_place',
        'clothing_request',
        'event_manager',
        'guests',
        'pallbearer_request',
        'coffin_design',
        'coffin_decorations',
        'coffin_decorations_detail',
        'charity_case',
        'another_thing',
        'notes',
    ];

    protected $casts = [
        'coffin_decorations' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ceremonyType(): BelongsTo
    {
        return $this->belongsTo(CeremonyType::class);
    }

    public function arrangementType(): BelongsTo
    {
        return $this->belongsTo(ArrangementType::class);
    }

    public function funeralServiceType(): BelongsTo
    {
        return $this->belongsTo(FuneralServiceType::class);
    }

    public function music(): HasMany
    {
        return $this->hasMany(FuneralMusic::class)->orderBy('sort_order');
    }
}
