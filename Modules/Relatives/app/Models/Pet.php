<?php

namespace Modules\Relatives\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Insurance\Models\InsuranceCompany;

class Pet extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'animal_type',
        'breed',
        'birth_year',
        'birth_month',
        'identification_number',
        'vet_clinic_name',
        'vet_contact_person',
        'vet_contact_number',
        'medical_conditions',
        'dietary_needs',
        'insurance_company_id',
        'policy_number',
        'designated_guardian_id',
        'guardian_agreed',
        'alternative_guardian_id',
        'final_wishes',
    ];

    protected $casts = [
        'guardian_agreed' => 'boolean',
        'birth_year' => 'integer',
        'birth_month' => 'integer',
        'name' => 'encrypted',
        'animal_type' => 'encrypted',
        'breed' => 'encrypted',
        'identification_number' => 'encrypted',
        'vet_clinic_name' => 'encrypted',
        'vet_contact_person' => 'encrypted',
        'vet_contact_number' => 'encrypted',
        'medical_conditions' => 'encrypted',
        'dietary_needs' => 'encrypted',
        'policy_number' => 'encrypted',
        'final_wishes' => 'encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function insuranceCompany(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    public function designatedGuardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'designated_guardian_id');
    }

    public function alternativeGuardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'alternative_guardian_id');
    }
}
