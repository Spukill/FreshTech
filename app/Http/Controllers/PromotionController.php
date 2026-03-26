<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Promotion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PromotionController extends Controller {
    public function show(Request $request) {
        Gate::authorize('managePromotion', Promotion::class);
        $promotions = Promotion::with('product')
            ->orderBy('id_product', 'asc')
            ->get();

        $products = Product::all();
        return view('pages.promotions.manage', compact('promotions', 'products'));
    }

    public function create(Request $request) {
        Gate::authorize('managePromotion', Promotion::class);
        $request->validate([
            'amount' => 'required|integer|min:1|max:100',
            'level_limit' => 'required|integer|min:1|max:5',
        ]);
        $level = $request->level_limit;
        $amount = $request->amount;
        $product = Product::findOrFail($request->productId);
        foreach ($product->promotions as $promotion) {
            if ($promotion->level_limit == $level || ($level > $promotion->level_limit && $amount < $promotion->amount) ||
            ($level < $promotion->level_limit && $amount > $promotion->amount) ) {
                return response()->json(['error' => 'Promotion parameters incorrect'], 433);
            }
        }

        $promotion = Promotion::create([
                'id_product' => $product->id,
                'level_limit' => $level,
                'amount' => $request->amount,
            ]);
        
        $promotion->load('product');

        return response()->json([
            'success' => 'Promotion updated!',
            'promotion' => $promotion
        ]);
    }

    public function get($id) // Changed from Promotion $promotion
    {
        Gate::authorize('managePromotion', Promotion::class);
        $promotion = Promotion::find($id);
        if (!$promotion) {
            return response()->json(['error' => 'Promotion not found'], 404);
        }
        return response()->json($promotion);
    }

    public function edit(Request $request) {
        Gate::authorize('managePromotion', Promotion::class);
        $request->validate([
            'amount' => 'required|integer|min:1|max:100',
            'level_limit' => 'required|integer|min:1|max:5',
        ]);
        $promotion = Promotion::findOrFail($request->promotionId);
        if ($promotion == NULL) {
            return response()->json([
            ], 422);
        }
        $level = $request->level_limit;
        $amount = $request->amount;
        $product = $promotion->product;
        foreach ($product->promotions as $promo) {
            if (($promo->level_limit == $level && $promo->id != $promotion->id) ||
            ($level > $promo->level_limit && $amount < $promo->amount) ||
            ($level < $promo->level_limit && $amount > $promo->amount) ) {
                return response()->json([
                ], 422);
            }
        }

        $promotion->update([
            'level_limit' => $level,
            'amount' => $amount,
        ]);

        return response()->json([
            'success' => 'Promotion updated!',
            'promotion' => $promotion
        ]);
    }

    public function delete(Request $request, $promotionId) {
        Gate::authorize('managePromotion', Promotion::class);
        $promotion = Promotion::findOrFail($promotionId);
        if ($promotion == NULL) {
            return redirect()->back()->withErrors('That promotion does not exist.');
        }
        $deleted = DB::table('promotions')->where('id', '=', $promotion->id)->delete();

        return redirect()->back()->with('success', 'Promotion Deleted!');
    }
}
