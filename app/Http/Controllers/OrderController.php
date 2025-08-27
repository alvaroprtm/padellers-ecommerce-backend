<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $this->authorize('create', Order::class);

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $order = Order::createFromItems(auth()->user(), $validated['items']);

            return response()->json($order, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create order: ' . $e->getMessage()
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
    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => 'required|in:pending,paid,shipped,completed,cancelled',
        ]);

        try {
            $order->updateStatus($validated['status']);

            return response()->json($order->load(['orderItems.product']));

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update order status: ' . $e->getMessage()
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
                'message' => $e->getMessage()
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to cancel order: ' . $e->getMessage()
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
                'message' => 'Failed to retrieve supplier orders: ' . $e->getMessage()
            ], 500);
        }
    }
}