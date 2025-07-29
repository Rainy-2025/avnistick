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
            'status' => true,
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
            'status' => true,
            'message' => 'Subcategory created successfully',
            'subcategory' => $subcategory
        ]);
    }

    // ✅ Get all categories with subcategories
    public function getCategoriesWithSubcategories()
    {
        $categories = Category::with('subcategories')->get();

        return response()->json([
            'status' => true,
            'message' => 'Fetched all categories with subcategories',
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
            'status' => true,
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
            'status' => true,
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
            'status' => true,
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
            'status' => true,
            'message' => 'Subcategory deleted successfully'
        ]);
    }

    // ✅ Show categories (for user frontend)
    public function getUserCategories()
    {
        $categories = Category::select('id', 'name')->get();

        return response()->json([
            'status' => true,
            'message' => 'User categories fetched successfully',
            'categories' => $categories
        ]);
    }

    // ✅ Navbar categories with image URL
    public function getNavbarCategories()
    {
        $categories = Category::select('id', 'name')->get();

        return response()->json([
            'status' => true,
            'message' => 'Navbar categories fetched successfully',
            'categories' => $categories
        ]);
    }

    // ✅ Get subcategories by category ID
  public function getSubcategoriesByCategory($id)
{
    if ($id == 0) {
        // Return all subcategories
        $subcategories = Subcategory::select('id', 'name', 'image', 'category_id')->get();
    } else {
        // Return subcategories for specific category
        $subcategories = Subcategory::where('category_id', $id)
            ->select('id', 'name', 'image', 'category_id')
            ->get();
    }

    // Add full image URL
    $subcategories = $subcategories->map(function ($sub) {
        $sub->image = $sub->image ? asset('storage/' . $sub->image) : null;
        return $sub;
    });

    return response()->json([
        'status' => true,
        'message' => $id == 0 ? 'All subcategories fetched successfully' : 'Subcategories fetched successfully',
        'subcategories' => $subcategories
    ]);
}

}
