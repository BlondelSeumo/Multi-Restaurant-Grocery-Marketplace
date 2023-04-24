<?php

namespace App\Providers;

use App\Repositories\CategoryRepository\CategoryRepository;
use App\Repositories\Interfaces\CategoryRepoInterface;
use App\Repositories\Interfaces\OrderRepoInterface;
use App\Repositories\Interfaces\ProductRepoInterface;
use App\Repositories\Interfaces\ShopRepoInterface;
use App\Repositories\Interfaces\UserRepoInterface;
use App\Repositories\OrderRepository\OrderRepository;
use App\Repositories\ProductRepository\ProductRepository;
use App\Repositories\ShopRepository\ShopRepository;
use App\Repositories\UserRepository\UserRepository;
use App\Services\BrandService\BrandService;
use App\Services\CategoryServices\CategoryService;
use App\Services\CurrencyServices\CurrencyService;
use App\Services\Interfaces\BrandServiceInterface;
use App\Services\Interfaces\CategoryServiceInterface;
use App\Services\Interfaces\CurrencyServiceInterface;
use App\Services\Interfaces\LanguageServiceInterface;
use App\Services\Interfaces\OrderServiceInterface;
use App\Services\Interfaces\ProductServiceInterface;
use App\Services\Interfaces\ShopServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Services\LanguageServices\LanguageService;
use App\Services\OrderService\OrderService;
use App\Services\ProductService\ProductService;
use App\Services\ShopServices\ShopService;
use App\Services\UserServices\UserService;
use Illuminate\Support\ServiceProvider;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Repositories biding
        $this->app->bind(UserRepoInterface::class, UserRepository::class);
        $this->app->bind(CategoryRepoInterface::class, CategoryRepository::class);
        $this->app->bind(ShopRepoInterface::class, ShopRepository::class);
        $this->app->bind(ProductRepoInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepoInterface::class, OrderRepository::class);

        // Services biding
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(LanguageServiceInterface::class, LanguageService::class);
        $this->app->bind(CurrencyServiceInterface::class, CurrencyService::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(BrandServiceInterface::class, BrandService::class);
        $this->app->bind(ShopServiceInterface::class, ShopService::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
        $this->app->bind(OrderServiceInterface::class, OrderService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
