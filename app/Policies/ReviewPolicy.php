<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    public function createReview(User $user): bool
    {
        return $user->buyer !== NULL && $user->buyer->shoppingCart !== NULL;
    }

    public function deleteReview(User $user, Review $review): bool
    {   
        if ($user === NULL) return FALSE;
        if ($user->admin !== NULL) return TRUE;
        $buyer = $user->buyer;
        return $buyer !== NULL && $review->order->cart->buyer->id === $buyer->id;
    }

    public function updateReview(User $user, Review $review): bool
    {   
        if ($user === NULL) return FALSE;
        $buyer = $user->buyer;
        return $buyer !== NULL && $review->order->cart->buyer->id === $buyer->id;
    }
}
