<?php

namespace App\Services\Customer;

use App\Repositories\Customer\HomeRepository;

class HomeService
{
    public function __construct(
        protected HomeRepository $homeRepository
    ) {}

    /**
     * Get all data required for customer home.
     */
    public function getHomeData(array $filters): array
    {
        return [
            'categories' => $this->homeRepository->getCategories(),
            'best_selling' => $this->homeRepository->getFeaturedProducts('best_selling'),
            'trending' => $this->homeRepository->getFeaturedProducts('trending'),
            'new_arrivals' => $this->homeRepository->getFeaturedProducts('latest'),
            'regions' => $this->homeRepository->getRegions(),
            'collections' => $this->homeRepository->getCollections(),
            'testimonials' => $this->homeRepository->getTestimonials(),
            'articles' => $this->homeRepository->getArticles(),
            'products' => $this->homeRepository->getProducts($filters),
        ];
    }
}
