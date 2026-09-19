<?php

namespace Modules\AfterMyDay\Http\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\AfterMyDay\Models\ArrangementType;
use Modules\AfterMyDay\Models\CeremonyType;
use Modules\AfterMyDay\Models\DeathNotice;
use Modules\AfterMyDay\Models\FarewellDrink;
use Modules\AfterMyDay\Models\Funeral;
use Modules\AfterMyDay\Models\FuneralServiceType;
use Modules\AfterMyDay\Models\LifetimeAchievement;
use Modules\AfterMyDay\Models\Memory;

class AfterMyDayService
{
    /**
     * Active ceremony types available for the "Ceremony" dropdown.
     */
    public function ceremonyTypeOptions(): Collection
    {
        return CeremonyType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    /**
     * Active arrangement types available for the "Arrangement" dropdown.
     */
    public function arrangementTypeOptions(): Collection
    {
        return ArrangementType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    /**
     * Active funeral service types available for the "Funeral services" dropdown.
     */
    public function funeralServiceTypeOptions(): Collection
    {
        return FuneralServiceType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    public function getOrCreateDeathNoticeForUser(int $userId): DeathNotice
    {
        return DeathNotice::query()->firstOrCreate(['user_id' => $userId]);
    }

    public function updateDeathNotice(DeathNotice $notice, array $data): DeathNotice
    {
        return DB::transaction(function () use ($notice, $data) {
            $notice->update(collect($data)->except('relatives')->all());

            if (array_key_exists('relatives', $data)) {
                $notice->relatives()->delete();

                foreach ($data['relatives'] ?? [] as $index => $relative) {
                    $notice->relatives()->create([
                        'name' => $relative['name'],
                        'column_position' => $relative['column_position'] ?? 1,
                        'sort_order' => $relative['sort_order'] ?? $index,
                    ]);
                }
            }

            return $notice->fresh();
        });
    }

    public function uploadDeathNoticePhoto(DeathNotice $notice, UploadedFile $file): DeathNotice
    {
        if ($notice->photo_path) {
            Storage::disk('public')->delete($notice->photo_path);
        }

        $path = $file->store('death-notice-photos/'.$notice->user_id, 'public');
        $notice->update(['photo_path' => $path]);

        return $notice->fresh();
    }

    public function getOrCreateFuneralForUser(int $userId): Funeral
    {
        $funeral = Funeral::query()->firstOrCreate(['user_id' => $userId]);

        return $funeral->load(['ceremonyType', 'arrangementType', 'funeralServiceType', 'music']);
    }

    public function updateFuneral(Funeral $funeral, array $data): Funeral
    {
        return DB::transaction(function () use ($funeral, $data) {
            $funeral->update(collect($data)->except('music')->all());

            if (array_key_exists('music', $data)) {
                $funeral->music()->delete();

                foreach ($data['music'] ?? [] as $index => $music) {
                    $funeral->music()->create([
                        'description' => $music['description'],
                        'sort_order' => $music['sort_order'] ?? $index,
                    ]);
                }
            }

            return $funeral->fresh(['ceremonyType', 'arrangementType', 'funeralServiceType', 'music']);
        });
    }

    public function getOrCreateFarewellDrinkForUser(int $userId): FarewellDrink
    {
        return FarewellDrink::query()->firstOrCreate(['user_id' => $userId]);
    }

    public function updateFarewellDrink(FarewellDrink $farewellDrink, array $data): FarewellDrink
    {
        $farewellDrink->update($data);

        return $farewellDrink->fresh();
    }

    public function getOrCreateLifetimeAchievementForUser(int $userId): LifetimeAchievement
    {
        $achievement = LifetimeAchievement::query()->firstOrCreate(['user_id' => $userId]);

        return $achievement->load(['educations', 'spouses', 'children', 'grandchildren']);
    }

    public function updateLifetimeAchievement(LifetimeAchievement $achievement, array $data): LifetimeAchievement
    {
        return DB::transaction(function () use ($achievement, $data) {
            $achievement->update(
                collect($data)->except(['educations', 'spouses', 'children', 'grandchildren', 'resume'])->all()
            );

            if (array_key_exists('educations', $data)) {
                $achievement->educations()->delete();

                foreach ($data['educations'] ?? [] as $index => $education) {
                    $achievement->educations()->create([
                        'school_or_university' => $education['school_or_university'] ?? null,
                        'degree' => $education['degree'] ?? null,
                        'year' => $education['year'] ?? null,
                        'sort_order' => $education['sort_order'] ?? $index,
                    ]);
                }
            }

            if (array_key_exists('spouses', $data)) {
                $achievement->spouses()->delete();

                foreach ($data['spouses'] ?? [] as $index => $spouse) {
                    $achievement->spouses()->create([
                        'name' => $spouse['name'],
                        'sort_order' => $spouse['sort_order'] ?? $index,
                    ]);
                }
            }

            if (array_key_exists('children', $data)) {
                $achievement->children()->delete();

                foreach ($data['children'] ?? [] as $index => $child) {
                    $achievement->children()->create([
                        'name' => $child['name'],
                        'date_of_birth' => $child['date_of_birth'] ?? null,
                        'spouse_name' => $child['spouse_name'] ?? null,
                        'sort_order' => $child['sort_order'] ?? $index,
                    ]);
                }
            }

            if (array_key_exists('grandchildren', $data)) {
                $achievement->grandchildren()->delete();

                foreach ($data['grandchildren'] ?? [] as $index => $grandchild) {
                    $achievement->grandchildren()->create([
                        'name' => $grandchild['name'],
                        'date_of_birth' => $grandchild['date_of_birth'] ?? null,
                        'parent_name' => $grandchild['parent_name'] ?? null,
                        'sort_order' => $grandchild['sort_order'] ?? $index,
                    ]);
                }
            }

            return $achievement->fresh(['educations', 'spouses', 'children', 'grandchildren']);
        });
    }

    public function uploadLifetimeAchievementResume(LifetimeAchievement $achievement, UploadedFile $file): LifetimeAchievement
    {
        if ($achievement->resume_path) {
            Storage::disk('public')->delete($achievement->resume_path);
        }

        $path = $file->store('lifetime-achievements/'.$achievement->user_id.'/resume', 'public');
        $achievement->update(['resume_path' => $path]);

        return $achievement->fresh(['educations', 'spouses', 'children', 'grandchildren']);
    }

    public function listMemoriesForUser(int $userId)
    {
        return Memory::query()
            ->with(['photos', 'documents'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function createMemory(int $userId, array $data, array $pictures = [], array $documents = []): Memory
    {
        return DB::transaction(function () use ($userId, $data, $pictures, $documents) {
            $memory = Memory::query()->create([
                'user_id' => $userId,
                ...collect($data)->only(['name', 'date_of_memory', 'description', 'location_notes'])->all(),
            ]);

            $this->attachPhotos($memory, $pictures);
            $this->attachDocuments($memory, $documents);

            return $memory->fresh(['photos', 'documents']);
        });
    }

    public function updateMemory(
        Memory $memory,
        array $data,
        array $newPictures = [],
        array $newDocuments = [],
        array $removePictureIds = [],
        array $removeDocumentIds = []
    ): Memory {
        return DB::transaction(function () use ($memory, $data, $newPictures, $newDocuments, $removePictureIds, $removeDocumentIds) {
            $memory->update(collect($data)->only(['name', 'date_of_memory', 'description', 'location_notes'])->all());

            if (! empty($removePictureIds)) {
                $this->removePhotos($memory, $removePictureIds);
            }

            if (! empty($removeDocumentIds)) {
                $this->removeDocuments($memory, $removeDocumentIds);
            }

            $this->attachPhotos($memory, $newPictures);
            $this->attachDocuments($memory, $newDocuments);

            return $memory->fresh(['photos', 'documents']);
        });
    }

    public function deleteMemory(Memory $memory): bool
    {
        foreach ($memory->photos as $photo) {
            Storage::disk('public')->delete($photo->file_path);
        }

        foreach ($memory->documents as $document) {
            Storage::disk('public')->delete($document->file_path);
        }

        return (bool) $memory->delete();
    }

    /**
     * @param  UploadedFile[]  $files
     */
    private function attachPhotos(Memory $memory, array $files): void
    {
        $nextOrder = (int) $memory->photos()->max('sort_order') + 1;

        foreach (array_values($files) as $index => $file) {
            $path = $file->store('memories/'.$memory->user_id.'/photos', 'public');

            $memory->photos()->create([
                'file_path' => $path,
                'sort_order' => $nextOrder + $index,
            ]);
        }
    }

    /**
     * @param  UploadedFile[]  $files
     */
    private function attachDocuments(Memory $memory, array $files): void
    {
        $nextOrder = (int) $memory->documents()->max('sort_order') + 1;

        foreach (array_values($files) as $index => $file) {
            $path = $file->store('memories/'.$memory->user_id.'/documents', 'public');

            $memory->documents()->create([
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'sort_order' => $nextOrder + $index,
            ]);
        }
    }

    private function removePhotos(Memory $memory, array $ids): void
    {
        $photos = $memory->photos()->whereIn('id', $ids)->get();

        foreach ($photos as $photo) {
            Storage::disk('public')->delete($photo->file_path);
            $photo->delete();
        }
    }

    private function removeDocuments(Memory $memory, array $ids): void
    {
        $documents = $memory->documents()->whereIn('id', $ids)->get();

        foreach ($documents as $document) {
            Storage::disk('public')->delete($document->file_path);
            $document->delete();
        }
    }
}
