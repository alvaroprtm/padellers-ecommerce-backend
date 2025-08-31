<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    const STATUS_PENDING = 'pending';

    const STATUS_PAID = 'paid';

    const STATUS_SHIPPED = 'shipped';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Create a new order from cart items
     */
    public static function createFromItems(User $user, array $items): self
    {
        return DB::transaction(function () use ($user, $items) {
            $order = $user->orders()->create([
                'status' => self::STATUS_PENDING,
                'price' => 0,
            ]);

            $order->addItems($items);
            $order->calculateAndUpdateTotal();

            return $order->load(['orderItems.product']);
        });
    }

    /**
     * Add items to the order
     */
    public function addItems(array $items): void
    {
        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);

            $this->orderItems()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'name' => $product->name,
                'description' => $product->description
            ]);
        }
    }

    /**
     * Calculate and update the order total
     */
    public function calculateAndUpdateTotal(): void
    {
        $total = $this->orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $this->update(['price' => $total]);
    }

    /**
     * Get the total amount for this order
     */
    public function getTotalAmount(): float
    {
        return $this->orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Cancel the order
     */
    public function cancel(): void
    {
        if (! $this->canBeCancelled()) {
            throw new \InvalidArgumentException('Only pending orders can be cancelled');
        }

        $this->delete();
    }

    /**
     * Update order status with validation
     */
    public function updateStatus(string $status): void
    {
        $validStatuses = [
            self::STATUS_PENDING,
            self::STATUS_PAID,
            self::STATUS_SHIPPED,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];

        if (! in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}. Valid statuses are: ".implode(', ', $validStatuses));
        }

        $this->update(['status' => $status]);
    }

    /**
     * Get orders for a specific user
     */
    public static function getForUser(User $user)
    {
        return self::where('user_id', $user->id)
            ->with(['orderItems.product'])
            ->latest()
            ->get();
    }

    /**
     * Get orders containing products from a specific supplier
     */
    public static function getForSupplier(User $supplier)
    {
        return self::whereHas('orderItems.product', function ($query) use ($supplier) {
            $query->where('user_id', $supplier->id);
        })
            ->with([
                'user:id,name,email',
                'orderItems' => function ($query) use ($supplier) {
                    $query->whereHas('product', function ($q) use ($supplier) {
                        $q->where('user_id', $supplier->id);
                    });
                },
                'orderItems.product',
            ])
            ->latest()
            ->get();
    }
}
