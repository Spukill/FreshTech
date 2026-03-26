<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    public $timestamps = false;

    protected $fillable = ['id_order', 'id_product', 'rating', 'description', 'title', 'time_stamp'];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "id_product");
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, "id_order");
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ReportReview::class, 'id_review');
    }
}
