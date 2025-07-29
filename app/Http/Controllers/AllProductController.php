<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ready_semi_products;
use App\Models\CustomizeProduct;

class AllProductController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type'); // optional
        $categoryId = $request->query('category_id'); // optional
        $subcategoryId = $request->query('subcategory_id'); // optional
        $search = $request->query('search'); // optional

        // ✅ ReadyMade + Semi-Customizable
        $readyProducts = ready_semi_products::query()
            ->when($type, function ($q) use ($type) {
                $q->where('type', $type); // readymade or semi_customizable
            })
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($subcategoryId, fn($q) => $q->where('subcategory_id', $subcategoryId))
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => $item->type,
                    'name' => $item->name,
                    'category_id' => $item->category_id,
                    'subcategory_id' => $item->subcategory_id,
                    'short_description' => $item->short_description,
                    'image' => $item->images[0] ?? null,
                    'source' => 'ready_semi'
                ];
            });

        // ✅ Customize products
        $customizeProducts = CustomizeProduct::query()
            ->when($type === 'customize', fn($q) => $q) // include only if 'customize' is requested
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($subcategoryId, fn($q) => $q->where('subcategory_id', $subcategoryId))
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'customize',
                    'name' => $item->name,
                    'category_id' => $item->category_id,
                    'subcategory_id' => $item->subcategory_id,
                    'short_description' => $item->short_description,
                    'image' => $item->images[0] ?? null,
                    'source' => 'customize'
                ];
            });

        // ✅ Merge results (based on filter)
        if ($type === 'customize') {
            $all = $customizeProducts;
        } elseif (in_array($type, ['readymade', 'semi_customizable'])) {
            $all = $readyProducts;
        } else {
            $all = $readyProducts->merge($customizeProducts);
        }

        return response()->json([
            'status' => true,
            'message' => 'All products fetched.',
            'data' => $all->values()
        ]);
    }
}
