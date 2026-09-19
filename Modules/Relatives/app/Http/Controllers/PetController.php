<?php

namespace Modules\Relatives\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponse;
use Modules\Insurance\Models\InsuranceCompany;
use Modules\Relatives\Http\Requests\StorePetRequest;
use Modules\Relatives\Http\Requests\UpdatePetRequest;
use Modules\Relatives\Http\Services\PetService;
use Modules\Relatives\Models\Guardian;
use Modules\Relatives\Models\Pet;

class PetController extends Controller
{
    use ApiResponse;

    public function __construct(protected PetService $petService) {}

    public function guardians()
    {
        $guardians = $this->petService->guardianOptions();

        return $this->success($guardians->map(fn (Guardian $g) => $this->formatOption($g))->values()->all());
    }

    public function insuranceProviders()
    {
        $companies = $this->petService->insuranceCompanyOptions();

        return $this->success($companies->map(fn (InsuranceCompany $c) => $this->formatOption($c))->values()->all());
    }

    public function index(Request $request)
    {
        $pets = $this->petService->listForUser($request->user()->id);

        return $this->success($pets->map(fn (Pet $pet) => $this->formatPet($pet))->values()->all());
    }

    public function store(StorePetRequest $request)
    {
        $pet = $this->petService->create($request->user()->id, $request->validated());

        return $this->success($this->formatPet($pet), 'Pet added.', 201);
    }

    public function show(Request $request, Pet $pet)
    {
        abort_if($pet->user_id !== $request->user()->id, 403, 'You do not have access to this pet.');

        $pet->load(['insuranceCompany', 'designatedGuardian', 'alternativeGuardian']);

        return $this->success($this->formatPet($pet));
    }

    public function update(UpdatePetRequest $request)
    {
        $data = $request->validated();

        $pet = Pet::query()->findOrFail($data['id']);

        abort_if($pet->user_id !== $request->user()->id, 403, 'You do not have access to this pet.');

        $pet = $this->petService->update($pet, $data);

        return $this->success($this->formatPet($pet), 'Pet updated.');
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:pets,id']]);

        $pet = Pet::query()->findOrFail($request->input('id'));

        abort_if($pet->user_id !== $request->user()->id, 403, 'You do not have access to this pet.');

        $this->petService->delete($pet);

        return $this->success(null, 'Pet removed.');
    }

    private function formatOption(Guardian|InsuranceCompany $option): array
    {
        return [
            'id' => $option->id,
            'name' => $option->name,
        ];
    }

    private function formatPet(Pet $pet): array
    {
        return [
            'id' => $pet->id,
            'user_id' => $pet->user_id,

            'name' => $pet->name,
            'animal_type' => $pet->animal_type,
            'breed' => $pet->breed,
            'birth_year' => $pet->birth_year,
            'birth_month' => $pet->birth_month,
            'identification_number' => $pet->identification_number,

            'vet_clinic_name' => $pet->vet_clinic_name,
            'vet_contact_person' => $pet->vet_contact_person,
            'vet_contact_number' => $pet->vet_contact_number,
            'medical_conditions' => $pet->medical_conditions,
            'dietary_needs' => $pet->dietary_needs,
            'insurance_company_id' => $pet->insurance_company_id,
            'insurance_company_name' => $pet->insuranceCompany?->name,
            'policy_number' => $pet->policy_number,

            'designated_guardian_id' => $pet->designated_guardian_id,
            'designated_guardian_name' => $pet->designatedGuardian?->name,
            'guardian_agreed' => $pet->guardian_agreed,
            'alternative_guardian_id' => $pet->alternative_guardian_id,
            'alternative_guardian_name' => $pet->alternativeGuardian?->name,
            'final_wishes' => $pet->final_wishes,

            'created_at' => $pet->created_at?->toIso8601String(),
            'updated_at' => $pet->updated_at?->toIso8601String(),
        ];
    }
}
