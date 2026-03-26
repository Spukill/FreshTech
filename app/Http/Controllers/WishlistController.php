<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Product;
use App\Events\WishlistUpdated;

class WishlistController extends Controller
{
    public function showWishlist(Request $request): View
    {
        // Only allow authenticated users to see the Wishlist
        $user = Auth::user();
        $buyer = $user->buyer;
        Gate::authorize('viewProfile', $buyer);
    
        /*
        $editMode = $request->query('edit', 'false');

        // Render the 'pages.wishlist' view with the user.
        return view('pages.wishlist', [
            'user' => $user
        ]);
        */

        $wishlist = $buyer->wishlist; // load the wishlist row
    
        return view('pages.wishlist', compact('user', 'wishlist'));
    }

    public function addToWishlist(Product $product)
    {
        $user = Auth::user();
        $buyer = $user->buyer;
 
        if (!$buyer) {
            return back()->with('error', 'Buyer profile not found.');
        }
 
        // Ensure buyer has a Wishlist row, then sync on the pivot products relation
        $wishlist = $buyer->wishlist()->firstOrCreate(['id_buyer' => $buyer->id]);
        $wishlist->products()->syncWithoutDetaching([$product->id]);
 
        broadcast(new WishlistUpdated($product->name, 'added', $buyer->id));
        return back();
    }
 
    public function removeFromWishlist(Product $product)
    {
        $user = Auth::user();
        $buyer = $user->buyer;
 
        if (!$buyer) {
            return back()->with('error', 'Buyer profile not found.');
        }
 
        $wishlist = $buyer->wishlist;
        if ($wishlist) {
            $wishlist->products()->detach($product->id);
        }
 
        broadcast(new WishlistUpdated($product->name, 'removed', $buyer->id));
        return back();
    }
}
