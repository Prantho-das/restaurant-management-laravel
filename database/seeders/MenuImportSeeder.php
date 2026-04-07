<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Outlet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonFile = base_path('menu.json');

        if (! File::exists($jsonFile)) {
            $this->command->error('Menu JSON file not found at '.$jsonFile);

            return;
        }

        $data = json_decode(File::get($jsonFile), true);
        $menu = $data['restaurant_menu'] ?? [];

        $outlet = Outlet::firstOrCreate(
            ['name' => 'Main Branch'],
            [
                'address' => 'Main Branch',
                'phone' => '',
                'is_active' => true,
            ]
        );

        foreach ($menu as $categoryKey => $items) {
            $categoryName = Str::title(str_replace('_', ' ', $categoryKey));

            $parentCategory = Category::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'is_active' => true, 'priority_order' => 1]
            );

            if (is_array($items) && isset($items[0])) {
                // Flat structure
                $this->importItems($items, $parentCategory, $outlet);
            } else {
                // Subcategories (e.g. lunch_and_dinner_items)
                foreach ($items as $subCategoryKey => $subItems) {
                    $subCategoryName = Str::title(str_replace('_', ' ', $subCategoryKey));
                    $subCategory = Category::updateOrCreate(
                        ['slug' => Str::slug($subCategoryName), 'parent_id' => $parentCategory->id],
                        ['name' => $subCategoryName, 'is_active' => true, 'priority_order' => 1]
                    );
                    $this->importItems($subItems, $subCategory, $outlet);
                }
            }
        }
    }

    private function importItems(array $items, Category $category, Outlet $outlet): void
    {
        foreach ($items as $item) {
            $name_bn = $item['item_bn'] ?? 'Unnamed Item';
            $name_en = $item['item_en'] ?? '';
            $priceTk = $item['price_tk'] ?? 0;

            // Check if price is simple numeric
            $basePrice = 0.00;
            if (is_numeric($priceTk)) {
                $basePrice = (float) $priceTk;
            } else {
                $basePrice = 0.00;
            }

            // Keep English slug for better URL stability
            $slug = Str::slug($name_en).'-'.Str::random(4);
            $sku = 'M-'.strtoupper(Str::random(6));

            $imagePath = null;
            if (! empty($item['image_url'])) {
                $imagePath = $this->downloadImage($item['image_url'], $slug);
            }

            MenuItem::updateOrCreate(
                ['name' => $name_bn], // Use name as unique identifier for this update
                [
                    'category_id' => $category->id,
                    'outlet_id' => $outlet->id,
                    'name' => $name_bn,
                    'slug' => $slug,
                    'description' => $name_en,
                    'base_price' => $basePrice,
                    'is_active' => true,
                    'sku' => $sku,
                    'image' => $imagePath,
                    'tax_rate' => 0,
                ]
            );
        }
    }

    private function downloadImage(string $url, string $slug): ?string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                $extension = 'jpg';
                $filename = 'menu-items/'.$slug.'.'.$extension;
                Storage::disk('public')->put($filename, $response->body());

                return $filename;
            }
        } catch (\Exception $e) {
            // Silently fail and fallback
        }

        return null;
    }
}
