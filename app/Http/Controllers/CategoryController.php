<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    // ✅ Add a new category (only name)
    public function addCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
            'category' => $category
        ]);
    }

    // ✅ Add a subcategory (name + image) under category
    public function addSubcategory(Request $request, $categoryId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'category_id' => $categoryId,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('subcategories', 'public');
        }

        $subcategory = Subcategory::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Subcategory created successfully',
            'subcategory' => $subcategory
        ]);
    }

    // ✅ Get all categories with subcategories
    public function getCategoriesWithSubcategories()
    {
        $categories = Category::with('subcategories')->get();

        return response()->json([
            'status' => 'success',
            'categories' => $categories
        ]);
    }

    // ✅ Update category (only name)
    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id);
        $category->update(['name' => $request->name]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'category' => $category
        ]);
    }

    // ✅ Delete category
    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully'
        ]);
    }

    // ✅ Update subcategory (name + image)
    public function updateSubcategory(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $subcategory->name = $request->name;

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($subcategory->image) {
                Storage::disk('public')->delete($subcategory->image);
            }

            $subcategory->image = $request->file('image')->store('subcategories', 'public');
        }

        $subcategory->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Subcategory updated successfully',
            'subcategory' => $subcategory
        ]);
    }

    // ✅ Delete subcategory
    public function deleteSubcategory($id)
    {
        $subcategory = Subcategory::findOrFail($id);

        if ($subcategory->image) {
            Storage::disk('public')->delete($subcategory->image);
        }

        $subcategory->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Subcategory deleted successfully'
        ]);
    }

    // ✅ Show categories (for user frontend)
    public function getUserCategories()
    {
        $categories = Category::select('id', 'name')->get();

        return response()->json([
            'status' => 'success',
            'categories' => $categories
        ]);
    }

    // ✅ Navbar categories with image URL
    public function getNavbarCategories()
    {
        $categories = Category::select('id', 'name')->get();

        return response()->json([
            'status' => 'success',
            'categories' => $categories
        ]);
    }

    // ✅ Get subcategories by category ID
    public function getSubcategoriesByCategory($id)
    {
        $subcategories = Subcategory::where('category_id', $id)
            ->select('id', 'name', 'image')
            ->get()
            ->map(function ($sub) {
                $sub->image = $sub->image ? asset('storage/' . $sub->image) : null;
                return $sub;
            });

        return response()->json([
            'status' => 'success',
            'subcategories' => $subcategories
        ]);
    }
}
