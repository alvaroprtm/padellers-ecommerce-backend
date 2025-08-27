<?php

use App\Jobs\SendOrderCreatedEmailJob;
use App\Mail\OrderCreatedMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

describe('Order Email System', function () {

    test('email job is dispatched when order is created', function () {
        Queue::fake();

        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'price' => 99.99,
        ]);

        Queue::assertPushed(SendOrderCreatedEmailJob::class, function ($job) use ($order) {
            return $job->order->id === $order->id;
        });
    });

    test('order created email can be sent', function () {
        Mail::fake();

        $user = User::factory()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $order->orderItems()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => $product->price,
        ]);

        Mail::to($user->email)->send(new OrderCreatedMail($order));

        Mail::assertSent(OrderCreatedMail::class, function ($mail) use ($order) {
            return $mail->order->id === $order->id;
        });
    });
});
