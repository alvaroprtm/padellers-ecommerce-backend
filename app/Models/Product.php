<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'image_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the user that owns the product (supplier).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get all products with supplier info
     */
    public static function getAllWithSupplier()
    {
        return self::with('user:id,name')->latest()->get();
    }

    /**
     * Get products for a specific supplier
     */
    public static function getForSupplier(User $supplier)
    {
        return self::where('user_id', $supplier->id)->latest()->get();
    }

    /**
     * Create a new product for a user
     */
    public static function createForUser(User $user, array $data): self
    {
        return $user->products()->create($data);
    }

    /**
     * Check if product belongs to user
     */
    public function belongsToUser(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

}
