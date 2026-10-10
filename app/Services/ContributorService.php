<?php

namespace App\Services;

use App\Models\Concerns\HasContributors;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

/**
 * The only writer of `contributors` (PRD §6.1): delete + rewrite in the
 * caller's transaction, sort_order 1..n in the submitted order.
 */
class ContributorService
{
    /**
     * @param  Model&HasContributors  $record
     * @param  array<int, array{user_id?: mixed, name?: mixed, affiliation?: mixed}>  $rows
     *
     * @throws ValidationException
     */
    public function sync(Model $record, array $rows, User $actor): void
    {
        $rows = $this->normalize($rows);

        $userIds = array_values(array_filter(array_column($rows, 'user_id')));

        if (count($userIds) !== count(array_unique($userIds))) {
            throw ValidationException::withMessages(['contributors' => 'Dosen yang sama tidak boleh dicantumkan dua kali.']);
        }

        // A Dosen is always on their own records: added automatically on create,
        // and may not remove themself afterwards (they would lose access).
        if ($actor->isDosen() && ! in_array($actor->id, $userIds, true)) {
            if (! $record->wasRecentlyCreated) {
                throw ValidationException::withMessages(['contributors' => 'Anda tidak dapat menghapus diri sendiri dari daftar anggota.']);
            }
            $rows[] = ['user_id' => $actor->id, 'name' => null, 'affiliation' => null];
        }

        if ($rows === []) {
            throw ValidationException::withMessages(['contributors' => 'Minimal satu anggota wajib diisi.']);
        }

        $record->contributors()->delete();
        $record->contributors()->createMany(array_map(
            fn (array $row, int $index) => $row + ['sort_order' => $index + 1],
            $rows,
            array_keys($rows),
        ));
        $record->unsetRelation('contributors');
    }

    /**
     * Drop empty rows; internal rows keep only user_id, external rows only name + affiliation.
     *
     * @return list<array{user_id: ?int, name: ?string, affiliation: ?string}>
     */
    private function normalize(array $rows): array
    {
        $normalized = [];

        foreach ($rows as $row) {
            $userId = filled($row['user_id'] ?? null) ? (int) $row['user_id'] : null;
            $name = trim((string) ($row['name'] ?? ''));

            if ($userId) {
                $normalized[] = ['user_id' => $userId, 'name' => null, 'affiliation' => null];
            } elseif ($name !== '') {
                $affiliation = trim((string) ($row['affiliation'] ?? ''));
                $normalized[] = ['user_id' => null, 'name' => $name, 'affiliation' => $affiliation ?: null];
            }
        }

        return $normalized;
    }
}
