<?php

namespace Modules\Insurance\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Traits\ApiResponse;
use Modules\Insurance\Http\Requests\ContactRequestRequest;
use Modules\Insurance\Http\Requests\StoreInsurancePolicyRequest;
use Modules\Insurance\Http\Requests\UpdateInsurancePolicyRequest;
use Modules\Insurance\Http\Requests\UploadInsuranceDocumentRequest;
use Modules\Insurance\Http\Services\InsuranceService;
use Modules\Insurance\Models\InsuranceCompany;
use Modules\Insurance\Models\InsuranceDocument;
use Modules\Insurance\Models\InsurancePolicy;
use Modules\Insurance\Models\InsuranceType;

class InsuranceController extends Controller
{
    use ApiResponse;

    protected const WITH_RELATIONS = [
        'insuranceCompany',
        'types',
        'documents',
    ];

    public function __construct(protected InsuranceService $insuranceService) {}

    public function index(Request $request)
    {
        $policies = $this->insuranceService->listForUser($request->user()->id);

        return $this->success($policies->map(fn (InsurancePolicy $policy) => $this->formatPolicy($policy))->values()->all());
    }

    public function store(StoreInsurancePolicyRequest $request)
    {
        $data = $request->validated();
        $data['documents'] = $request->file('documents', []);

        $policy = $this->insuranceService->create($request->user()->id, $data);

        return $this->success($this->formatPolicy($policy), 'Insurance policy added.', 201);
    }

    public function show(Request $request, InsurancePolicy $policy)
    {
        abort_if($policy->user_id !== $request->user()->id, 403, 'You do not have access to this insurance policy.');

        $policy->load(self::WITH_RELATIONS);

        return $this->success($this->formatPolicy($policy));
    }

    public function update(UpdateInsurancePolicyRequest $request)
    {
        $data = $request->validated();

        $policy = InsurancePolicy::query()->findOrFail($data['id']);

        abort_if($policy->user_id !== $request->user()->id, 403, 'You do not have access to this insurance policy.');

        $data['documents'] = $request->file('documents', []);

        $policy = $this->insuranceService->update($policy, $data);

        return $this->success($this->formatPolicy($policy), 'Insurance policy updated.');
    }

    public function destroy(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:insurance_policies,id']]);

        $policy = InsurancePolicy::query()->findOrFail($request->input('id'));

        abort_if($policy->user_id !== $request->user()->id, 403, 'You do not have access to this insurance policy.');

        $this->insuranceService->delete($policy);

        return $this->success(null, 'Insurance policy removed.');
    }

    public function storeContactRequest(ContactRequestRequest $request)
    {
        $data = $request->validated();

        $policy = InsurancePolicy::query()->findOrFail($data['id']);

        abort_if($policy->user_id !== $request->user()->id, 403, 'You do not have access to this insurance policy.');

        $policy = $this->insuranceService->submitContactRequest($policy, $data);

        return $this->success($this->formatPolicy($policy), 'Contact request submitted.');
    }

    public function storeDocument(UploadInsuranceDocumentRequest $request)
    {
        $policy = InsurancePolicy::query()->findOrFail($request->input('id'));

        abort_if($policy->user_id !== $request->user()->id, 403, 'You do not have access to this insurance policy.');

        $documents = $this->insuranceService->addDocuments($policy, $request->file('documents'));

        return $this->success($documents->map(fn (InsuranceDocument $document) => $this->formatDocument($document))->all(), 'Documents uploaded.', 201);
    }

    public function destroyDocument(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:insurance_documents,id']]);

        $document = InsuranceDocument::query()->findOrFail($request->input('id'));

        abort_if($document->insurancePolicy->user_id !== $request->user()->id, 403, 'You do not have access to this document.');

        $this->insuranceService->deleteDocument($document);

        return $this->success(null, 'Document removed.');
    }

    public function companies()
    {
        $companies = $this->insuranceService->activeCompanies();

        return $this->success($companies->map(fn (InsuranceCompany $company) => $this->formatCompany($company))->values()->all());
    }

    public function insuranceTypes()
    {
        $types = $this->insuranceService->activeTypes();

        return $this->success($types->map(fn (InsuranceType $type) => $this->formatType($type))->values()->all());
    }

    private function formatPolicy(InsurancePolicy $policy): array
    {
        return [
            'id' => $policy->id,
            'user_id' => $policy->user_id,
            'insurance_company_id' => $policy->insurance_company_id,
            'insurance_company_name' => $policy->insuranceCompany?->name,
            'types' => $policy->types->map(fn (InsuranceType $type) => $this->formatType($type))->values()->all(),
            'other_type_text' => $policy->other_type_text,
            'contact_preference' => $policy->contact_preference,
            'contact_message' => $policy->contact_message,
            'documents' => $policy->documents->map(fn (InsuranceDocument $document) => $this->formatDocument($document))->values()->all(),
            'created_at' => $policy->created_at?->toIso8601String(),
            'updated_at' => $policy->updated_at?->toIso8601String(),
        ];
    }

    private function formatCompany(?InsuranceCompany $company): array
    {
        return [
            'id' => $company?->id,
            'name' => $company?->name,
        ];
    }

    private function formatType(InsuranceType $type): array
    {
        return [
            'id' => $type->id,
            'name' => $type->name,
        ];
    }

    private function formatDocument(InsuranceDocument $document): array
    {
        return [
            'id' => $document->id,
            'original_name' => $document->original_name,
            'size' => $document->size,
            'url' => asset('storage/'.$document->file_path),
            'created_at' => $document->created_at?->toIso8601String(),
        ];
    }
}
