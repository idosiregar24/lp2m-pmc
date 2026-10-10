<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

/**
 * A document follows its parent record: download = may view it, delete = may update it.
 */
class DocumentPolicy
{
    public function download(User $user, Document $document): bool
    {
        // An Admin can still retrieve the files of a soft-deleted record.
        $parent = $user->isAdmin()
            ? $document->documentable()->withTrashed()->first()
            : $document->documentable;

        return $parent !== null && $user->can('view', $parent);
    }

    public function delete(User $user, Document $document): bool
    {
        $parent = $document->documentable;

        return $parent !== null && $user->can('update', $parent);
    }
}
