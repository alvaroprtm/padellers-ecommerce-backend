<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of products
     */
    public function index()
    {
        $products = Product::with('user:id,name')->latest()->get();

        return response()->json($products);
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        return response()->json($product->load('user:id,name'));
    }

    /**
     * Store a new product (for authenticated users with permission)
     */
    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|url',
        ]);

        $product = auth()->user()->products()->create($validated);

        return response()->json($product->load('user:id,name'), 201);
    }

    /**
     * Update product (check ownership OR edit permission)
     */
    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|url',
        ]);

        $product->update($validated);

        return response()->json($product->load('user:id,name'));
    }

    /**
     * Delete product (check ownership OR delete permission)
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    /**
     * Get products for authenticated user
     */
    public function supplierProducts()
    {
        $this->authorize('viewOwned', Product::class);

        $products = auth()->user()->products()->latest()->get();

        return response()->json($products);
    }
}
