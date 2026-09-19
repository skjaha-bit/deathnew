<?php

namespace Modules\InheritanceMatters\Http\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\InheritanceMatters\Models\InheritanceMatter;

class InheritanceMatterService
{
    public function getOrCreateForUser(int $userId): InheritanceMatter
    {
        return InheritanceMatter::query()->firstOrCreate(['user_id' => $userId]);
    }

    public function save(InheritanceMatter $matter, array $data, ?UploadedFile $file = null): InheritanceMatter
    {
        return DB::transaction(function () use ($matter, $data, $file) {
            $fields = collect($data)->except(['agents', 'will_file'])->all();

            if ($file) {
                if ($matter->will_file_path) {
                    Storage::disk('public')->delete($matter->will_file_path);
                }

                $fields['will_file_path'] = $file->store('inheritance-matters/'.$matter->user_id, 'public');
            }

            $matter->update($fields);

            if (array_key_exists('agents', $data)) {
                $matter->agents()->delete();

                foreach ($data['agents'] ?? [] as $index => $agent) {
                    $matter->agents()->create([
                        'name' => $agent['name'],
                        'phone' => $agent['phone'] ?? null,
                        'email' => $agent['email'] ?? null,
                        'social_security_number' => $agent['social_security_number'] ?? null,
                        'address' => $agent['address'] ?? null,
                        'sort_order' => $agent['sort_order'] ?? $index,
                    ]);
                }
            }

            return $matter->fresh('agents');
        });
    }
}
