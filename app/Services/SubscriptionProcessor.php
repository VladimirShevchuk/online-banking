<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Subscription;
use App\Repositories\AccountRepository;
use App\Repositories\SubscriptionRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class SubscriptionProcessor
{
    public function __construct(
        protected SubscriptionRepository $subscriptionRepository,
        protected AccountRepository $accountRepository
    ) {
    }
    public function chargeSubscriptionFee(Subscription $subscription): void
    {
        /**
         * Safety check for idempotency
         */
        if (!$subscription->isDueToInvoice()) {
            return;
        }

        DB::beginTransaction();

        try {
            /**
             * @var Account $accountLocked
             * @var Subscription $subscriptionLocked
             */
            $accountLocked = $this->accountRepository->findWithLockOrFail($subscription->user->account->getKey());
            $subscriptionLocked = $this->subscriptionRepository->findWithLockOrFail($subscription->getKey());

            /**
             * Here we only withdraw money from user for demonstration and simplicity.
             * In real implementation money would also be deposited to some "system" account.
             */
            $accountLocked->withdrawMoney($subscription->price);
            $accountLocked->save();
            $subscriptionLocked->actualizeNextInvoiceDate();
            $subscriptionLocked->activate();
            $subscriptionLocked->save();

            DB::commit();
        } catch (Exception $exc) {
            DB::rollBack();
            throw $exc;
        }
    }
}
