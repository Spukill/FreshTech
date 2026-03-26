<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\Category;

class CategoryController extends Controller
{
    public function manage()
{
    return view('pages.categories.manage', [
        'categories' => Category::all()
    ]);
}

public function store(Request $request)
{
    $category = Category::create([
        'name' => $request->name,
        'description' => $request->description ?? '',
    ]);

    return response()->json([
        'category' => $category
    ]);
}

public function editJson(Category $category)
{
    return response()->json($category);
}

public function update(Request $request, Category $category)
{
    $category->update([
        'name' => $request->name
    ]);

    return response()->json([
        'category' => $category
    ]);
}

public function destroy($id)
{
    $category = Category::find($id);

    if ($category) {
        $category->products()->delete();

        $category->delete();

        return redirect()->route('categories.index')
                         ->with('success', 'Categoria e produtos apagados com sucesso!');
    }

    return redirect()->route('categories.index')
                     ->with('error', 'Categoria não encontrada.');
}
}