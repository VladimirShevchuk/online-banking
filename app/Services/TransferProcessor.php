<?php

namespace App\Services;

use App\Exceptions\TransferValidationException;
use App\Models\Account;
use App\Models\Transfer;
use App\Models\User;
use App\Repositories\AccountRepository;
use App\Repositories\TransferRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class TransferProcessor
{
    public function __construct(
        protected TransferRepository $transferRepository,
        protected AccountRepository $accountRepository
    ) {
    }

    public function process(Transfer $transfer): void
    {
        DB::beginTransaction();

        /**
         * Lock affected accounts to prevent their "balance" field read and modification by concurrent processes.
         *
         * Calling lockForUpdate method on the existing model doesn't execute the query on the database.
         * That's why we need to fetch user models again to execute "SELECT FOR UPDATE" query for lock acquiring.
         * Locks will be released by the database after transaction's commit or rollback.
         */
        $senderAccount = $this->accountRepository->findWithLockOrFail($transfer->sender->account->getKey());
        $recipientAccount = $this->accountRepository->findWithLockOrFail($transfer->recipient->account->getKey());
        $transfer = $this->transferRepository->findWithLockOrFail($transfer->getKey());

        try {
            $this->validateTransferWasNotProcessedYet($transfer);
        } catch (TransferValidationException $exc) {
            // Do nothing, just skip this transfer
            DB::commit();
            return;
        }

        try {
            $this->validateSenderHasEnoughMoney($senderAccount, $transfer);
            $this->validateSenderAndRecipientAreDifferentUsers($transfer->sender, $transfer->recipient);
        } catch (TransferValidationException $exc) {
            $transfer->decline($exc->getMessage())->save();
            DB::commit();
            return;
        }

        /** Start of the nested transaction with actual money transfer */
        DB::beginTransaction();
        try {
            $moneyTransferError = null;
            $senderAccount->withdrawMoney($transfer->amount)->save();
            $recipientAccount->depositMoney($transfer->amount)->save();
            DB::commit();
        } catch (Exception $exc) {
            $moneyTransferError = $exc->getMessage();
            DB::rollBack();
        }
        /** End of the nested transaction with actual money transfer */

        /**
         * In case of money transfer error, nested transaction changes to the balance are rolled back.
         * Here we change the status of the transfer and commit the main transaction to save it.
         */
        try {
            if ($moneyTransferError !== null) {
                $transfer->error($moneyTransferError)->save();
            } else {
                $transfer->approve()->save();
            }
            DB::commit();
        } catch (Exception $exc) {
            DB::rollBack();
            throw $exc;
        }
    }

    /**
     * @param Transfer $transfer
     * @return void
     * @throws TransferValidationException
     */
    protected function validateTransferWasNotProcessedYet(Transfer $transfer): void
    {
        if ($transfer->isProcessed()) {
            throw new TransferValidationException("This transfer was already processed.");
        }
    }

    /**
     * @param Account $senderAccount
     * @param Transfer $transfer
     * @return void
     * @throws TransferValidationException
     */
    protected function validateSenderHasEnoughMoney(Account $senderAccount, Transfer $transfer): void
    {
        if ($senderAccount->hasLessMoneyThan($transfer->amount)) {
            throw new TransferValidationException("Not enough money on the sender's balance.");
        }
    }

    /**
     * @param User $sender
     * @param User $recipient
     * @return void
     * @throws TransferValidationException
     */
    protected function validateSenderAndRecipientAreDifferentUsers(User $sender, User $recipient): void
    {
        if ($sender->getKey() === $recipient->getKey()) {
            throw new TransferValidationException("Recipient account can't be the same as sender.");
        }
    }
}
