<?php

namespace Modules\Relatives\Http\Services;

use Illuminate\Support\Collection;
use Modules\Insurance\Models\InsuranceCompany;
use Modules\Relatives\Models\Guardian;
use Modules\Relatives\Models\Pet;

class PetService
{
    protected const WITH_RELATIONS = [
        'insuranceCompany',
        'designatedGuardian',
        'alternativeGuardian',
    ];

    protected const FILLABLE_KEYS = [
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

    public function listForUser(int $userId)
    {
        return Pet::query()
            ->with(self::WITH_RELATIONS)
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function create(int $userId, array $data): Pet
    {
        $pet = Pet::query()->create([
            'user_id' => $userId,
            ...array_intersect_key($data, array_flip(self::FILLABLE_KEYS)),
        ]);

        return $pet->fresh(self::WITH_RELATIONS);
    }

    public function update(Pet $pet, array $data): Pet
    {
        $pet->update(array_intersect_key($data, array_flip(self::FILLABLE_KEYS)));

        return $pet->fresh(self::WITH_RELATIONS);
    }

    public function delete(Pet $pet): bool
    {
        return (bool) $pet->delete();
    }

    /**
     * Active guardians available for selection as designated/alternative guardian.
     */
    public function guardianOptions(): Collection
    {
        return Guardian::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    /**
     * Active insurance providers available for selection (shared with Insurance).
     */
    public function insuranceCompanyOptions(): Collection
    {
        return InsuranceCompany::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }
}
