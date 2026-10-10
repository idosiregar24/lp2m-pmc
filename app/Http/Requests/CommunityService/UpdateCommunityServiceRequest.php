<?php

namespace App\Http\Requests\CommunityService;

use App\Enums\SchemeType;
use App\Http\Requests\Concerns\UpdatesActivity;
use App\Models\CommunityService;

class UpdateCommunityServiceRequest extends StoreCommunityServiceRequest
{
    use UpdatesActivity;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $communityService = $this->record();

        return [
            ...parent::rules(),
            // The record's current masters stay valid even if since deactivated.
            'scheme_id' => ['required', 'integer', $this->schemeRule(SchemeType::CommunityService, $communityService->scheme_id)],
            'funding_source_id' => ['required', 'integer', $this->fundingSourceRule($communityService->funding_source_id)],
        ];
    }

    protected function record(): CommunityService
    {
        return $this->route('communityService');
    }
}
