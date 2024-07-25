<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\SubscriptionProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChargeSubscriptionFee implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Subscription $subscription)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SubscriptionProcessor $subscriptionProcessor): void
    {
        try {
            $subscriptionProcessor->chargeSubscriptionFee($this->subscription);
        } catch (\Exception $exc) {
            $this->fail($exc);
        }
    }
}
