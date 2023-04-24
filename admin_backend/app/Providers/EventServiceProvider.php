<?php

namespace App\Providers;

use App\Events\Mails\EmailSendByTemplate;
use App\Events\Mails\SendEmailVerification;
use App\Listeners\Mails\EmailSendByTemplateListener;
use App\Listeners\Mails\SendEmailVerificationListener;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserCart;
use App\Observers\BrandObserver;
use App\Observers\CategoryObserver;
use App\Observers\GalleryObserver;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use App\Observers\ShopObserver;
use App\Observers\StockObserver;
use App\Observers\TicketObserver;
use App\Observers\UserCartObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SendEmailVerification::class => [
            SendEmailVerificationListener::class,
        ],
        EmailSendByTemplate::class => [
            EmailSendByTemplateListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        Category::observe(CategoryObserver::class);
        Shop::observe(ShopObserver::class);
        Product::observe(ProductObserver::class);
        User::observe(UserObserver::class);
        Brand::observe(BrandObserver::class);
        Ticket::observe(TicketObserver::class);
        Gallery::observe(GalleryObserver::class);
        Stock::observe(StockObserver::class);
        Order::observe(OrderObserver::class);

        // Cards
        UserCart::observe(UserCartObserver::class);

    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
