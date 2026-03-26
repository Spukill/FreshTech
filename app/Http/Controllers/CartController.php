<?php

namespace App\Http\Controllers;

use App\Models\ShoppingCart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Events\ProductAddedToCart;
use App\Events\CartUpdated;

class CartController extends Controller
{
    public function showCart(Request $request): View
    {
        $buyer = Auth::user()->buyer;
        Gate::authorize('viewProfile', $buyer);

        // Use o método correto do Buyer
        $cart = $buyer->shoppingCart()
            ->whereDoesntHave('orders')   // cart without any order
            ->with(['items.product'])
            ->first();
        if (! $cart) {
            $cart = $buyer->shoppingCart()->create([
                'id_buyer' => $buyer->id,
            ]);
        }

        $cartItems = $cart ? $cart->items : [];
        $totalPrice = $cart ? $cart->items->sum(fn($item) => $item->quantity * $item->product->price) : 0;
        $discPrice = $cart ? $cart->discountPrice() : 0;

        return view('pages.cart', compact('cartItems', 'totalPrice', 'discPrice', 'buyer'));
    }

    public function addToCart(Request $request, $productId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $product = Product::findOrFail($productId);
        $buyer = Auth::user()->buyer;
        if (!$buyer) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Buyer profile not found.'], 404);
            }
            return back()->with('error', 'Buyer profile not found.');
        }

        $cart = $buyer->shoppingCart()
            ->whereDoesntHave('orders')   // cart without any order
            ->with(['items.product'])
            ->first();
        if (!$cart) {
            $cart = $buyer->shoppingCart()->create([
                'id_buyer' => $buyer->id,
            ]);
        }

        $cartItem = $cart->items()->firstOrNew([
            'id_product' => $product->id,      // must be a valid ID
            'id_shopping_cart' => $cart->id,   // set the cart FK
        ]);

        $cartItem->quantity = ($cartItem->quantity ?? 0) + $request->quantity;
        $cartItem->save();

        // Broadcast real-time notification
        broadcast(new ProductAddedToCart($product->name, $request->quantity, $buyer->id));

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart',
                'product' => $product->name,
                'quantity' => $request->quantity,
                'cartItemCount' => $cart->items->sum('quantity')
            ]);
        }

        return back();
    }

    public function removeFromCart(Request $request, $productId)
    {
        $buyer = Auth::user()->buyer;
        $cart = $buyer->shoppingCart()
            ->whereDoesntHave('orders')   // cart without any order
            ->with(['items.product'])
            ->first();
        if (! $cart) {
            $cart = $buyer->shoppingCart()->create([
                'id_buyer' => $buyer->id,
            ]);
        }
        if ($cart) {
            $cart->items()->where('id_product', $productId)->delete();
        }

        broadcast(new CartUpdated('Item removed from cart', $buyer->id));
        
        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            // Refresh cart data
            $cart->load('items.product');
            $itemCount = $cart->items->count();
            $totalPrice = $cart->items->sum(fn($item) => $item->quantity * $item->product->price);
            $discPrice = $cart->discountPrice();
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'itemCount' => $itemCount,
                'totalPrice' => number_format($totalPrice, 2),
                'discPrice' => number_format($discPrice, 2),
                'isEmpty' => $itemCount === 0
            ]);
        }
        
        return back();
    }

    public function updateQuantity(Request $request, $productId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $buyer = Auth::user()->buyer;
        $cart = $buyer->shoppingCart()
            ->whereDoesntHave('orders')   // cart without any order
            ->with(['items.product'])
            ->first();
        if (! $cart) {
            $cart = $buyer->shoppingCart()->create([
                'id_buyer' => $buyer->id,
            ]);
        }

        if ($cart) {
            $cart->items()->where('id_product', $productId)->update([
                'quantity' => $request->quantity
            ]);
        }
        
        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            // Get updated item data
            $cart->load('items.product');
            $cartItem = $cart->items()->where('id_product', $productId)->first();
            
            if ($cartItem) {
                $itemSubtotal = $cartItem->subTotal();
                $itemDiscSubtotal = $cartItem->discSubTotal();
                $totalPrice = $cart->items->sum(fn($item) => $item->quantity * $item->product->price);
                $discPrice = $cart->discountPrice();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Quantity updated',
                    'itemSubtotal' => number_format($itemSubtotal, 2),
                    'itemDiscSubtotal' => number_format($itemDiscSubtotal, 2),
                    'hasDiscount' => $itemDiscSubtotal != $itemSubtotal,
                    'totalPrice' => number_format($totalPrice, 2),
                    'discPrice' => number_format($discPrice, 2)
                ]);
            }
        }

        return back();
    }

}


