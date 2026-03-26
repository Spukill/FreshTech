<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;


class ProductController extends Controller
{
    // Mostra página do produto individual
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        // Busca reviews do produto via orders → cart → items
        $reviews = $product->reviews;

        $averageRating = $reviews->avg('rating');

        // Check if product is in user's wishlist
        $promotion = NULL;
        $inWishlist = false;
        if (Auth::check()) {
            $user = Auth::user();
            $buyer = $user->buyer;
            if ($buyer && $buyer->wishlist) {
                $inWishlist = $buyer->wishlist->products()->where('products.id', $product->id)->exists();
            }

            if ($buyer !== NULL) {
                $promotion = $buyer->getPromotion($product->promotions);
            }
        }



        return view('pages.product', compact('product', 'reviews', 'inWishlist', 'promotion', 'averageRating'));
    }

    // ----------------------------
    // Dashboard: Manage Products
    // ----------------------------
   public function manage()
{
    Gate::authorize('manage', Product::class);
    $products = Product::select('id', 'name', 'price', 'stock', 'id_category')
        ->with('category:id,name')
        ->orderByDesc('stock')
        ->get();

    $categories = Category::all(); // <--- Adicione isto

    return view('pages.products.manage', compact('products', 'categories'));
}

    public function store(Request $request)
    {
        Gate::authorize('manage', Product::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'id_category' => 'required|exists:categories,id', 
            'image'       => 'required|array|size:3', 
            'image.*'     => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'image.size' => 'You must upload exactly 3 images.',
        ]);

        $data = $request->only(['name', 'price', 'stock', 'id_category']);

        if ($request->hasFile('image')) {
            $files = $request->file('image');
            $destinationPath = public_path('images/products');

            foreach ($files as $index => $file) {
                $columnName = 'image' . ($index + 1);
                $fileName = $file->hashName();
                $file->move($destinationPath, $fileName);
                $data[$columnName] = $fileName;
            }
        }

    $product = Product::create($data);

    // Retorna JSON se a requisição for AJAX
    if ($request->ajax()) {
        // Carrega a categoria para retornar com o produto
        $product->load('category');
        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }

    // Para requisições normais
    return redirect()->route('products.manage')->with('success', 'Product created!');
}


    // ----------------------------
    // Edit / Update Product
    // ----------------------------
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        return view('pages.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'id_category' => 'required|exists:categories,id',
            'image'       => 'nullable|array|size:3',
            'image.*'     => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'image.size' => 'To change images, you must upload exactly 3.',
        ]);

        $product = Product::findOrFail($id);

        $data = $request->only(['name', 'price', 'stock', 'id_category']);

        if ($request->hasFile('image')) {
            $files = $request->file('image');
            $destinationPath = public_path('images/products');

            foreach ($files as $index => $file) {
                $columnName = 'image' . ($index + 1);
                $oldImage = $product->$columnName;
                if ($oldImage && file_exists($destinationPath . '/' . $oldImage)) {
                    unlink($destinationPath . '/' . $oldImage);
                }
                $fileName = $file->hashName();
                $file->move($destinationPath, $fileName);
                $data[$columnName] = $fileName;
            }
        }

        $product->update($data);


        if ($request->ajax()) {
            $product->load('category');
            return response()->json(['success' => true, 'product' => $product]);
        }
        
        return redirect()->route('products.manage')->with('success', 'Product updated!');


    }

    // ----------------------------
    // Delete Product
    // ----------------------------
    public function destroy($id)
    {
        Gate::authorize('manage', Product::class);
        $product = Product::findOrFail($id);

        // Opcional: excluir imagem do storage se existir
        if ($product->image) {
            \Storage::delete('public/' . $product->image);
        }

        $product->delete();

        return redirect()->route('products.manage')->with('success', 'Product deleted successfully!');
    }

    public function editJson($id)
    {
        Gate::authorize('manage', Product::class);
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    // ----------------------------
    // Dashboard: Manage Stock
    // ----------------------------

    public function updateStock(Request $request, $id)
    {
        Gate::authorize('manage', Product::class);
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->stock = $request->stock;
        $product->save();

        return redirect()->back()->with('success', 'Stock updated successfully!');
    }
}
