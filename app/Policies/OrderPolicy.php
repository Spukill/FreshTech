<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{

    public function view(User $user, Order $order): bool
    {
        return $order->cart->buyer->user->id === $user->id;
    }

    public function cancel(User $user, Order $order): bool
    {
        return $order->cart->buyer->user->id === $user->id && $order->status == "in distribution";
    }

    public function manage(User $user): bool
    {
        return $user->admin != NULL;
    }
}
