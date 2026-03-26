<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id_cart',
        'status',
        'date_ord'
    ];

    protected $casts = [
        'date_ord' => 'datetime'
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(ShoppingCart::class, 'id_cart');
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(OrderStatus::class, 'id_order');
    }

    public function orderStatus(): HasOne
    {
        return $this->hasOne(OrderStatus::class, 'id_order');
    }


    // ! not implemented on the vertical prototype
    /*
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'id_order');
    }
    */

    //////// Helpers ////////
    public function products()
    {
        return $this->cart->items->map(fn($item) => $item->product);
    }

    public function total()
    {
        return $this->cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
    }


    protected $appends = ['total_amount'];

public function getTotalAmountAttribute()
{
    return $this->cart->items->sum(function ($item) {
        return $item->product->price * $item->quantity;
    });
}

}
