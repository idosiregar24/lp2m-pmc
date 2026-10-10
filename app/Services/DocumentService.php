<?php

namespace App\Services;

use App\Models\Concerns\HasDocuments;
use App\Models\Document;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * The only writer of `documents` and of files under storage/app/private/documents.
 */
class DocumentService
{
    private const DISK = 'local';

    /** @var list<string> files written during the current transaction */
    private array $written = [];

    /**
     * DB::transaction() that also removes files written inside it when it fails
     * (files are not rolled back by the database).
     *
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    public function transaction(Closure $callback): mixed
    {
        $this->written = [];

        try {
            return DB::transaction($callback);
        } catch (Throwable $e) {
            Storage::disk(self::DISK)->delete($this->written);

            throw $e;
        } finally {
            $this->written = [];
        }
    }

    /**
     * @param  Model&HasDocuments  $record
     * @param  array<UploadedFile>  $files
     */
    public function storeMany(Model $record, array $files, User $actor): void
    {
        foreach ($files as $file) {
            $directory = 'documents/'.$record->getMorphClass().'/'.$record->getKey();
            $path = $file->storeAs($directory, Str::ulid()->toBase32().'.pdf', self::DISK);

            if ($path === false) {
                throw new \RuntimeException('Dokumen gagal disimpan.');
            }

            $this->written[] = $path;

            $record->documents()->create([
                'original_name' => Str::limit(basename($file->getClientOriginalName()), 250, ''),
                'path' => $path,
                'mime_type' => $file->getMimeType() ?? 'application/pdf',
                'size' => $file->getSize(),
                'uploaded_by' => $actor->id,
            ]);
        }

        $record->unsetRelation('documents');
    }

    public function delete(Document $document): void
    {
        DB::transaction(fn () => $document->delete());

        Storage::disk(self::DISK)->delete($document->path);
    }

    /**
     * Remove every document of a record (force delete of the parent).
     */
    public function deleteAllFor(Model $record): void
    {
        $record->documents()->get()->each(fn (Document $document) => $this->delete($document));
    }
}
