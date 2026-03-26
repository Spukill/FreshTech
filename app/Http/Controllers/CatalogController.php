<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\Category;

class CatalogController extends Controller
{
    public function showCatalog(Request $request): View
    {
        $query = Product::query();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('id_category', $request->category);
        }

        // Filter by search (name or description)
        if ($request->filled('search')) {
            $search = $request->search;
        
            if (strlen($search) <= 2) {
                $query->where('name', 'ILIKE', "%{$search}%");
            } else {
                $query->select('*')
                    ->selectRaw("ts_rank(tsvectors, plainto_tsquery('english', ?)) AS rank", [$search])
                    ->whereRaw("tsvectors @@ plainto_tsquery('english', ?)", [$search])
                    ->orderByDesc('rank');
            }
        }

        // Filter by price
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Sorting
        if ($request->filled('sort')) {
            if ($request->sort === 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort === 'price_desc') {
                $query->orderBy('price', 'desc');
            }
        }

        // Do the pagination for the products
        $products = $query->paginate(30);

        // Get all categories for filter
        $categories = Category::all();

        // Render the catalog view with products and categories.
        return view('pages.catalog', compact('products', 'categories'));
    }
}