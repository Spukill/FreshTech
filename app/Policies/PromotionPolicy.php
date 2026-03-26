<?php

namespace App\Policies;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PromotionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function managePromotion(User $user): bool
    {
        return $user->admin !== NULL;
    }

}
