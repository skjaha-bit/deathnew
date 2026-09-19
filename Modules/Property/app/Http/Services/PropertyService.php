<?php

namespace Modules\Property\Http\Services;

use Illuminate\Support\Collection;
use Modules\CommercialBank\Models\LoanType;
use Modules\Insurance\Models\InsuranceCompany;
use Modules\Property\Models\Property;
use Modules\Property\Models\PropertyType;

class PropertyService
{
    protected const WITH_RELATIONS = [
        'propertyType',
        'loanType',
        'insuranceCompany',
    ];

    protected const FILLABLE_KEYS = [
        'property_type_id',
        'name',
        'location',
        'asset_registration_number',
        'ownership',
        'co_owner_name',
        'co_owner_contact',
        'deeds_location',
        'has_will',
        'lender',
        'loan_number',
        'loan_outstanding_balance',
        'loan_type_id',
        'insurance_company_id',
        'policy_number',
        'has_loan_protection',
        'notes_text',
    ];

    public function listForUser(int $userId)
    {
        return Property::query()
            ->with(self::WITH_RELATIONS)
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function create(int $userId, array $data): Property
    {
        $property = Property::query()->create([
            'user_id' => $userId,
            ...array_intersect_key($data, array_flip(self::FILLABLE_KEYS)),
        ]);

        return $property->fresh(self::WITH_RELATIONS);
    }

    public function update(Property $property, array $data): Property
    {
        $property->update(array_intersect_key($data, array_flip(self::FILLABLE_KEYS)));

        return $property->fresh(self::WITH_RELATIONS);
    }

    public function delete(Property $property): bool
    {
        return (bool) $property->delete();
    }

    /**
     * Active property types available for selection.
     */
    public function propertyTypeOptions(): Collection
    {
        return PropertyType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    /**
     * Active loan types available for selection (shared with CommercialBank).
     */
    public function loanTypeOptions(): Collection
    {
        return LoanType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    /**
     * Active insurance providers available for selection (shared with Insurance).
     */
    public function insuranceCompanyOptions(): Collection
    {
        return InsuranceCompany::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }
}
