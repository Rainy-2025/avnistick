<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomizeProduct;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomizeProductController extends Controller
{
    // ✅ Admin Create
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'subcategory_id' => 'required|integer',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'attributes' => 'nullable|array',
            'attributes.*.heading' => 'required_with:attributes|string',
            'attributes.*.type' => 'required_with:attributes|string',
            'attributes.*.value' => 'nullable',
            'properties' => 'nullable|string',
            'expected_price' => 'nullable|numeric'
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('customize', 'public');
                $imagePaths[] = $path;
            }
        }

        $product = CustomizeProduct::create([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'short_description' => $validated['short_description'] ?? null,
            'long_description' => $validated['long_description'] ?? null,
            'images' => json_encode($imagePaths),
            'attributes' => json_encode($validated['attributes'] ?? []),
            'properties' => $validated['properties'] ?? null,
            'expected_price' => $validated['expected_price'] ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Customizable product added successfully.',
            'data' => $product
        ]);
    }

    // ✅ User Side - Fetch All
    public function index()
    {
        $products = CustomizeProduct::latest()->get();
        return response()->json([
            'status' => true,
            'message' => 'All customizable products fetched.',
            'data' => $products
        ]);
    }

    // ✅ Admin Fetch All
    public function adminIndex()
    {
        $products = CustomizeProduct::latest()->get();
        return response()->json([
            'status' => true,
            'message' => 'Admin: all customizable products fetched.',
            'data' => $products
        ]);
    }

    // ✅ Admin Fetch One
    public function show($id)
    {
        $product = CustomizeProduct::find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Product fetched successfully.',
            'data' => $product
        ]);
    }

    // ✅ Admin Update
    public function update(Request $request, $id)
    {
        $product = CustomizeProduct::find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'subcategory_id' => 'required|integer',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'attributes' => 'nullable|array',
            'attributes.*.heading' => 'required_with:attributes|string',
            'attributes.*.type' => 'required_with:attributes|string',
            'attributes.*.value' => 'nullable',
            'properties' => 'nullable|string',
            'expected_price' => 'nullable|numeric'
        ]);

        $imagePaths = json_decode($product->images ?? '[]', true);
        if ($request->hasFile('images')) {
            foreach ($imagePaths as $oldPath) {
                Storage::disk('public')->delete($oldPath);
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('customize', 'public');
                $imagePaths[] = $path;
            }
        }

        $product->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'short_description' => $validated['short_description'] ?? null,
            'long_description' => $validated['long_description'] ?? null,
            'images' => json_encode($imagePaths),
            'attributes' => json_encode($validated['attributes'] ?? []),
            'properties' => $validated['properties'] ?? null,
            'expected_price' => $validated['expected_price'] ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully.',
            'data' => $product
        ]);
    }

    // ✅ Admin Delete
    public function destroy($id)
    {
        $product = CustomizeProduct::find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        // Delete images
        $imagePaths = json_decode($product->images ?? '[]', true);
        foreach ($imagePaths as $path) {
            Storage::disk('public')->delete($path);
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }
}
