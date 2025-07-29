<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LandingBanner;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;

class LandingPageController extends Controller
{
    // 🔹 Add Banner
    public function addBanner(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $path = $request->file('image')->store('landing_banners', 'public');

        $banner = LandingBanner::create([
            'category_id' => $request->category_id,
            'image' => $path,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Banner added successfully',
            'data' => $banner
        ]);
    }

    // 🔹 Get All Banners
    public function getBanners()
    {
        $banners = LandingBanner::with('category:id,name')->get()->map(function ($b) {
            $b->image = asset('storage/' . $b->image);
            return $b;
        });

        return response()->json([
            'status' => true,
            'message' => 'Banners fetched successfully',
            'data' => $banners
        ]);
    }

    // 🔹 Update Banner
    public function updateBanner(Request $request, $id)
    {
        $banner = LandingBanner::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $banner->category_id = $request->category_id;

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image);
            $banner->image = $request->file('image')->store('landing_banners', 'public');
        }

        $banner->save();

        return response()->json([
            'status' => true,
            'message' => 'Banner updated successfully',
            'data' => $banner
        ]);
    }

    // 🔹 Delete Banner
    public function deleteBanner($id)
    {
        $banner = LandingBanner::findOrFail($id);
        Storage::disk('public')->delete($banner->image);
        $banner->delete();

        return response()->json([
            'status' => true,
            'message' => 'Banner deleted successfully',
            'data' => null
        ]);
    }

    // 🔸 Add Brand
    public function addBrand(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'image1' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $data = ['description' => $request->description];

        foreach (['image1', 'image2', 'image3'] as $img) {
            if ($request->hasFile($img)) {
                $data[$img] = $request->file($img)->store('brands', 'public');
            }
        }

        $brand = Brand::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Brand added successfully',
            'data' => $brand
        ]);
    }

    // 🔸 Get Brand (Latest Only)
    public function getBrand()
    {
        $brand = Brand::latest()->first();

        if ($brand) {
            foreach (['image1', 'image2', 'image3'] as $img) {
                if ($brand->$img) {
                    $brand->$img = asset('storage/' . $brand->$img);
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Brand fetched successfully',
            'data' => $brand
        ]);
    }

    // 🔸 Update Brand
    public function updateBrand(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);

        $request->validate([
            'description' => 'required|string',
            'image1' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $brand->description = $request->description;

        foreach (['image1', 'image2', 'image3'] as $img) {
            if ($request->hasFile($img)) {
                if ($brand->$img) {
                    Storage::disk('public')->delete($brand->$img);
                }
                $brand->$img = $request->file($img)->store('brands', 'public');
            }
        }

        $brand->save();

        return response()->json([
            'status' => true,
            'message' => 'Brand updated successfully',
            'data' => $brand
        ]);
    }
}

