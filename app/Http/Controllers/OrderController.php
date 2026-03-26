<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Controllers\NotificationController;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function manage() {
        Gate::authorize('manage', Order::class);
        $orders = Order::with('cart')->orderBy('date_ord', 'desc')->get();
        return view('pages.orders.manage', compact('orders'));
    }

    public function details(Order $order) {
        Gate::authorize('manage', Order::class);
        $order->load('cart.items.product');
        return view('pages.orders.details', compact('order'));
    }

    public function edit(Order $order) {
        $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Canceled'];

        return view('pages.orders.edit', compact('order', 'statuses'));
    }

    public function update(Request $request, Order $order) {
        Gate::authorize('manage', Order::class);
        $request->validate([
            'status' => 'required|string|max:255'
        ]);

        $oldStatus = $order->status;
        $order->status = $request->status;
        $order->save();

        // Update buyer exp if delivered
        if ($order->status == 'delivered') {
            $buyer = $order->cart->buyer;
            $exp = $buyer->exp + ((int)$order->cart->totalPrice());
            if ($exp > 3000) $buyer->exp = 3000;
            else $buyer->exp = $exp;
            $buyer->save();
        }

        // If status changed, create notification
        if ($oldStatus !== $request->status) {
    $orderStatus = $order->orderStatus()->first();
    if ($orderStatus && $orderStatus->id_buyer) {

        NotificationController::createOrderStatusNotification(
            $order->id,
            $request->status
        );

        // 🔥 DISPARA O EVENTO
        broadcast(new OrderStatusUpdated(
            $order->id,
            $request->status
        ))->toOthers();
    }
}


        return response()->json([
        'success' => 'Order status updated!'
    ]);
    }

}
