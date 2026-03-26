<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Controllers\FileController;

class Buyer extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'user_name',
        'exp',
        'profile_image'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function shoppingCart(): HasMany
    {
        return $this->hasMany(ShoppingCart::class, 'id_buyer');
    }

    public function orderStatus(): HasMany
    {
        return $this->hasMany(OrderStatus::class, 'id_buyer');
    }

    public function wishlist(): HasOne
    {
        return $this->hasOne(Wishlist::class, 'id_buyer');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'id_buyer');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'id_buyer');
    }

    public function getLevel() {
        $exp = $this->exp;
        if ($exp == 3000) return 5;
        else if ($exp > 2000) return 4;
        else if ($exp > 1500) return 3;
        else if ($exp > 1000) return 2;
        else if ($exp > 500) return 1;

        return 0;
    }

    public function getPromotion($promotions) {
        $promotion = NULL;
        $max_level = 0;
        $user_level = $this->getLevel();
        foreach ($promotions as $promo) {
            if ($promo->limit_level > $max_level && $promo->limit_level <= $user_level) {
                $promotion = $promo;
                $max_level = $promo->level_limit;
            }
        }

        return $promotion;
    }

    public function getProfileImage(){
        return FileController::get('profile', $this->id);
    }
}
