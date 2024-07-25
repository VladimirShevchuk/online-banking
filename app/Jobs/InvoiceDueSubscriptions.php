<?php

namespace App\Jobs;

use App\Repositories\SubscriptionRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InvoiceDueSubscriptions implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * This is a simple demonstrative approach.
     * In high load envs, subscriptions would be processed in batches with pagination.
     * Or fetched one-by-one with locking to allow multiple instances of this job to process subscriptions in parallel.
     *
     * Laravel's standard feature of job dispatch is used, but in prod-ready solution I'd pay more attention to delivery acknowledgement.
     */
    public function handle(SubscriptionRepository $repository): void
    {
        $subscriptions = $repository->getSubscriptionsDueForInvoice();
        foreach ($subscriptions as $subscription) {
            ChargeSubscriptionFee::dispatch($subscription);
        }
    }
}
