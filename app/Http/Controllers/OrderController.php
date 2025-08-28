<?php

namespace App\Http\Controllers;

use App\Http\Requests\Orders\StoreOrderRequest;
use App\Http\Requests\Orders\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get all orders for authenticated user
     */
    public function index()
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::getForUser(auth()->user());

        return response()->json($orders);
    }

    /**
     * Create a new order from cart items
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $order = Order::createFromItems(auth()->user(), $request->validated()['items']);

            return response()->json($order, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create order: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get specific order
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);

        return response()->json($order->load(['orderItems.product']));
    }

    /**
     * Update order status
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        try {
            $order->updateStatus($request->validated()['status']);

            return response()->json($order->load(['orderItems.product']));

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update order status: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete/Cancel order
     */
    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);

        try {
            $order->cancel();

            return response()->json([
                'message' => 'Order cancelled successfully',
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to cancel order: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user orders (alias for index)
     */
    public function userOrders()
    {
        return $this->index();
    }

    /**
     * Get orders that contain products from the authenticated supplier
     */
    public function supplierOrders()
    {
        $this->authorize('viewAsSupplier', Order::class);

        try {
            $orders = Order::getForSupplier(auth()->user());

            return response()->json($orders);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve supplier orders: '.$e->getMessage(),
            ], 500);
        }
    }
}
