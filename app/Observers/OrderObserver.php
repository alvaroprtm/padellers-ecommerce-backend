<?php

namespace App\Observers;

use App\Jobs\SendOrderCreatedEmailJob;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        SendOrderCreatedEmailJob::dispatch($order)
            ->delay(now()->addMinutes(1));
    }
}
