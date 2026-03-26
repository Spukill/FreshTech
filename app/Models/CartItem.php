<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'id_shopping_cart',
        'id_product',
        'quantity'
    ];

    protected $casts = [
        'quantity' => 'integer'
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(ShoppingCart::class, 'id_shopping_cart');
    }
    
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    //////// Helpers ////////
    public function subTotal()
    {
        return $this->product->price * $this->quantity;
    }

    public function discSubTotal() {
        return $this->quantity * $this->product->promotionPrice($this->cart->buyer);
    }
}
