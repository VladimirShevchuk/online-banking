<?php

namespace App\Repositories;

use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class SubscriptionRepository
{
    public function findWithLockOrFail(int $id): Subscription
    {
        return Subscription::lockForUpdate()->findOrFail($id);
    }

    public function getSubscriptionsDueForInvoice()
    {
        return Subscription::whereDate('next_invoice_date', '<=', DB::raw('NOW()'))->get();
    }

    public function getUserSubscriptionsPaginated(int $userId, int $perPage = 10)
    {
        return Subscription::where('user_id', '=', $userId)->paginate($perPage);
    }
}
