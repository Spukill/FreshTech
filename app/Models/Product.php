<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Controllers\NotificationController;

class Product extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'description', 'price', 'stock', 'image1', 'image2', 'image3', 'id_category'];

    protected $casts = ['tsvectors' => 'string',];

    protected static function booted()
    {
        static::updating(function ($product) {
            // Check if stock is changing
            if ($product->isDirty('stock')) {
                $oldStock = $product->getOriginal('stock');
                $newStock = $product->stock;

                // Get all buyers who have this product in their wishlist
                $buyers = \App\Models\Buyer::whereHas('wishlist.products', function ($query) use ($product) {
                    $query->where('products.id', $product->id);
                })->get();

                foreach ($buyers as $buyer) {
                    // Product went from out of stock to in stock
                    if ($oldStock <= 0 && $newStock > 0) {
                        NotificationController::createProductAvailableNotification(
                            $product->id,
                            $product->name,
                            $buyer->id
                        );
                    }
                    // Product went from in stock to out of stock
                    elseif ($oldStock > 0 && $newStock <= 0) {
                        NotificationController::createProductOutOfStockNotification(
                            $product->id,
                            $product->name,
                            $buyer->id
                        );
                    }
                }
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpec::class, 'id_product');
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'id_product');
    }
    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class, 'id_product');
    }

    ////// Helper //////
    public function promotionPrice(Buyer $buyer) {
        $promotion = $buyer->getPromotion($this->promotions);
        if ($promotion !== NULL) return $this->price * (1 - ($promotion->amount / 100));
        return $this->price;
    }

    public function fullDetails()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => floatval($this->price),
            'stock' => $this->stock,
            'image1' => $this->image1,
            'image2' => $this->image2,
            'image3' => $this->image3,
            'categoryId' => $this->id_category,

            'specifications' => $this->specifications
                                ->get()
                                ->map(fn($spec) => [
                                    'key' => $spec->spec_key,
                                    'value' => $spec->spec_value
                                ])
        ];
    }
}
