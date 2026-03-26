<?php

namespace App\Policies;

use App\Models\Buyer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BuyerPolicy
{
    public function viewProfile(User $authUser, Buyer $buyer): bool
    {
        return $buyer !== NULL && $authUser->id === $buyer->user->id;
    }

    public function editProfile(User $authUser, Buyer $buyer): bool
    {
        return $buyer !== NULL && $authUser->id === $buyer->user->id;
    }
}
