<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    // Add a new category
    public function addCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        

        $data = [
            'name' => $request->name,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = $path;
        }

        $category = Category::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
            'category' => $category
        ]);
    }

    // Add a subcategory under a category
public function addSubcategory(Request $request, $id)
    {
       $request->validate([
    'name' => 'required|string|max:255',
]);

$category = Category::findOrFail($id);

$subcategory = $category->subcategories()->create([
    'name' => $request->name
]);

return response()->json([
    'status' => 'success',
    'message' => 'Subcategory created successfully',
    'subcategory' => $subcategory
]);

    }

    // Get all categories with their subcategories
    public function getCategoriesWithSubcategories()
    {
        $categories = Category::with('subcategories')->get();

        return response()->json([
            'status' => 'success',
            'categories' => $categories
        ]);
    }

   // Update a category

public function updateCategory(Request $request, $id)
{
    try {
        $category = Category::findOrFail($id);

        if ($request->has('name')) {
            $category->name = $request->name;
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $category->image = $path;
        }

        $category->save(); // attempt save

        Log::info('Category updated:', $category->toArray());

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'category' => $category
        ]);

    } catch (\Exception $e) {
        Log::error('Update failed: '.$e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Update failed',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function deleteCategory($id)
{
    $category = Category::findOrFail($id);

    // Optional: delete image
    if ($category->image) {
        Storage::disk('public')->delete($category->image);
    }

    $category->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Category deleted successfully'
    ]);
}
public function updateSubcategory(Request $request, $id)
{
    $subcategory = Subcategory::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $subcategory->name = $request->name;
    $subcategory->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Subcategory updated successfully',
        'subcategory' => $subcategory
    ]);
}

public function deleteSubcategory($id)
{
    $subcategory = Subcategory::findOrFail($id);
    $subcategory->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Subcategory deleted successfully'
    ]);
}




//user categories fetch all 
public function getUserCategories()
{
    $categories = Category::select('id','name', 'image')->get();

    return response()->json([
        'status' => 'success',
        'categories' => $categories
    ]);
}


// Show categories (name + image only) for navbar
public function getNavbarCategories()
{
    $categories = Category::select('id', 'name')->get();

    $categories->transform(function ($category) {
        $category->image = $category->image ? asset('storage/' . $category->image) : null;
        return $category;
    });

    return response()->json([
        'status' => 'success',
        'categories' => $categories
    ]);
}

// Fetch subcategories for a given category ID
public function getSubcategoriesByCategory($id)
{
    $subcategories = Subcategory::where('category_id', $id)->select('id', 'name')->get();

    return response()->json([
        'status' => 'success',
        'subcategories' => $subcategories
    ]);
}




}
