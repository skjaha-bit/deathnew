<?php

namespace Modules\Property\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponse;
use Modules\CommercialBank\Models\LoanType;
use Modules\Insurance\Models\InsuranceCompany;
use Modules\Property\Http\Requests\StorePropertyRequest;
use Modules\Property\Http\Requests\UpdatePropertyRequest;
use Modules\Property\Http\Services\PropertyService;
use Modules\Property\Models\Property;
use Modules\Property\Models\PropertyType;

class PropertyController extends Controller
{
    use ApiResponse;

    protected const WITH_RELATIONS = [
        'propertyType',
        'loanType',
        'insuranceCompany',
    ];

    public function __construct(protected PropertyService $propertyService) {}

    public function propertyTypes()
    {
        $types = $this->propertyService->propertyTypeOptions();

        return $this->success($types->map(fn (PropertyType $type) => $this->formatOption($type))->values()->all());
    }

    public function loanTypes()
    {
        $types = $this->propertyService->loanTypeOptions();

        return $this->success($types->map(fn (LoanType $type) => $this->formatOption($type))->values()->all());
    }

    public function insuranceProviders()
    {
        $companies = $this->propertyService->insuranceCompanyOptions();

        return $this->success($companies->map(fn (InsuranceCompany $company) => $this->formatOption($company))->values()->all());
    }

    public function index(Request $request)
    {
        $properties = $this->propertyService->listForUser($request->user()->id);

        return $this->success($properties->map(fn (Property $property) => $this->formatProperty($property))->values()->all());
    }

    public function store(StorePropertyRequest $request)
    {
        $property = $this->propertyService->create($request->user()->id, $request->validated());

        return $this->success($this->formatProperty($property), 'Property added.', 201);
    }

    public function show(Request $request, Property $property)
    {
        abort_if($property->user_id !== $request->user()->id, 403, 'You do not have access to this property.');

        $property->load(self::WITH_RELATIONS);

        return $this->success($this->formatProperty($property));
    }

    public function update(UpdatePropertyRequest $request)
    {
        $data = $request->validated();

        $property = Property::query()->findOrFail($data['id']);

        abort_if($property->user_id !== $request->user()->id, 403, 'You do not have access to this property.');

        $property = $this->propertyService->update($property, $data);

        return $this->success($this->formatProperty($property), 'Property updated.');
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:properties,id']]);

        $property = Property::query()->findOrFail($request->input('id'));

        abort_if($property->user_id !== $request->user()->id, 403, 'You do not have access to this property.');

        $this->propertyService->delete($property);

        return $this->success(null, 'Property removed.');
    }

    private function formatOption(PropertyType|LoanType|InsuranceCompany $option): array
    {
        return [
            'id' => $option->id,
            'name' => $option->name,
        ];
    }

    private function formatProperty(Property $property): array
    {
        return [
            'id' => $property->id,
            'user_id' => $property->user_id,
            'property_type_id' => $property->property_type_id,
            'property_type_name' => $property->propertyType?->name,
            'name' => $property->name,
            'location' => $property->location,
            'asset_registration_number' => $property->asset_registration_number,
            'ownership' => $property->ownership,
            'co_owner_name' => $property->co_owner_name,
            'co_owner_contact' => $property->co_owner_contact,
            'deeds_location' => $property->deeds_location,
            'has_will' => $property->has_will,

            'lender' => $property->lender,
            'loan_number' => $property->loan_number,
            'loan_outstanding_balance' => $property->loan_outstanding_balance,
            'loan_type_id' => $property->loan_type_id,
            'loan_type_name' => $property->loanType?->name,
            'insurance_company_id' => $property->insurance_company_id,
            'insurance_company_name' => $property->insuranceCompany?->name,
            'policy_number' => $property->policy_number,
            'has_loan_protection' => $property->has_loan_protection,

            'notes_text' => $property->notes_text,
            'created_at' => $property->created_at?->toIso8601String(),
            'updated_at' => $property->updated_at?->toIso8601String(),
        ];
    }
}
