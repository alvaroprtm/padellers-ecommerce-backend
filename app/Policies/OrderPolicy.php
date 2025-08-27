<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('order.view');
    }

    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        if ($order->user_id === $user->id) {
            return $user->can('order.view');
        }

        if ($user->hasRole('supplier')) {
            return $order->orderItems()
                ->whereHas('product', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->exists();
        }

        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create orders.
     */
    public function create(User $user): bool
    {
        return $user->can('order.create');
    }

    /**
     * Determine whether the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->hasRole('supplier')) {
            return $order->orderItems()
                ->whereHas('product', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->exists();
        }

        // Admins can update any order
        return $user->hasRole('admin') && $user->can('order.edit');
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        if ($order->user_id === $user->id) {
            return $user->can('order.delete');
        }

        return $user->hasRole('admin') && $user->can('order.delete');
    }

    /**
     * Determine whether the user can view supplier orders.
     */
    public function viewAsSupplier(User $user): bool
    {
        return $user->hasRole('supplier') || $user->hasRole('admin');
    }
}