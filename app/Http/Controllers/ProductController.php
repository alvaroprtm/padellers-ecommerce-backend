<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of products
     */
    public function index(): JsonResponse
    {
        $products = Product::getAllWithSupplier();

        return response()->json($products);
    }

    /**
     * Display the specified product
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load('user:id,name'));
    }

    /**
     * Store a new product (for authenticated users with permission)
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::createForUser(auth()->user(), $request->validated());

        return response()->json($product->load('user:id,name'), 201);
    }

    /**
     * Update product (check ownership OR edit permission)
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json($product->load('user:id,name'));
    }

    /**
     * Delete product (check ownership OR delete permission)
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    /**
     * Get products for authenticated user
     */
    public function supplierProducts(): JsonResponse
    {
        $this->authorize('viewOwned', Product::class);

        $products = Product::getForSupplier(auth()->user());

        return response()->json($products);
    }
}
