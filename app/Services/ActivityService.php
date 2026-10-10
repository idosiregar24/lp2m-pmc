<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

/**
 * Save flow shared by the five research-data modules: record + contributors +
 * documents in one transaction, then the verification step (PRD §4.9, §4.11).
 * A module service only names its model.
 *
 * @template TModel of Model
 */
abstract class ActivityService
{
    public function __construct(
        protected readonly ContributorService $contributors,
        protected readonly DocumentService $documents,
        protected readonly VerificationService $verification,
    ) {}

    /**
     * @return class-string<TModel>
     */
    abstract protected function model(): string;

    /**
     * @param  array<string, mixed>  $data  validated input
     * @param  array<UploadedFile>  $files
     * @return TModel
     */
    public function create(array $data, array $files, User $actor, bool $submit = false): Model
    {
        return $this->documents->transaction(function () use ($data, $files, $actor, $submit) {
            $model = $this->model();
            $record = new $model(Arr::except($data, ['contributors', 'documents']));
            $record->created_by = $actor->id;
            $record->save();

            $this->contributors->sync($record, $data['contributors'], $actor);
            $this->documents->storeMany($record, $files, $actor);
            $this->afterSave($record, $actor, $submit);

            return $record;
        });
    }

    /**
     * @param  TModel  $record
     * @param  array<string, mixed>  $data  validated input
     * @param  array<UploadedFile>  $files
     * @return TModel
     */
    public function update(Model $record, array $data, array $files, User $actor, bool $submit = false): Model
    {
        return $this->documents->transaction(function () use ($record, $data, $files, $actor, $submit) {
            if (! $actor->isAdmin()) {
                $this->verification->ensureEditableByOwner($record);
            }

            $record->fill(Arr::except($data, ['contributors', 'documents']));
            $record->updated_by = $actor->id;
            $record->save();

            $this->contributors->sync($record, $data['contributors'], $actor);
            $this->documents->storeMany($record, $files, $actor);
            $this->afterSave($record, $actor, $submit);

            return $record;
        });
    }

    /**
     * Soft delete; contributors and documents stay for a possible restore.
     *
     * @param  TModel  $record
     */
    public function delete(Model $record): void
    {
        $record->delete();
    }

    private function afterSave(Model $record, User $actor, bool $submit): void
    {
        if ($actor->isAdmin()) {
            $this->verification->markVerifiedByAdmin($record, $actor);
        } elseif ($submit) {
            $this->verification->submit($record, $actor);
        }
    }
}
