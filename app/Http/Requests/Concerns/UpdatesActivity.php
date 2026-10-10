<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Update{Model}Request of a research-data module: authorise through the Policy,
 * count saved documents toward the limit, and keep current members valid.
 */
trait UpdatesActivity
{
    /**
     * The record bound to the route.
     */
    abstract protected function record(): Model;

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->record());
    }

    protected function existingDocumentCount(): int
    {
        return $this->record()->documents()->count();
    }

    protected function currentContributorIds(): array
    {
        return $this->record()->internalContributorIds();
    }
}
