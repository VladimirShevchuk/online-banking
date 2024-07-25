<?php

namespace App\Repositories;

use Spatie\Activitylog\Models\Activity;

class ActivityRepository
{
    public function getAccountBalanceChangesPaginated(int $accountId, int $itemsPerPage = 10)
    {
        return Activity::where('subject_type', '=', 'App\Models\Account')
            ->where('subject_id', '=', $accountId)
            ->whereNotNull('properties->attributes->balance')
            ->paginate($itemsPerPage);
    }
}
