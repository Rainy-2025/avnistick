<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InstagramImage;
use App\Models\ArtSection;
use App\Models\GalleryImage;
use App\Models\Artist;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    // ✅ Upload Instagram Images (max 5 total)
    public function uploadInstagramImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $currentCount = InstagramImage::count();
        $newImagesCount = count($request->file('images'));

        if (($currentCount + $newImagesCount) > 5) {
            return response()->json([
                'status' => false,
                'message' => 'You can only have up to 5 Instagram images. ' .
                    (5 - $currentCount) . ' more image(s) allowed.'
            ], 403);
        }

        foreach ($request->file('images') as $image) {
            $path = $image->store('instagram', 'public');

            InstagramImage::create([
                'image' => $path
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Instagram images uploaded successfully.'
        ], 200);
    }

    // ✅ Get Instagram Images (with full URL)
    public function getInstagramImages()
    {
        $images = InstagramImage::all()->map(function ($item) {
            $item->image_url = asset('storage/' . $item->image);
            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'Instagram images fetched successfully.',
            'data' => $images
        ], 200);
    }

    // ✅ Delete Instagram Image
    public function deleteInstagramImage($id)
    {
        $image = InstagramImage::find($id);
        if (!$image) {
            return response()->json(['status' => false, 'message' => 'Image not found.'], 404);
        }

        Storage::disk('public')->delete($image->image);
        $image->delete();

        return response()->json(['status' => true, 'message' => 'Instagram image deleted successfully.']);
    }

    // ✅ Upload Art Section
    public function uploadArtSection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $path = $request->file('image')->store('art', 'public');

        ArtSection::create([
            'image' => $path,
            'title' => $request->title,
            'description' => $request->description
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Art section uploaded successfully.'
        ], 200);
    }

    // ✅ Get Art Section
    public function getArtSection()
    {
        $arts = ArtSection::all()->map(function ($item) {
            $item->image_url = asset('storage/' . $item->image);
            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'Art section data fetched successfully.',
            'data' => $arts
        ], 200);
    }

    // ✅ Delete Art Section
    public function deleteArtSection($id)
    {
        $art = ArtSection::find($id);
        if (!$art) {
            return response()->json(['status' => false, 'message' => 'Art not found.'], 404);
        }

        Storage::disk('public')->delete($art->image);
        $art->delete();

        return response()->json(['status' => true, 'message' => 'Art section deleted successfully.']);
    }

    // ✅ Upload Gallery Images
    public function uploadGalleryImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->file('images') as $image) {
            $path = $image->store('gallery', 'public');
            GalleryImage::create(['image' => $path]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Gallery images uploaded successfully.'
        ], 200);
    }

    // ✅ Get Gallery Images
    public function getGalleryImages()
    {
        $gallery = GalleryImage::all()->map(function ($item) {
            $item->image_url = asset('storage/' . $item->image);
            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'Gallery images fetched successfully.',
            'data' => $gallery
        ], 200);
    }

    // ✅ Delete Gallery Image
    public function deleteGalleryImage($id)
    {
        $image = GalleryImage::find($id);
        if (!$image) {
            return response()->json(['status' => false, 'message' => 'Image not found.'], 404);
        }

        Storage::disk('public')->delete($image->image);
        $image->delete();

        return response()->json(['status' => true, 'message' => 'Gallery image deleted successfully.']);
    }

    // ✅ Upload Artist (max 5)
    public function uploadArtist(Request $request)
    {
        if (Artist::count() >= 5) {
            return response()->json([
                'status' => false,
                'message' => 'Maximum of 5 artist records allowed.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $path = $request->file('image')->store('artists', 'public');

        Artist::create([
            'image' => $path,
            'description' => $request->description
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Artist uploaded successfully.'
        ], 200);
    }

    // ✅ Get Artists
    public function getArtists()
    {
        $artists = Artist::all()->map(function ($item) {
            $item->image_url = asset('storage/' . $item->image);
            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'Artists fetched successfully.',
            'data' => $artists
        ], 200);
    }

    // ✅ Delete Artist
    public function deleteArtist($id)
    {
        $artist = Artist::find($id);
        if (!$artist) {
            return response()->json(['status' => false, 'message' => 'Artist not found.'], 404);
        }

        Storage::disk('public')->delete($artist->image);
        $artist->delete();

        return response()->json(['status' => true, 'message' => 'Artist deleted successfully.']);
    }
}
