<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use App\Models\Order;

use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Events\ReviewAction;

class ReviewController extends Controller
{
    /**
     * Store a new review for a product, linked via the user's order.
     */
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $product = Product::find($productId);
        Gate::authorize('createReview', Review::class);
        // Encontra a ordem do usuário que contém este produto
        foreach ($user->buyer->shoppingCart as $cart) {
            $order = $cart->productDelivered($product);
            if ($order !== NULL) break;
        }

        if ($order === NULL) {
            return redirect()->back()->withErrors('You must purchase this product before reviewing.');
        }

        // Cria a review vinculada à ordem correta
        try {
            $review = Review::create([
                'id_product' => $product->id,
                'id_order' => $order->id,
                'rating' => $request->rating,
                'description' => $request->comment,
                'title' => null,
                'time_stamp' => now(),
            ]);
        } catch (\PDOException $e) {
            return redirect()->back()->withErrors('You must purchase this product before reviewing.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('You must purchase this product before reviewing.');
        }

        broadcast(new ReviewAction('Review submitted successfully!', $user->buyer->id));
        return redirect()->back();
    }

    public function delete($reviewId) {
        $review = Review::find($reviewId);
        Gate::authorize('deleteReview', $review);
        $buyerId = $review->order->cart->buyer->id;
        $deleted = DB::table('reviews')->where('id', '=', $review->id)->delete();

        broadcast(new ReviewAction('Review deleted!', $buyerId));
        return redirect()->back();
    }

    public function edit(Review $review)
    {
        Gate::authorize('updateReview', $review);

        return response()->json($review);
    }

    public function update(Request $request)
    {
        
        $review = Review::find($request->reviewId);
        Gate::authorize('updateReview', $review);

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }
            
        $review->update([
            'rating' => $request->rating,
            'description' => $request->comment,
        ]);

        return response()->json([
            'success' => 'Review atualizada com sucesso!',
            'review' => $review
        ]);
    }
}

