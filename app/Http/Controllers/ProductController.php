<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
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
     * Store a new product (for authenticated users)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|url'
        ]);

        $product = auth()->user()->products()->create($validated);
        return response()->json($product->load('user:id,name'), 201);
    }

    /**
     * Update product (only owner can update)
     */
    public function update(Request $request, Product $product)
    {
        // Check if user owns this product
        if ($product->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|url'
        ]);

        $product->update($validated);
        return response()->json($product->load('user:id,name'));
    }

    /**
     * Delete product (only owner can delete)
     */
    public function destroy(Product $product)
    {
        // Check if user owns this product
        if ($product->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    /**
     * Get products for authenticated user
     */
    public function supplierProducts()
    {
        $products = auth()->user()->products()->latest()->get();
        return response()->json($products);
    }
}