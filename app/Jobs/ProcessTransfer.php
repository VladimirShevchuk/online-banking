<?php

namespace App\Jobs;

use App\Models\Transfer;
use App\Services\TransferProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTransfer implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Transfer $transfer)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TransferProcessor $transferProcessor): void
    {
        try {
            $transferProcessor->process($this->transfer);
        } catch (\Exception $exc) {
            /**
             * Here we can check different kinds of Exceptions and fail if it's query error and not fail if connection error,
             * to let Laravel put the job back in the queue.
             */
            $this->fail($exc);
        }
    }
}
