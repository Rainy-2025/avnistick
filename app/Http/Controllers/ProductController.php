<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

}
