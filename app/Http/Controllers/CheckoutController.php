<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

use App\Models\Buyer;
use App\Models\ShoppingCart;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Http\Controllers\NotificationController;

class CheckoutController extends Controller
{
    // Show checkout page with cart summary
    public function showCheckout(): View|RedirectResponse
    {
        \Log::info('=== SHOW CHECKOUT CALLED ===');
        
        $user = Auth::user();
        $buyer = $user->buyer ?? null;

        \Log::info('User and buyer check', ['user_id' => $user?->id, 'buyer_id' => $buyer?->id]);

        if (!$buyer) {
            \Log::warning('No buyer profile found');
            return redirect()->route('cart')->with('error', 'Buyer profile not found.');
        }

        // Get cart without orders (same logic as CartController)
        $cart = $buyer->shoppingCart()
            ->whereDoesntHave('orders')
            ->with('items.product')
            ->first();
        
        // Create cart if it doesn't exist
        if (!$cart) {
            $cart = $buyer->shoppingCart()->create([
                'id_buyer' => $buyer->id,
            ]);
        }

        \Log::info('Cart loaded', ['cart_id' => $cart?->id, 'items_count' => $cart?->items?->count()]);

        if (!$cart || $cart->items->isEmpty()) {
            \Log::warning('Cart is empty or null');
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        // compute total
        $total = $cart->discountPrice();

        \Log::info('Rendering checkout view', ['total' => $total]);

        return view('pages.checkout', [
            'buyer' => $buyer,
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    // Process payment and create order
    public function processCheckout(Request $request)
    {
        \Log::info('=== CHECKOUT STARTED ===');
        
        // base validation
        $data = $request->validate([
            'shipping_address' => 'required|string|max:1000',
            'payment_method'   => 'required|in:card,paypal,mbway',
            'agree'            => 'accepted',
        ]);

        // conditional validation
        if ($request->input('payment_method') === 'card') {
            // Strip spaces from card number before validation
            $cardNumber = str_replace(' ', '', $request->input('card_number', ''));
            $request->merge(['card_number' => $cardNumber]);
            
            $request->validate([
                'card_name'   => 'required|string|max:255',
                // require digits only (12-19)
                'card_number' => 'required|digits_between:12,19',
                // expiry as MM/YY - regex to accept format like 12/25
                'card_expiry' => ['required', 'regex:/^\d{2}\/\d{2}$/'],
                'card_cvc'    => 'required|digits_between:3,4',
            ]);
        } else {
            $request->validate([
                'phone' => 'required|string|max:30',
            ]);
        }

        $user = Auth::user();
        $buyer = $user->buyer ?? null;

        if (!$buyer) {
            return redirect()->route('home')->with('error', 'Buyer profile not found.');
        }

        // Get cart without orders (same logic as CartController and showCheckout)
        $cart = $buyer->shoppingCart()
            ->whereDoesntHave('orders')
            ->with('items.product')
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        // compute total
        $total = $cart->items->reduce(function ($carry, $item) {
            $price = $item->product->price ?? 0;
            return $carry + ($price * $item->quantity);
        }, 0);

        // simulate payment success
        $paymentSucceeded = true;

        if (! $paymentSucceeded) {
            return back()->with('error', 'Payment failed. Please try again.');
        }

        \Log::info('=== STARTING ORDER CREATION ===', ['buyer_id' => $buyer->id, 'cart_id' => $cart->id]);

        DB::beginTransaction();
        try {
            \Log::info('=== INSIDE TRANSACTION ===');
            
            // create order (matches your schema)
            $order = Order::create([
                'id_cart'  => $cart->id,
                'status'   => 'in distribution',
                'date_ord' => Carbon::now(),
            ]);

            DB::transaction(function () use ($order) {
                foreach ($order->cart->items as $item) {
                    if ($item->product->stock < $item->quantity) {
                        throw new \Exception("Not enough stock for product: " . $item->product->name);
                    }
                    $item->product->decrement('stock', $item->quantity);
                }
            });

            \Log::info('Order created', ['order_id' => $order->id]);

            // Create notification for order placement FIRST
            try {
                \Log::info('About to create notification for order', ['order_id' => $order->id, 'buyer_id' => $buyer->id]);
                $notification = NotificationController::createOrderStatusNotification(
                    $order->id, 
                    $buyer->id, 
                    'in distribution'
                );
                \Log::info('Notification created', ['notification_id' => $notification->id]);

                // link order_status with the notification
                OrderStatus::create([
                    'id_notification' => $notification->id,
                    'id_order' => $order->id,
                    'id_buyer' => $buyer->id,
                ]);
                \Log::info('OrderStatus created');
            } catch (\Exception $e) {
                \Log::error('Failed to create notification or order_status: ' . $e->getMessage());
                throw $e; // Re-throw to rollback transaction
            }

            // Update buyer experience points
            $exp = $buyer->exp + ((int)$total);
            if ($exp > 3000) $buyer->exp = 3000;
            else $buyer->exp = $exp;
            $buyer->save();

            \Log::info('Buyer exp updated', ['buyer_id' => $buyer->id, 'new_exp' => $buyer->exp]);

            // Note: We keep the cart items so the order can display them
            // The cart is filtered out from active carts using whereDoesntHave('orders')

            DB::commit();

            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('success', 'Payment successful. Order placed.');

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Checkout failed: ' . $e->getMessage());
            return back()->with('error', 'Could not place order. Please contact support.');
        }
    }

    // Show a simple order confirmation page
    public function success(int $orderId): View
    {
        $order = Order::with(['orderStatus'])->findOrFail($orderId);
        $user = Auth::user();
        return view('pages.order', ['order' => $order, 'user' => $user]);
    }
}