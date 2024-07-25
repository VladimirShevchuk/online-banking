<?php

namespace App\Repositories;

use App\Models\Transfer;

class TransferRepository
{
    public function getTransfersSentByUserPaginated(int $userId, int $itemsPerPage)
    {
        return Transfer::where('sender_user_id', $userId)
            ->paginate($itemsPerPage);
    }

    /**
     * @param int $userId
     * @param array $statuses
     * @param int $itemsPerPage
     * @return mixed
     */
    public function getTransfersSentAndReceivedByUserPaginated(int $userId, array $statuses = [], int $itemsPerPage = 10)
    {
        $query = Transfer::where(function ($q) use ($userId) {
            $q->where('sender_user_id', '=', $userId)
                ->orWhere('recipient_user_id', '=', $userId);
        });

        if (!empty($statuses)) {
            $query->whereIn('status', $statuses);
        }

        return $query->paginate($itemsPerPage);
    }

    public function find(int $id): ?Transfer
    {
        return Transfer::find($id);
    }

    public function findWithLockOrFail(int $id): Transfer
    {
        return Transfer::lockForUpdate()->findOrFail($id);
    }

    public function findByBatchId(string $id)
    {
        return Transfer::where('batch_id', '=', $id)->get();
    }
}
