<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ready_semi_products;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReadySemiProductsController extends Controller
{
    // Create
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:readymade,semi_customizable',
            'category_id' => 'required|integer',
            'subcategory_id' => 'required|integer',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'sizes' => 'nullable|array',
            'sizes.*.size' => 'required_with:sizes|string',
            'sizes.*.price' => 'required_with:sizes|numeric',
            'sizes.*.stock' => 'required_with:sizes|integer',
            'attributes' => 'nullable|array',
            'attributes.*.heading' => 'required_with:attributes|string',
            'attributes.*.type' => 'required_with:attributes|string',
            'attributes.*.value' => 'nullable',
            'properties' => 'nullable|string',
        ]);

        // Save images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $filename = Str::uuid()->toString() . '.' . $img->getClientOriginalExtension();
                $path = $img->storeAs('readysemi', $filename, 'public');
                $imagePaths[] = 'storage/' . $path;
            }
        }

        $data['images'] = $imagePaths;
        $product = ready_semi_products::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ]);
    }

    // Read All
    public function index()
    {
        $products = ready_semi_products::all();

        return response()->json([
            'status' => true,
            'message' => 'Product list fetched successfully',
            'data' => $products
        ]);
    }

    // Read One
    public function show($id)
    {
        $product = ready_semi_products::find($id);

        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Product fetched successfully',
            'data' => $product
        ]);
    }

    // Update
    public function update(Request $request, $id)
    {
        $product = ready_semi_products::find($id);

        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:readymade,semi_customizable',
            'category_id' => 'required|integer',
            'subcategory_id' => 'required|integer',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'sizes' => 'nullable|array',
            'sizes.*.size' => 'required_with:sizes|string',
            'sizes.*.price' => 'required_with:sizes|numeric',
            'sizes.*.stock' => 'required_with:sizes|integer',
            'attributes' => 'nullable|array',
            'attributes.*.heading' => 'required_with:attributes|string',
            'attributes.*.type' => 'required_with:attributes|string',
            'attributes.*.value' => 'nullable',
            'properties' => 'nullable|string',
        ]);

        // Save new images if provided
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $filename = Str::uuid()->toString() . '.' . $img->getClientOriginalExtension();
                $path = $img->storeAs('readysemi', $filename, 'public');
                $imagePaths[] = 'storage/' . $path;
            }
            $data['images'] = $imagePaths;
        }

        $product->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    // Delete
    public function destroy($id)
    {
        $product = ready_semi_products::find($id);

        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    // Public API for user
    public function userProducts()
    {
        $products = ready_semi_products::select(
            'id', 'name', 'short_description', 'long_description',
            'images', 'sizes', 'attributes', 'properties'
        )->get();

        return response()->json([
            'status' => true,
            'message' => 'Ready made products fetched successfully',
            'data' => $products
        ]);
    }
}
