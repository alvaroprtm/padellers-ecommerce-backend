<?php

namespace App\Jobs;

use App\Mail\OrderCreatedMail;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\InteractsWithUniqueJobs;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderCreatedEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithUniqueJobs, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->order->user->email)
                ->send(new OrderCreatedMail($this->order));

            Log::info('Order created email sent successfully', [
                'order_id' => $this->order->id,
                'user_email' => $this->order->user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send order created email', [
                'order_id' => $this->order->id,
                'user_email' => $this->order->user->email,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Order created email job failed permanently', [
            'order_id' => $this->order->id,
            'user_email' => $this->order->user->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
