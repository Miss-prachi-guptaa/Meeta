<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoCatalogSeeder extends Seeder
{
    /**
     * Build a repeatable, frontend-ready artisan catalogue without deleting orders or user data.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $vendor = Vendor::approved()->where('store_slug', 'test-store')->first() ?? Vendor::approved()->firstOrFail();
            $root = Category::updateOrCreate(['slug' => 'handicrafts'], ['name' => 'Handicrafts', 'description' => 'Handmade décor and functional craft from Indian artisans.', 'is_active' => true]);
            $categories = collect([
                'vases' => ['Vases', 'Decorative handcrafted vases in ceramic, wood and glass.'],
                'cushion-covers' => ['Cushion Covers', 'Hand-printed and handwoven soft furnishings.'],
                'diyas-candle-holders' => ['Diyas & Candle Holders', 'Artisan-made festive and ambient lighting accents.'],
                'planters' => ['Planters', 'Handcrafted indoor and balcony planters.'],
                'storage-baskets' => ['Storage Baskets', 'Natural fibre storage handcrafted for modern homes.'],
            ])->mapWithKeys(fn ($data, $slug) => [$slug => Category::updateOrCreate(['slug' => $slug], [
                'parent_id' => $root->id, 'name' => $data[0], 'description' => $data[1], 'is_active' => true,
            ])]);

            $this->cleanExistingAttributeSpellings();
            $optionIds = $this->options();

            foreach ($this->products() as $definition) {
                $product = Product::updateOrCreate(['slug' => $definition['slug']], [
                    'vendor_id' => $vendor->id,
                    'category_id' => $categories[$definition['category']]->id,
                    'brand_id' => null,
                    'name' => $definition['name'],
                    'short_description' => $definition['short_description'],
                    'description' => $definition['description'],
                    'meta_title' => $definition['name'].' | Handcrafted Home Décor',
                    'meta_description' => $definition['short_description'],
                    'status' => 'active',
                    'approved_at' => now(),
                    'rejection_reason' => null,
                ]);

                foreach ($definition['images'] as $position => $image) {
                    ProductImage::withTrashed()->updateOrCreate(
                        ['product_id' => $product->id, 'image_url' => $image],
                        ['product_variant_id' => null, 'alt_text' => $definition['name'], 'sort_order' => $position + 1, 'is_primary' => $position === 0, 'deleted_at' => null]
                    );
                }

                foreach ($definition['variants'] as $position => $variantData) {
                    $variant = ProductVariant::withTrashed()->updateOrCreate(['sku' => $variantData['sku']], [
                        'product_id' => $product->id,
                        'name' => $variantData['name'],
                        'price' => $variantData['price'],
                        'sale_price' => $variantData['sale_price'],
                        'weight' => $variantData['weight'],
                        'length' => $variantData['length'],
                        'width' => $variantData['width'],
                        'height' => $variantData['height'],
                        'is_active' => true,
                        'deleted_at' => null,
                    ]);
                    $variant->attributeValues()->sync(collect($variantData['options'])->map(fn ($value, $attribute) => $optionIds[$attribute][$value])->values());
                    Inventory::updateOrCreate(['product_variant_id' => $variant->id], [
                        'quantity' => $variantData['stock'], 'reserved_quantity' => 0, 'minimum_stock' => 5, 'maximum_stock' => 100,
                    ]);
                }
            }
        });
    }

    private function cleanExistingAttributeSpellings(): void
    {
        Attribute::where('slug', 'colorr')->update(['name' => 'Color', 'slug' => 'color']);
        AttributeValue::where('slug', 'bluee')->update(['value' => 'Blue', 'slug' => 'blue']);
        AttributeValue::where('slug', 'yellow')->update(['value' => 'Yellow']);
        AttributeValue::where('slug', 'black')->update(['value' => 'Black']);
    }

    private function options(): array
    {
        $definitions = [
            'color' => ['Color', ['Cobalt Blue', 'Ivory Blue', 'Indigo']],
            'size' => ['Size', ['Small', 'Medium', 'Large', '16 × 16 in', '18 × 18 in']],
            'set-size' => ['Set Size', ['Set of 2', 'Set of 4']],
            'finish' => ['Finish', ['Antique Brass', 'Polished Brass']],
        ];
        $ids = [];
        foreach ($definitions as $slug => [$name, $values]) {
            $attribute = Attribute::updateOrCreate(['slug' => $slug], ['name' => $name, 'status' => 'active']);
            foreach ($values as $position => $value) {
                $option = AttributeValue::updateOrCreate(['attribute_id' => $attribute->id, 'slug' => Str::slug($value)], [
                    'value' => $value, 'sort_order' => $position + 1, 'status' => 'active',
                ]);
                $ids[$slug][$value] = $option->id;
            }
        }

        return $ids;
    }

    private function products(): array
    {
        return [
            [
                'name' => 'Jaipur Blue Pottery Floral Vase', 'slug' => 'jaipur-blue-pottery-floral-vase', 'category' => 'vases',
                'short_description' => 'Hand-painted glazed ceramic vase inspired by Jaipur blue pottery.',
                'description' => 'A luminous ceramic vase painted by hand with traditional floral motifs. Each piece carries subtle brush variations that make it individual. Display it with dry stems or as a standalone accent. Wipe clean with a soft dry cloth.',
                'images' => ['/images/catalog/jaipur-blue-pottery-vase-1.webp', '/images/catalog/jaipur-blue-pottery-vase-2.webp'],
                'variants' => [
                    $this->variant('JBP-CB-S', 'Cobalt Blue / Small', 1299, 1099, .85, 16, 16, 24, 24, ['color' => 'Cobalt Blue', 'size' => 'Small']),
                    $this->variant('JBP-CB-L', 'Cobalt Blue / Large', 1899, 1599, 1.35, 22, 22, 34, 18, ['color' => 'Cobalt Blue', 'size' => 'Large']),
                    $this->variant('JBP-IB-S', 'Ivory Blue / Small', 1399, 1199, .85, 16, 16, 24, 16, ['color' => 'Ivory Blue', 'size' => 'Small']),
                    $this->variant('JBP-IB-L', 'Ivory Blue / Large', 1999, 1699, 1.35, 22, 22, 34, 12, ['color' => 'Ivory Blue', 'size' => 'Large']),
                ],
            ],
            [
                'name' => 'Indigo Block Print Cushion Covers', 'slug' => 'indigo-block-print-cushion-covers', 'category' => 'cushion-covers',
                'short_description' => 'Natural cotton cushion covers hand block printed in deep indigo.',
                'description' => 'Printed one block at a time by artisan hands, these cotton cushion covers bring rhythmic botanical pattern to a sofa or reading corner. Concealed zip closure. Cold hand wash separately; expect the gentle irregularities of authentic hand printing.',
                'images' => ['/images/catalog/indigo-block-print-cushion-1.webp', '/images/catalog/indigo-block-print-cushion-2.webp'],
                'variants' => [
                    $this->variant('IBC-16-2', 'Indigo / 16 × 16 in / Set of 2', 1199, 999, .45, 22, 18, 4, 30, ['color' => 'Indigo', 'size' => '16 × 16 in', 'set-size' => 'Set of 2']),
                    $this->variant('IBC-16-4', 'Indigo / 16 × 16 in / Set of 4', 2199, 1899, .90, 30, 22, 6, 18, ['color' => 'Indigo', 'size' => '16 × 16 in', 'set-size' => 'Set of 4']),
                    $this->variant('IBC-18-2', 'Indigo / 18 × 18 in / Set of 2', 1399, 1199, .55, 24, 20, 4, 24, ['color' => 'Indigo', 'size' => '18 × 18 in', 'set-size' => 'Set of 2']),
                    $this->variant('IBC-18-4', 'Indigo / 18 × 18 in / Set of 4', 2499, 2199, 1.10, 32, 24, 7, 14, ['color' => 'Indigo', 'size' => '18 × 18 in', 'set-size' => 'Set of 4']),
                ],
            ],
            [
                'name' => 'Handcrafted Brass Lotus Diya Set', 'slug' => 'handcrafted-brass-lotus-diya-set', 'category' => 'diyas-candle-holders',
                'short_description' => 'Engraved lotus diyas cast in solid brass for festive and everyday rituals.',
                'description' => 'Petal by petal, this lotus diya is finished by hand in solid brass. Use it for festive décor, a quiet prayer corner or as a thoughtful housewarming gift. Clean with a soft dry cloth; brass develops a natural patina over time.',
                'images' => ['/images/catalog/brass-lotus-diya-1.webp', '/images/catalog/brass-lotus-diya-2.webp'],
                'variants' => [
                    $this->variant('BLD-A-2', 'Antique Brass / Set of 2', 1599, 1399, .70, 18, 18, 8, 28, ['finish' => 'Antique Brass', 'set-size' => 'Set of 2']),
                    $this->variant('BLD-A-4', 'Antique Brass / Set of 4', 2999, 2599, 1.40, 28, 20, 10, 15, ['finish' => 'Antique Brass', 'set-size' => 'Set of 4']),
                    $this->variant('BLD-P-2', 'Polished Brass / Set of 2', 1699, 1499, .70, 18, 18, 8, 20, ['finish' => 'Polished Brass', 'set-size' => 'Set of 2']),
                    $this->variant('BLD-P-4', 'Polished Brass / Set of 4', 3199, 2799, 1.40, 28, 20, 10, 10, ['finish' => 'Polished Brass', 'set-size' => 'Set of 4']),
                ],
            ],
            [
                'name' => 'Warli Hand-Painted Terracotta Planter', 'slug' => 'warli-hand-painted-terracotta-planter', 'category' => 'planters',
                'short_description' => 'Earthy terracotta planter painted by hand with a white Warli narrative.',
                'description' => 'Wheel-shaped in natural terracotta and detailed with a hand-painted Warli scene, this planter celebrates earth and community. A drainage opening supports healthy roots. Best kept in a sheltered balcony or indoors.',
                'images' => ['/images/catalog/warli-terracotta-planter-1.webp', '/images/catalog/warli-terracotta-planter-2.webp'],
                'variants' => [
                    $this->variant('WTP-M', 'Medium', 999, 849, 1.25, 22, 22, 20, 25, ['size' => 'Medium']),
                    $this->variant('WTP-L', 'Large', 1499, 1299, 2.10, 30, 30, 27, 16, ['size' => 'Large']),
                ],
            ],
            [
                'name' => 'Handwoven Cane Storage Basket with Lid', 'slug' => 'handwoven-cane-storage-basket-with-lid', 'category' => 'storage-baskets',
                'short_description' => 'Lidded natural cane basket woven by hand for beautiful everyday storage.',
                'description' => 'A lightweight but sturdy round storage basket woven from natural cane. The fitted lid keeps everyday clutter composed while side handles make it easy to move. Variations in fibre colour and weave are part of its handmade character.',
                'images' => ['/images/catalog/cane-storage-basket-1.webp', '/images/catalog/cane-storage-basket-2.webp'],
                'variants' => [
                    $this->variant('CSB-S', 'Small', 1299, 1099, .65, 28, 28, 22, 22, ['size' => 'Small']),
                    $this->variant('CSB-M', 'Medium', 1799, 1499, .95, 36, 36, 28, 18, ['size' => 'Medium']),
                    $this->variant('CSB-L', 'Large', 2499, 2199, 1.35, 44, 44, 34, 12, ['size' => 'Large']),
                ],
            ],
        ];
    }

    private function variant(string $sku, string $name, float $price, float $salePrice, float $weight, float $length, float $width, float $height, int $stock, array $options): array
    {
        return compact('sku', 'name', 'price', 'salePrice', 'weight', 'length', 'width', 'height', 'stock', 'options') + ['sale_price' => $salePrice];
    }
}
