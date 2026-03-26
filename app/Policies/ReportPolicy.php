<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function manageReport(User $user): bool
    {
        return $user->admin != NULL;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function createReport(User $user, Review $review): bool
    {
        return $user->buyer != NULL && $review->order->cart->buyer->id != $user->buyer->id;
    }

}
