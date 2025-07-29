<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ready_semi_products;
use App\Models\CustomizeProduct;
use App\Models\Contact;
use App\Models\Service;
use App\Models\Subservice;
use App\Models\InstagramImage;
use App\Models\ArtSection;
use App\Models\GalleryImage;
use App\Models\Artist;
use App\Models\LandingBanner;
use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test admin if not exists
        if (!Admin::where('email', 'admin@example.com')->exists()) {
            Admin::create([
                'name' => 'Test Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
            ]);
        }

        // Create test user if not exists
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password123'),
            ]);
        }

        // Create category and subcategory
        $category = Category::firstOrCreate(['name' => 'Test Category']);
        $subcategory = Subcategory::firstOrCreate([
            'name' => 'Test Subcategory',
            'category_id' => $category->id,
            'image' => null,
        ]);

        // Create ready-made product if not exists
        if (!ready_semi_products::where('name', 'Test Ready Product')->exists()) {
            ready_semi_products::create([
                'name' => 'Test Ready Product',
                'type' => 'readymade',
                'category_id' => $category->id,
                'subcategory_id' => $subcategory->id,
                'short_description' => 'Short description for ready product',
                'long_description' => 'Long description for ready product',
                'images' => ['ready_product_image.jpg'],
                'sizes' => [
                    ['size' => 'M', 'price' => 100, 'stock' => 10],
                ],
                'attributes' => [
                    ['heading' => 'Color', 'type' => 'string', 'value' => 'Red'],
                ],
                'properties' => 'Some properties',
            ]);
        }

        // Create customizable product if not exists
        if (!CustomizeProduct::where('name', 'Test Customizable Product')->exists()) {
            CustomizeProduct::create([
                'name' => 'Test Customizable Product',
                'category_id' => $category->id,
                'subcategory_id' => $subcategory->id,
                'short_description' => 'Short description for customizable product',
                'long_description' => 'Long description for customizable product',
                'images' => ['custom_product_image.jpg'],
                'attributes' => [
                    ['heading' => 'Material', 'type' => 'string', 'value' => 'Cotton'],
                ],
                'properties' => 'Custom properties',
                'expected_price' => 200,
            ]);
        }

        // Create contact if not exists
        if (!Contact::where('email', 'contact@example.com')->exists()) {
            Contact::create([
                'name' => 'Test Contact',
                'email' => 'contact@example.com',
                'phone' => '1234567890',
                'subject' => 'Test Subject',
                'message' => 'Test message content',
            ]);
        }

        // Create service and subservice if not exists
        $service = Service::firstOrCreate([
            'name' => 'Test Service',
            'description' => 'Service description',
            'conclusion' => 'Service conclusion',
            'image' => null,
        ]);
        Subservice::firstOrCreate([
            'service_id' => $service->id,
            'description' => 'Test Subservice description',
            'image' => null,
        ]);

        // Create Instagram image if not exists
        InstagramImage::firstOrCreate([
            'image' => 'instagram/test_image.jpg',
        ]);

        // Create art section if not exists
        ArtSection::firstOrCreate([
            'image' => 'art/test_art.jpg',
            'title' => 'Test Art Title',
            'description' => 'Test art description',
        ]);

        // Create gallery image if not exists
        GalleryImage::firstOrCreate([
            'image' => 'gallery/test_gallery.jpg',
        ]);

        // Create artist if not exists
        Artist::firstOrCreate([
            'image' => 'artists/test_artist.jpg',
            'description' => 'Test artist description',
        ]);

        // Create landing banner if not exists
        LandingBanner::firstOrCreate([
            'category_id' => $category->id,
            'image' => 'landing_banners/test_banner.jpg',
        ]);

        // Create brand if not exists
        Brand::firstOrCreate([
            'description' => 'Test brand description',
            'image1' => 'brands/test_brand1.jpg',
            'image2' => 'brands/test_brand2.jpg',
            'image3' => 'brands/test_brand3.jpg',
        ]);
    }
}
