<?php

namespace App\Repositories\Customer;

use App\Models\Category;
use App\Models\Product;
use App\Models\StorefrontArticle;
use App\Models\StorefrontCollection;
use App\Models\StorefrontRegion;
use App\Models\StorefrontTestimonial;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class HomeRepository
{
    public function getRegions(): Collection
    {
        return StorefrontRegion::where('is_active', true)->orderBy('sort_order')->limit(8)->get();
    }

    public function getCollections(): Collection
    {
        return StorefrontCollection::where('is_active', true)->orderBy('sort_order')->limit(6)->get();
    }

    public function getTestimonials(): Collection
    {
        return StorefrontTestimonial::where('is_active', true)->orderBy('sort_order')->limit(6)->get();
    }

    public function getArticles(): Collection
    {
        return StorefrontArticle::where('is_active', true)->whereNotNull('published_at')->latest('published_at')->limit(6)->get();
    }

    public function visibleProductsQuery()
    {
        return Product::query()
            ->with([
                'category:id,name,slug', 'brand:id,name,slug', 'vendor:id,store_name,store_slug,logo',
                'images' => fn ($query) => $query->select('id', 'product_id', 'product_variant_id', 'image_url', 'alt_text', 'sort_order', 'is_primary')->orderByDesc('is_primary')->orderBy('sort_order'),
                'variants' => fn ($query) => $query->select('id', 'product_id', 'name', 'price', 'sale_price')->where('is_active', true)->orderBy('price'),
            ])
            ->withAvg(['reviews as rating' => fn ($query) => $query->where('is_approved', true)], 'rating')
            ->withCount(['reviews as reviews_count' => fn ($query) => $query->where('is_approved', true)])
            ->where('status', 'active')->whereNotNull('approved_at')
            ->whereHas('vendor', fn ($query) => $query->approved())
            ->whereHas('category', fn ($query) => $query->active())
            ->whereHas('variants', fn ($query) => $query->where('is_active', true))
            ->whereHas('images')
            ->where(fn ($query) => $query->whereNull('brand_id')->orWhereHas('brand', fn ($brand) => $brand->active()));
    }

    public function getFeaturedProducts(string $type, int $limit = 8): Collection
    {
        $query = $this->visibleProductsQuery();

        if ($type === 'best_selling') {
            $query->withSum('orderItems as sold_quantity', 'quantity')->orderByDesc('sold_quantity');
        } elseif ($type === 'trending') {
            $query->orderByDesc('reviews_count')->orderByDesc('rating');
        } else {
            $query->latest('created_at');
        }

        return $query->orderByDesc('id')->limit(min(max($limit, 1), 20))->get();
    }

    /**
     * Get active categories for customer home.
     */
    public function getCategories(int $limit = 12): Collection
    {
        return Category::query()
            ->active()
            ->root()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit($limit)
            ->get([
                'id',
                'parent_id',
                'name',
                'slug',
                'image',
                'sort_order',
            ]);
    }

    /**
     * Get customer-visible products.
     *
     * Only products that are:
     * - active
     * - not deleted
     * - under an active category
     * - associated with an active brand, when brand exists
     * - having at least one active variant
     * - having at least one product image
     */
    public function getProducts(array $filters): LengthAwarePaginator
    {
        $query = $this->visibleProductsQuery();
        /*->with([
            'category:id,name,slug',
            'brand:id,name,slug',
            'images' => function ($query) {
              $query
                ->select([
                  'id',
                  'product_id',
                  'image_url',
                  'alt_text',
                  'sort_order',
                  'is_primary',
                ])
                ->orderByDesc('is_primary')
                ->orderBy('sort_order');
            },
            'variants' => function ($query) {
              $query
                ->select([
                  'id',
                  'product_id',
                  'price',
                  'sale_price',
                ])
                ->where('is_active', true)
                ->orderBy('price');
            },
          ])
          ->where('status', 'active')

          // Product must have an active category.
          ->whereHas('category', function ($query) {
            $query->active();
          })

          // Product must have at least one active variant.
          ->whereHas('variants', function ($query) {
            $query->where('is_active', true);
          })

          // Approved products should have product images.
          ->whereHas('images');*/

        /*
             * Brand is optional.
             * If a product has a brand, that brand must be active.
             */
        /*$query->where(function ($query) {
          $query
            ->whereNull('brand_id')
            ->orWhereHas('brand', function ($brandQuery) {
              $brandQuery->active();
            });
        });*/

        /*
             * Search
             */
        if (! empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%')
                    ->orWhere('short_description', 'like', '%'.$search.'%');
            });
        }

        /*
             * Category filter
             */
        if (! empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        /*
             * Brand filter
             */
        if (! empty($filters['brand_id'])) {
            $query->where('brand_id', (int) $filters['brand_id']);
        }

        /*
             * Sorting
             *
             * Only allow predefined values.
             * Never pass raw request values directly into orderBy.
             */
        $sort = $filters['sort'] ?? 'latest';

        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'price_low':
                $query->orderByRaw(
                    'COALESCE(
                        (
                            SELECT MIN(
                                CASE
                                    WHEN sale_price IS NOT NULL
                                         AND sale_price > 0
                                    THEN sale_price
                                    ELSE price
                                END
                            )
                            FROM product_variants
                            WHERE product_variants.product_id = products.id
                            AND product_variants.is_active = 1
                            AND product_variants.deleted_at IS NULL
                        ),
                        999999999
                    ) ASC'
                );
                break;

            case 'price_high':
                $query->orderByRaw(
                    'COALESCE(
                        (
                            SELECT MAX(
                                CASE
                                    WHEN sale_price IS NOT NULL
                                         AND sale_price > 0
                                    THEN sale_price
                                    ELSE price
                                END
                            )
                            FROM product_variants
                            WHERE product_variants.product_id = products.id
                            AND product_variants.is_active = 1
                            AND product_variants.deleted_at IS NULL
                        ),
                        0
                    ) DESC'
                );
                break;

            case 'latest':
            default:
                $query->latest('created_at');
                break;
        }

        /*
             * Stable secondary ordering prevents products from jumping
             * between pages when timestamps are identical.
             */
        $query->orderByDesc('id');

        $perPage = min(
            max((int) ($filters['per_page'] ?? 12), 1),
            50
        );

        return $query->paginate($perPage);
    }
}
