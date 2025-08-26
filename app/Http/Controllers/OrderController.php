<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Get all orders for authenticated user
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['orderItems.product'])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    /**
     * Create a new order from cart items
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Create the order
            $order = auth()->user()->orders()->create([
                'status' => Order::STATUS_PENDING,
                'price' => 0, 
            ]);

            $totalPrice = 0;

            // Create order items
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);

                if (! $product) {
                    throw new \Exception('Product not found');
                }

                $orderItem = $order->orderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);

                $totalPrice += $orderItem->subtotal;
            }

            $order->update(['price' => $totalPrice]);

            DB::commit();

            return response()->json($order->load(['orderItems.product']), 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to create order: '.$e->getMessage()], 500);
        }
    }

    /**
     * Get specific order
     */
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($order->load(['orderItems.product']));
    }

    /**
     * Update order status
     */
    public function update(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,paid,shipped,completed,cancelled',
        ]);

        $order->update($validated);

        return response()->json($order->load(['orderItems.product']));
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
        $supplierId = auth()->id();

        $orders = Order::whereHas('orderItems.product', function ($query) use ($supplierId) {
            $query->where('user_id', $supplierId);
        })
            ->with([
                'user:id,name,email', // Customer info
                'orderItems' => function ($query) use ($supplierId) {
                    // Only load order items that belong to this supplier's products
                    $query->whereHas('product', function ($q) use ($supplierId) {
                        $q->where('user_id', $supplierId);
                    });
                },
                'orderItems.product',
            ])
            ->latest()
            ->get();

        return response()->json($orders);
    }
}
