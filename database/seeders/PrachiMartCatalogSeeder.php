<?php

namespace Database\Seeders;

use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrachiMartCatalogSeeder extends Seeder
{
    /**
     * Seed a small repeatable catalogue for the approved Piku/Prachi Mart vendor.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $vendor = Vendor::approved()
                ->whereHas('user', fn ($query) => $query->whereRaw('LOWER(email) = ?', ['piku@gmail.com']))
                ->firstOrFail();

            foreach ($this->products() as $definition) {
                $category = Category::where('slug', $definition['category'])->where('is_active', true)->firstOrFail();
                $product = Product::updateOrCreate(['slug' => $definition['slug']], [
                    'vendor_id' => $vendor->id,
                    'category_id' => $category->id,
                    'brand_id' => null,
                    'name' => $definition['name'],
                    'short_description' => $definition['short_description'],
                    'description' => $definition['description'],
                    'meta_title' => $definition['name'].' | Prachi Mart',
                    'meta_description' => $definition['short_description'],
                    'status' => 'active',
                    'approved_at' => now(),
                    'rejection_reason' => null,
                ]);

                foreach ($definition['images'] as $position => $image) {
                    ProductImage::updateOrCreate(
                        ['product_id' => $product->id, 'image_url' => $image],
                        ['alt_text' => $definition['name'], 'sort_order' => $position + 1, 'is_primary' => $position === 0]
                    );
                }

                foreach ($definition['variants'] as $variantData) {
                    $variant = ProductVariant::updateOrCreate(['sku' => $variantData['sku']], [
                        'product_id' => $product->id,
                        'name' => $variantData['name'],
                        'price' => $variantData['price'],
                        'sale_price' => $variantData['sale_price'],
                        'weight' => $variantData['weight'],
                        'length' => $variantData['length'],
                        'width' => $variantData['width'],
                        'height' => $variantData['height'],
                        'is_active' => true,
                    ]);
                    $valueIds = collect($variantData['options'])->map(function ($value, $attributeSlug) {
                        return AttributeValue::where('slug', $value)
                            ->whereHas('attribute', fn ($query) => $query->where('slug', $attributeSlug))
                            ->firstOrFail()
                            ->id;
                    });
                    $variant->attributeValues()->sync($valueIds);
                    Inventory::updateOrCreate(['product_variant_id' => $variant->id], [
                        'quantity' => $variantData['stock'], 'reserved_quantity' => 0,
                        'minimum_stock' => 4, 'maximum_stock' => 60,
                    ]);
                }
            }
        });
    }

    private function products(): array
    {
        return [
            [
                'name' => 'Prachi Blue Pottery Bud Vase', 'slug' => 'prachi-blue-pottery-bud-vase', 'category' => 'vases',
                'short_description' => 'A compact hand-painted Jaipur blue pottery vase for fresh or dried stems.',
                'description' => 'Painted by hand with cobalt floral motifs and finished in a luminous glaze, this compact ceramic vase brings traditional Jaipur craft to shelves, desks and bedside tables. Subtle brush variations make every piece individual.',
                'images' => ['/images/catalog/jaipur-blue-pottery-vase-1.webp', '/images/catalog/jaipur-blue-pottery-vase-2.webp'],
                'variants' => [
                    $this->variant('PM-BPV-CB-S', 'Cobalt Blue / Small', 1199, 999, .75, 15, 15, 22, 20, ['color' => 'cobalt-blue', 'size' => 'small']),
                    $this->variant('PM-BPV-CB-M', 'Cobalt Blue / Medium', 1599, 1399, 1.10, 19, 19, 29, 14, ['color' => 'cobalt-blue', 'size' => 'medium']),
                ],
            ],
            [
                'name' => 'Prachi Antique Brass Lotus Tealight Set', 'slug' => 'prachi-antique-brass-lotus-tealight-set', 'category' => 'diyas-candle-holders',
                'short_description' => 'Hand-finished solid brass lotus holders for festive tables and prayer corners.',
                'description' => 'Layered lotus petals are cast and finished by artisan hands in warm antique brass. Use the holders with tealights or traditional cotton wicks. Brass naturally develops a graceful patina over time.',
                'images' => ['/images/catalog/brass-lotus-diya-1.webp', '/images/catalog/brass-lotus-diya-2.webp'],
                'variants' => [
                    $this->variant('PM-BLT-A-2', 'Antique Brass / Set of 2', 1499, 1299, .65, 17, 17, 7, 24, ['finish' => 'antique-brass', 'set-size' => 'set-of-2']),
                    $this->variant('PM-BLT-A-4', 'Antique Brass / Set of 4', 2799, 2399, 1.30, 27, 20, 9, 12, ['finish' => 'antique-brass', 'set-size' => 'set-of-4']),
                ],
            ],
            [
                'name' => 'Prachi Handwoven Cane Desk Organizer', 'slug' => 'prachi-handwoven-cane-desk-organizer', 'category' => 'storage-baskets',
                'short_description' => 'Natural cane storage woven by hand to organize stationery and everyday essentials.',
                'description' => 'A lightweight, sturdy organizer woven from natural cane by skilled makers. Its warm texture keeps a work desk, entry console or dressing table composed while celebrating the small irregularities of genuine handwork.',
                'images' => ['/images/catalog/cane-storage-basket-1.webp', '/images/catalog/cane-storage-basket-2.webp'],
                'variants' => [
                    $this->variant('PM-CDO-S', 'Small', 899, 749, .45, 24, 18, 12, 28, ['size' => 'small']),
                    $this->variant('PM-CDO-M', 'Medium', 1299, 1099, .70, 32, 23, 15, 18, ['size' => 'medium']),
                ],
            ],
        ];
    }

    private function variant(string $sku, string $name, float $price, float $salePrice, float $weight, float $length, float $width, float $height, int $stock, array $options): array
    {
        return compact('sku', 'name', 'price', 'weight', 'length', 'width', 'height', 'stock', 'options') + ['sale_price' => $salePrice];
    }
}
