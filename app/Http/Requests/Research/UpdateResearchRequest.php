<?php

namespace App\Http\Requests\Research;

use App\Enums\SchemeType;
use App\Http\Requests\Concerns\UpdatesActivity;
use App\Models\Research;

class UpdateResearchRequest extends StoreResearchRequest
{
    use UpdatesActivity;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $research = $this->record();

        return [
            ...parent::rules(),
            // The record's current masters stay valid even if since deactivated.
            'scheme_id' => ['required', 'integer', $this->schemeRule(SchemeType::Research, $research->scheme_id)],
            'funding_source_id' => ['required', 'integer', $this->fundingSourceRule($research->funding_source_id)],
        ];
    }

    protected function record(): Research
    {
        return $this->route('research');
    }
}
