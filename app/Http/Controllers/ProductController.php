<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Subcategory;
use App\Models\Category;


class ProductController extends Controller
{
    //
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'short_description' => 'nullable|string',
        'long_description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'price' => 'required|numeric',
        'quantity' => 'required|integer',
        'category_id' => 'required|exists:categories,id',
        'subcategory_id' => 'required|exists:subcategories,id',
    ]);

    $data = $request->only([
        'name', 'short_description', 'long_description',
        'price', 'quantity', 'category_id', 'subcategory_id'
    ]);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('products', 'public');
        $data['image'] = $path;
    }

    $product = Product::create($data);

    return response()->json([
        'status' => 'success',
        'message' => 'Product added successfully',
        'product' => $product,
    ]);
}
public function index()
{
    $products = Product::with(['category', 'subcategory'])->get();

    return response()->json([
        'status' => 'success',
        'products' => $products
    ]);
}
public function show($id)
{
    $product = Product::with(['category', 'subcategory'])->findOrFail($id);

    return response()->json([
        'status' => 'success',
        'product' => $product
    ]);
}
public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $request->validate([
        'name' => 'sometimes|string|max:255',
        'short_description' => 'nullable|string',
        'long_description' => 'nullable|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'price' => 'sometimes|numeric',
        'quantity' => 'sometimes|integer',
        'category_id' => 'sometimes|exists:categories,id',
        'subcategory_id' => 'sometimes|exists:subcategories,id',
    ]);

    $product->fill($request->only([
        'name', 'short_description', 'long_description',
        'price', 'quantity', 'category_id', 'subcategory_id'
    ]));

    if ($request->hasFile('image')) {
        // Delete old image if needed
        if ($product->image) {
            \Storage::disk('public')->delete($product->image);
        }

        $path = $request->file('image')->store('products', 'public');
        $product->image = $path;
    }

    $product->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Product updated successfully',
        'product' => $product
    ]);
}
public function destroy($id)
{
    $product = Product::findOrFail($id);

    // Delete image from storage
    if ($product->image) {
        \Storage::disk('public')->delete($product->image);
    }

    $product->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Product deleted successfully'
    ]);
}


//home page latest eight products
public function latestEightProducts()
{
    $products = Product::select('id', 'name', 'price', 'image', 'short_description')
        ->latest()
        ->take(8)
        ->get();

    $products->transform(function ($item) {
        $item->image = $item->image ? asset('storage/' . $item->image) : null;
        return $item;
    });

    return response()->json([
        'status' => 'success',
        'products' => $products
    ]);
}

public function allSubcategoriesWithProducts()
{
    $subcategories = Subcategory::with([
        'products' => function ($query) {
            $query->select('id', 'subcategory_id', 'name', 'price', 'image');
        }
    ])
    ->select('id', 'name', 'category_id')
    ->get();

    return response()->json([
        'status' => 'success',
        'subcategories' => $subcategories
    ]);
}

public function filterProducts(Request $request)
{
    $query = Product::with(['category', 'subcategory']);

    // Optional filter: category
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Optional filter: subcategory
    if ($request->filled('subcategory_id')) {
        $query->where('subcategory_id', $request->subcategory_id);
    }

    // Optional filter: minimum price
    if ($request->filled('min_price')) {
        $query->where('price', '>=', $request->min_price);
    }

    // Optional filter: maximum price
    if ($request->filled('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    // Optional search by product name
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Optional sort
    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
        }
    } else {
        // Default sort by newest
        $query->orderBy('created_at', 'desc');
    }

    // Pagination
    $perPage = $request->input('per_page', 12); // default: 12
    $products = $query->paginate($perPage);

    // Format image URL
    $products->getCollection()->transform(function ($item) {
        $item->image = $item->image ? asset('storage/' . $item->image) : null;
        return $item;
    });

    return response()->json([
        'status' => 'success',
        'products' => $products
    ]);
}


public function searchAll(Request $request)
{
    $request->validate([
        'search' => 'required|string|max:255'
    ]);

    $search = $request->search;

    // 🔍 Search Categories by name
    $categories = Category::where('name', 'like', "%{$search}%")
        ->select('id', 'name')
        ->get();

    // 🔍 Search Subcategories by name
    $subcategories = Subcategory::where('name', 'like', "%{$search}%")
        ->select('id', 'name', 'category_id')
        ->get();

    // 🔍 Search Products by name only
    $products = Product::where('name', 'like', "%{$search}%")
        ->select('id', 'name', 'price', 'image', 'category_id', 'subcategory_id')
        ->limit(10) // Optional limit
        ->get()
        ->transform(function ($item) {
            $item->image = $item->image ? asset('storage/' . $item->image) : null;
            return $item;
        });

    return response()->json([
        'status' => 'success',
        'search_term' => $search,
        'categories' => $categories,
        'subcategories' => $subcategories,
        'products' => $products,
    ]);
}


}
