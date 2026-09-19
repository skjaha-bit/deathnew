<?php

namespace Modules\AfterMyDay\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AfterMyDay\Http\Requests\StoreMemoryRequest;
use Modules\AfterMyDay\Http\Requests\UpdateDeathNoticeRequest;
use Modules\AfterMyDay\Http\Requests\UpdateFarewellDrinkRequest;
use Modules\AfterMyDay\Http\Requests\UpdateFuneralRequest;
use Modules\AfterMyDay\Http\Requests\UpdateLifetimeAchievementRequest;
use Modules\AfterMyDay\Http\Requests\UpdateMemoryRequest;
use Modules\AfterMyDay\Http\Requests\UploadDeathNoticePhotoRequest;
use Modules\AfterMyDay\Http\Requests\UploadLifetimeAchievementResumeRequest;
use Modules\AfterMyDay\Http\Services\AfterMyDayService;
use Modules\AfterMyDay\Models\ArrangementType;
use Modules\AfterMyDay\Models\CeremonyType;
use Modules\AfterMyDay\Models\DeathNotice;
use Modules\AfterMyDay\Models\FarewellDrink;
use Modules\AfterMyDay\Models\Funeral;
use Modules\AfterMyDay\Models\FuneralMusic;
use Modules\AfterMyDay\Models\FuneralServiceType;
use Modules\AfterMyDay\Models\LifetimeAchievement;
use Modules\AfterMyDay\Models\LifetimeAchievementChild;
use Modules\AfterMyDay\Models\LifetimeAchievementEducation;
use Modules\AfterMyDay\Models\LifetimeAchievementGrandchild;
use Modules\AfterMyDay\Models\LifetimeAchievementSpouse;
use Modules\AfterMyDay\Models\Memory;
use Modules\AfterMyDay\Models\MemoryDocument;
use Modules\AfterMyDay\Models\MemoryPhoto;
use Modules\Core\Traits\ApiResponse;

class AfterMyDayController extends Controller
{
    use ApiResponse;

    public function __construct(protected AfterMyDayService $afterMyDayService) {}

    public function show(Request $request)
    {
        $notice = $this->afterMyDayService->getOrCreateDeathNoticeForUser($request->user()->id);

        return $this->success($this->formatDeathNotice($notice));
    }

    public function update(UpdateDeathNoticeRequest $request)
    {
        $notice = $this->afterMyDayService->getOrCreateDeathNoticeForUser($request->user()->id);
        $notice = $this->afterMyDayService->updateDeathNotice($notice, $request->validated());

        return $this->success($this->formatDeathNotice($notice), 'Death notice updated.');
    }

    public function uploadPhoto(UploadDeathNoticePhotoRequest $request)
    {
        $notice = $this->afterMyDayService->getOrCreateDeathNoticeForUser($request->user()->id);
        $notice = $this->afterMyDayService->uploadDeathNoticePhoto($notice, $request->file('photo'));

        return $this->success($this->formatDeathNotice($notice), 'Photo updated.');
    }

    public function ceremonyTypes()
    {
        $types = $this->afterMyDayService->ceremonyTypeOptions();

        return $this->success($types->map(fn (CeremonyType $type) => $this->formatOption($type))->values()->all());
    }

    public function arrangementTypes()
    {
        $types = $this->afterMyDayService->arrangementTypeOptions();

        return $this->success($types->map(fn (ArrangementType $type) => $this->formatOption($type))->values()->all());
    }

    public function funeralServiceTypes()
    {
        $types = $this->afterMyDayService->funeralServiceTypeOptions();

        return $this->success($types->map(fn (FuneralServiceType $type) => $this->formatOption($type))->values()->all());
    }

    public function showFuneral(Request $request)
    {
        $funeral = $this->afterMyDayService->getOrCreateFuneralForUser($request->user()->id);

        return $this->success($this->formatFuneral($funeral));
    }

    public function updateFuneral(UpdateFuneralRequest $request)
    {
        $funeral = $this->afterMyDayService->getOrCreateFuneralForUser($request->user()->id);
        $funeral = $this->afterMyDayService->updateFuneral($funeral, $request->validated());

        return $this->success($this->formatFuneral($funeral), 'Funeral wishes updated.');
    }

    public function showFarewellDrink(Request $request)
    {
        $farewellDrink = $this->afterMyDayService->getOrCreateFarewellDrinkForUser($request->user()->id);

        return $this->success($this->formatFarewellDrink($farewellDrink));
    }

    public function updateFarewellDrink(UpdateFarewellDrinkRequest $request)
    {
        $farewellDrink = $this->afterMyDayService->getOrCreateFarewellDrinkForUser($request->user()->id);
        $farewellDrink = $this->afterMyDayService->updateFarewellDrink($farewellDrink, $request->validated());

        return $this->success($this->formatFarewellDrink($farewellDrink), 'Farewell drink updated.');
    }

    public function showLifetimeAchievement(Request $request)
    {
        $achievement = $this->afterMyDayService->getOrCreateLifetimeAchievementForUser($request->user()->id);

        return $this->success($this->formatLifetimeAchievement($achievement));
    }

    public function updateLifetimeAchievement(UpdateLifetimeAchievementRequest $request)
    {
        $achievement = $this->afterMyDayService->getOrCreateLifetimeAchievementForUser($request->user()->id);
        $achievement = $this->afterMyDayService->updateLifetimeAchievement($achievement, $request->validated());

        if ($request->hasFile('resume')) {
            $achievement = $this->afterMyDayService->uploadLifetimeAchievementResume($achievement, $request->file('resume'));
        }

        return $this->success($this->formatLifetimeAchievement($achievement), 'Lifetime achievement updated.');
    }

    public function uploadLifetimeAchievementResume(UploadLifetimeAchievementResumeRequest $request)
    {
        $achievement = $this->afterMyDayService->getOrCreateLifetimeAchievementForUser($request->user()->id);
        $achievement = $this->afterMyDayService->uploadLifetimeAchievementResume($achievement, $request->file('resume'));

        return $this->success($this->formatLifetimeAchievement($achievement), 'Resume updated.');
    }

    public function memoriesIndex(Request $request)
    {
        $memories = $this->afterMyDayService->listMemoriesForUser($request->user()->id);

        return $this->success($memories->map(fn (Memory $memory) => $this->formatMemory($memory))->values()->all());
    }

    public function storeMemory(StoreMemoryRequest $request)
    {
        $memory = $this->afterMyDayService->createMemory(
            $request->user()->id,
            $request->validated(),
            $request->file('pictures', []),
            $request->file('documents', [])
        );

        return $this->success($this->formatMemory($memory), 'Memory added.', 201);
    }

    public function showMemory(Request $request, Memory $memory)
    {
        abort_if($memory->user_id !== $request->user()->id, 403, 'You do not have access to this memory.');

        $memory->load(['photos', 'documents']);

        return $this->success($this->formatMemory($memory));
    }

    public function updateMemory(UpdateMemoryRequest $request)
    {
        $data = $request->validated();

        $memory = Memory::query()->findOrFail($data['id']);

        abort_if($memory->user_id !== $request->user()->id, 403, 'You do not have access to this memory.');

        $memory = $this->afterMyDayService->updateMemory(
            $memory,
            $data,
            $request->file('new_pictures', []),
            $request->file('new_documents', []),
            $data['remove_picture_ids'] ?? [],
            $data['remove_document_ids'] ?? []
        );

        return $this->success($this->formatMemory($memory), 'Memory updated.');
    }

    public function destroyMemory(Request $request)
    {
        $request->validate(['id' => ['required', 'integer', 'exists:memories,id']]);

        $memory = Memory::query()->findOrFail($request->input('id'));

        abort_if($memory->user_id !== $request->user()->id, 403, 'You do not have access to this memory.');

        $this->afterMyDayService->deleteMemory($memory);

        return $this->success(null, 'Memory removed.');
    }

    private function formatMemory(Memory $memory): array
    {
        return [
            'id' => $memory->id,
            'user_id' => $memory->user_id,
            'name' => $memory->name,
            'date_of_memory' => $memory->date_of_memory?->toDateString(),
            'description' => $memory->description,
            'location_notes' => $memory->location_notes,
            'pictures' => $memory->photos->map(fn (MemoryPhoto $photo) => [
                'id' => $photo->id,
                'url' => asset('storage/'.$photo->file_path),
                'sort_order' => $photo->sort_order,
            ])->values(),
            'documents' => $memory->documents->map(fn (MemoryDocument $document) => [
                'id' => $document->id,
                'name' => $document->original_name,
                'url' => asset('storage/'.$document->file_path),
                'size' => $document->file_size,
                'sort_order' => $document->sort_order,
            ])->values(),
            'created_at' => $memory->created_at?->toIso8601String(),
            'updated_at' => $memory->updated_at?->toIso8601String(),
        ];
    }

    private function formatDeathNotice(DeathNotice $notice): array
    {
        return [
            'id' => $notice->id,
            'user_id' => $notice->user_id,
            'preference' => $notice->preference,
            'obituary_media_preference' => $notice->obituary_media_preference,
            'symbol' => $notice->symbol,
            'photo_path' => $notice->photo_path,
            'photo_url' => $notice->photo_path ? asset('storage/'.$notice->photo_path) : null,
            'introduction' => $notice->introduction,
            'job_title' => $notice->job_title,
            'home_address' => $notice->home_address,
            'date_of_birth' => $notice->date_of_birth?->toDateString(),
            'body_text' => $notice->body_text,
            'alignment' => $notice->alignment,
            'layout' => $notice->layout,
            'created_at' => $notice->created_at?->toIso8601String(),
            'updated_at' => $notice->updated_at?->toIso8601String(),
        ];
    }

    private function formatFuneral(Funeral $funeral): array
    {
        return [
            'id' => $funeral->id,
            'user_id' => $funeral->user_id,
            'ceremony_type_id' => $funeral->ceremony_type_id,
            'ceremony_type_name' => $funeral->ceremonyType?->name,
            'arrangement_type_id' => $funeral->arrangement_type_id,
            'arrangement_type_name' => $funeral->arrangementType?->name,
            'other_ceremony_options' => $funeral->other_ceremony_options,
            'funeral_service_type_id' => $funeral->funeral_service_type_id,
            'funeral_service_type_name' => $funeral->funeralServiceType?->name,
            'other_thing' => $funeral->other_thing,
            'location' => $funeral->location,
            'laying_place' => $funeral->laying_place,
            'clothing_request' => $funeral->clothing_request,
            'event_manager' => $funeral->event_manager,
            'guests' => $funeral->guests,
            'pallbearer_request' => $funeral->pallbearer_request,
            'coffin_design' => $funeral->coffin_design,
            'coffin_decorations' => $funeral->coffin_decorations,
            'coffin_decorations_detail' => $funeral->coffin_decorations_detail,
            'music' => $funeral->music->map(fn (FuneralMusic $music) => [
                'id' => $music->id,
                'description' => $music->description,
                'sort_order' => $music->sort_order,
            ])->values(),
            'charity_case' => $funeral->charity_case,
            'another_thing' => $funeral->another_thing,
            'notes' => $funeral->notes,
            'created_at' => $funeral->created_at?->toIso8601String(),
            'updated_at' => $funeral->updated_at?->toIso8601String(),
        ];
    }

    private function formatFarewellDrink(FarewellDrink $farewellDrink): array
    {
        return [
            'id' => $farewellDrink->id,
            'user_id' => $farewellDrink->user_id,
            'preference' => $farewellDrink->preference,
            'location' => $farewellDrink->location,
            'guests' => $farewellDrink->guests,
            'catering' => $farewellDrink->catering,
            'arrangement' => $farewellDrink->arrangement,
            'comments' => $farewellDrink->comments,
            'other_wishes' => $farewellDrink->other_wishes,
            'created_at' => $farewellDrink->created_at?->toIso8601String(),
            'updated_at' => $farewellDrink->updated_at?->toIso8601String(),
        ];
    }

    

    private function formatLifetimeAchievement(LifetimeAchievement $achievement): array
    {
        return [
            'id' => $achievement->id,
            'user_id' => $achievement->user_id,
            'full_name' => $achievement->full_name,
            'date_of_birth' => $achievement->date_of_birth?->toDateString(),
            'place_of_birth' => $achievement->place_of_birth,
            'resume_path' => $achievement->resume_path,
            'resume_url' => $achievement->resume_path ? asset('storage/'.$achievement->resume_path) : null,
            'educations' => $achievement->educations->map(fn (LifetimeAchievementEducation $education) => [
                'id' => $education->id,
                'school_or_university' => $education->school_or_university,
                'degree' => $education->degree,
                'year' => $education->year,
                'sort_order' => $education->sort_order,
            ])->values(),
            'spouses' => $achievement->spouses->map(fn (LifetimeAchievementSpouse $spouse) => [
                'id' => $spouse->id,
                'name' => $spouse->name,
                'sort_order' => $spouse->sort_order,
            ])->values(),
            'children' => $achievement->children->map(fn (LifetimeAchievementChild $child) => [
                'id' => $child->id,
                'name' => $child->name,
                'date_of_birth' => $child->date_of_birth?->toDateString(),
                'spouse_name' => $child->spouse_name,
                'sort_order' => $child->sort_order,
            ])->values(),
            'grandchildren' => $achievement->grandchildren->map(fn (LifetimeAchievementGrandchild $grandchild) => [
                'id' => $grandchild->id,
                'name' => $grandchild->name,
                'date_of_birth' => $grandchild->date_of_birth?->toDateString(),
                'parent_name' => $grandchild->parent_name,
                'sort_order' => $grandchild->sort_order,
            ])->values(),
            'personal_information' => $achievement->personal_information,
            'another_thing' => $achievement->another_thing,
            'created_at' => $achievement->created_at?->toIso8601String(),
            'updated_at' => $achievement->updated_at?->toIso8601String(),
        ];
    }

    private function formatOption(CeremonyType|ArrangementType|FuneralServiceType $option): array
    {
        return [
            'id' => $option->id,
            'name' => $option->name,
        ];
    }
}
