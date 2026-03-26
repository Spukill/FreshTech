<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Wishlist extends Model
{
    protected $table = 'wishlists';
    public $timestamps = false;

    protected $fillable = [
        'id_buyer',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'id_buyer');
    }

    // Many-to-many to products via wishlist_products (id_wishlist, id_product)
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlist_products', 'id_wishlist', 'id_product');
    }

    // Convenience helpers (optional)
    public function addProduct($product)
    {
        $id = $product instanceof Product ? $product->id : $product;
        $this->products()->syncWithoutDetaching([$id]);
    }

    public function removeProduct($product)
    {
        $id = $product instanceof Product ? $product->id : $product;
        $this->products()->detach($id);
    }
}
