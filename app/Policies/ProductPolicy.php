<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('product.view');
    }

    /**
     * Determine whether the user can view the product.
     */
    public function view(User $user, Product $product): bool
    {
        return $user->can('product.view');
    }

    /**
     * Determine whether the user can create products.
     */
    public function create(User $user): bool
    {
        return $user->can('product.create');
    }

    /**
     * Determine whether the user can update the product.
     */
    public function update(User $user, Product $product): bool
    {
        return $product->user_id === $user->id || 
               ($user->can('product.edit') && $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can delete the product.
     */
    public function delete(User $user, Product $product): bool
    {
        return $product->user_id === $user->id || 
               ($user->can('product.delete') && $user->hasRole('admin'));
    }

    /**
     * Determine whether the user can view products they supply.
     */
    public function viewOwned(User $user): bool
    {
        return $user->can('product.create'); 
    }
}