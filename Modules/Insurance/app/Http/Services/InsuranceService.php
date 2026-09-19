<?php

namespace Modules\Insurance\Http\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\Insurance\Models\InsuranceCompany;
use Modules\Insurance\Models\InsuranceDocument;
use Modules\Insurance\Models\InsurancePolicy;
use Modules\Insurance\Models\InsuranceType;

class InsuranceService
{
    protected const WITH_RELATIONS = [
        'insuranceCompany',
        'types',
        'documents',
    ];

    public function listForUser(int $userId)
    {
        return InsurancePolicy::query()
            ->with(self::WITH_RELATIONS)
            ->where('user_id', $userId)
            ->get();
    }

    public function create(int $userId, array $data): InsurancePolicy
    {
        return DB::transaction(function () use ($userId, $data) {
            $policy = InsurancePolicy::query()->create([
                'user_id' => $userId,
                'insurance_company_id' => $data['insurance_company_id'] ?? null,
                'other_type_text' => $data['other_type_text'] ?? null,
            ]);

            if (array_key_exists('type_ids', $data)) {
                $policy->types()->sync($data['type_ids'] ?? []);
            }

            if (! empty($data['documents'])) {
                $this->addDocuments($policy, $data['documents']);
            }

            return $policy->fresh(self::WITH_RELATIONS);
        });
    }

    public function update(InsurancePolicy $policy, array $data): InsurancePolicy
    {
        return DB::transaction(function () use ($policy, $data) {
            $policy->update([
                'insurance_company_id' => $data['insurance_company_id'] ?? $policy->insurance_company_id,
                'other_type_text' => $data['other_type_text'] ?? $policy->other_type_text,
            ]);

            if (array_key_exists('type_ids', $data)) {
                $policy->types()->sync($data['type_ids'] ?? []);
            }

            if (! empty($data['documents'])) {
                $this->addDocuments($policy, $data['documents']);
            }

            return $policy->fresh(self::WITH_RELATIONS);
        });
    }

    public function delete(InsurancePolicy $policy): bool
    {
        foreach ($policy->documents as $document) {
            Storage::disk('public')->delete($document->file_path);
        }

        return (bool) $policy->delete();
    }

    /**
     * Active insurance companies available for selection.
     */
    public function activeCompanies()
    {
        return InsuranceCompany::query()->where('is_active', true)->orderBy('name')->get();
    }

    /**
     * Active insurance types available for selection.
     */
    public function activeTypes(): Collection
    {
        return InsuranceType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    /**
     * @param  UploadedFile[]  $files
     * @return Collection<int, InsuranceDocument>
     */
    public function addDocuments(InsurancePolicy $policy, array $files): Collection
    {
        return collect($files)->map(fn (UploadedFile $file) => $this->addDocument($policy, $file));
    }

    public function addDocument(InsurancePolicy $policy, UploadedFile $file): InsuranceDocument
    {
        $path = $file->store('insurance-documents/'.$policy->user_id, 'public');

        return $policy->documents()->create([
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ]);
    }

    public function deleteDocument(InsuranceDocument $document): bool
    {
        Storage::disk('public')->delete($document->file_path);

        return (bool) $document->delete();
    }

    /**
     * Update the policy's contact preference and log the request.
     * Stand-in for actually notifying a guardian/advisor, which is not wired up yet.
     */
    public function submitContactRequest(InsurancePolicy $policy, array $data): InsurancePolicy
    {
        $policy->update([
            'contact_preference' => $data['contact_preference'],
            'contact_message' => $data['contact_message'] ?? null,
        ]);

        Log::channel('api')->info('Insurance contact request dispatched', [
            'insurance_policy_id' => $policy->id,
            'user_id' => $policy->user_id,
            'contact_preference' => $policy->contact_preference,
        ]);

        return $policy->fresh(self::WITH_RELATIONS);
    }
}
