<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ShoppingCart extends Model
{
    public $timestamps = false;
    protected $table = 'shopping_carts';

    protected $fillable = [
        'id_buyer',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'id_buyer');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'id_shopping_cart');
    }

    public function orders(): HasOne
    {
        return $this->hasOne(Order::class, 'id_cart');
    }

    //////// Helpers ////////
    public function totalPrice()
    {
        return $this->items->sum(fn ($item) =>
            $item->product->price * $item->quantity
        );
    }

    public function discountPrice() {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->discSubTotal();
        }
        return $total;
    }

    public function productDelivered(Product $product) {
        $this->loadMissing('items.product', 'orders');
        if ($this->orders === NULL) return NULL;
        if ($this->orders->status !== "delivered") return NULL;
        foreach ($this->items as $item) {
            if ($item->product->id === $product->id) return $this->orders;
        }
        return NULL;
    }
}
