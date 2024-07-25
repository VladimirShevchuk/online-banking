<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository
{
    public function find(int $id): ?Account
    {
        return Account::find($id);
    }

    public function findWithLockOrFail(int $id): Account
    {
        return Account::lockForUpdate()->findOrFail($id);
    }
}
