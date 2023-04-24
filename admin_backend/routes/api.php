<?php

use App\Http\Controllers\API\v1\{GalleryController, Rest};
use App\Http\Controllers\API\v1\Auth\{LoginController, RegisterController, VerifyAuthController};
use App\Http\Controllers\API\v1\Dashboard\{Admin, Deliveryman, Payment, Seller, User};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix' => 'v1', 'middleware' => ['block.ip']], function () {
    // Methods without AuthCheck
    Route::post('/auth/register',                       [RegisterController::class, 'register'])
        ->middleware('sessions');

    Route::post('/auth/login',                          [LoginController::class, 'login'])
        ->middleware('sessions');

    Route::post('/auth/logout',                         [LoginController::class, 'logout'])
        ->middleware('sessions');

    Route::post('/auth/verify/phone',                   [VerifyAuthController::class, 'verifyPhone'])
        ->middleware('sessions');

    Route::post('/auth/resend-verify',                  [VerifyAuthController::class, 'resendVerify'])
        ->middleware('sessions');

    Route::get('/auth/verify/{hash}',                   [VerifyAuthController::class, 'verifyEmail'])
        ->middleware('sessions');

    Route::post('/auth/after-verify',                   [VerifyAuthController::class, 'afterVerifyEmail'])
        ->middleware('sessions');

    Route::post('/auth/forgot/password',                [LoginController::class, 'forgetPassword'])
        ->middleware('sessions');

    Route::post('/auth/forgot/password/confirm',        [LoginController::class, 'forgetPasswordVerify'])
        ->middleware('sessions');

    Route::post('/auth/forgot/email-password',          [LoginController::class, 'forgetPasswordEmail'])
        ->middleware('sessions');

    Route::post('/auth/forgot/email-password',          [LoginController::class, 'forgetPasswordEmail'])
        ->middleware('sessions');

    Route::post('/auth/forgot/email-password/{hash}',   [LoginController::class, 'forgetPasswordVerifyEmail'])
        ->middleware('sessions');

//    Route::get('/login/{provider}',                 [LoginController::class,'redirectToProvider']);
    Route::post('/auth/{provider}/callback',        [LoginController::class, 'handleProviderCallback']);

    Route::group(['prefix' => 'install'], function () {
        Route::get('/init/check',                   [Rest\InstallController::class, 'checkInitFile']);
        Route::post('/init/set',                    [Rest\InstallController::class, 'setInitFile']);
        Route::post('/database/update',             [Rest\InstallController::class, 'setDatabase']);
        Route::post('/admin/create',                [Rest\InstallController::class, 'createAdmin']);
        Route::post('/migration/run',               [Rest\InstallController::class, 'migrationRun']);
        Route::post('/check/licence',               [Rest\InstallController::class, 'licenceCredentials']);
        Route::post('/currency/create',             [Rest\InstallController::class, 'createCurrency']);
        Route::post('/languages/create',            [Rest\InstallController::class, 'createLanguage']);
    });

    Route::group(['prefix' => 'rest'], function () {

        /* Languages */
        Route::get('bosya/test',                    [Rest\TestController::class, 'bosyaTest']);
        Route::get('project/version',               [Rest\SettingController::class, 'projectVersion']);
        Route::get('timezone',                      [Rest\SettingController::class, 'timeZone']);
        Route::get('translations/paginate',         [Rest\SettingController::class, 'translationsPaginate']);
        Route::get('settings',                      [Rest\SettingController::class, 'settingsInfo']);
        Route::get('referral',                      [Rest\SettingController::class, 'referral']);
        Route::get('system/information',            [Rest\SettingController::class, 'systemInformation']);

        /* Languages */
        Route::get('languages/default',             [Rest\LanguageController::class, 'default']);
        Route::get('languages/active',              [Rest\LanguageController::class, 'active']);
        Route::get('languages/{id}',                [Rest\LanguageController::class, 'show']);
        Route::get('languages',                     [Rest\LanguageController::class, 'index']);

        /* Currencies */
        Route::get('currencies',                    [Rest\CurrencyController::class, 'index']);
        Route::get('currencies/active',             [Rest\CurrencyController::class, 'active']);

        /* CouponCheck */
        Route::post('coupons/check',                [Rest\CouponController::class, 'check']);
        Route::post('cashback/check',               [Rest\ProductController::class, 'checkCashback']);

        /* Products */
        Route::post('products/review/{uuid}',       [Rest\ProductController::class, 'addProductReview']);
        Route::get('products/reviews/{uuid}',       [Rest\ProductController::class, 'reviews']);
        Route::get('order/products/calculate',      [Rest\ProductController::class, 'orderStocksCalculate']);
        Route::get('products/paginate',             [Rest\ProductController::class, 'paginate']);
        Route::get('products/brand/{id}',           [Rest\ProductController::class, 'productsByBrand']);
        Route::get('products/shop/{uuid}',          [Rest\ProductController::class, 'productsByShopUuid']);
        Route::get('products/category/{uuid}',      [Rest\ProductController::class, 'productsByCategoryUuid']);
        Route::get('products/search',               [Rest\ProductController::class, 'productsSearch']);
        Route::get('products/most-sold',            [Rest\ProductController::class, 'mostSoldProducts']);
        Route::get('products/discount',             [Rest\ProductController::class, 'discountProducts']);
        Route::get('products/ids',                  [Rest\ProductController::class, 'productsByIDs']);
        Route::get('products/{uuid}',               [Rest\ProductController::class, 'show']);

        /* Categories */
        Route::get('categories/types',              [Rest\CategoryController::class, 'types']);
        Route::get('categories/parent',             [Rest\CategoryController::class, 'parentCategory']);
        Route::get('categories/children/{id}',      [Rest\CategoryController::class, 'childrenCategory']);
        Route::get('categories/paginate',           [Rest\CategoryController::class, 'paginate']);
        Route::get('categories/select-paginate',    [Rest\CategoryController::class, 'selectPaginate']);
        Route::get('categories/product/paginate',   [Rest\CategoryController::class, 'shopCategoryProduct']);
        Route::get('categories/shop/paginate',      [Rest\CategoryController::class, 'shopCategory']);
        Route::get('categories/search',             [Rest\CategoryController::class, 'categoriesSearch']);
        Route::get('categories/{uuid}',             [Rest\CategoryController::class, 'show']);

        /* Brands */
        Route::get('brands/paginate',               [Rest\BrandController::class, 'paginate']);
        Route::get('brands/{id}',                   [Rest\BrandController::class, 'show']);

        /* Shops */
        Route::get('shops/recommended',             [Rest\ShopController::class, 'recommended']);
        Route::get('shops/paginate',                [Rest\ShopController::class, 'paginate']);
        Route::get('shops/select-paginate',         [Rest\ShopController::class, 'selectPaginate']);
        Route::get('shops/search',                  [Rest\ShopController::class, 'shopsSearch']);
        Route::get('shops/{uuid}',                  [Rest\ShopController::class, 'show']);
        Route::get('shops',                         [Rest\ShopController::class, 'shopsByIDs']);
        Route::get('shops-takes',                   [Rest\ShopController::class, 'takes']);
        Route::get('products-avg-prices',           [Rest\ShopController::class, 'productsAvgPrices']);
        Route::get('shops/{id}/categories',         [Rest\ShopController::class, 'categories'])->where('id', '[0-9]+');
        Route::get('shops/{id}/products',           [Rest\ShopController::class, 'products'])->where('id', '[0-9]+');
        Route::get('shops/{id}/galleries',          [Rest\ShopController::class, 'galleries'])->where('id', '[0-9]+');
        Route::get('shops/{id}/reviews',            [Rest\ShopController::class, 'reviews'])->where('id', '[0-9]+');
        Route::get('shops/{id}/reviews-group-rating', [Rest\ShopController::class, 'reviewsGroupByRating'])->where('id', '[0-9]+');

        Route::get('shops/{id}/products/paginate',  [Rest\ShopController::class, 'productsPaginate'])->where('id', '[0-9]+');
        Route::get('shops/{id}/products/recommended/paginate',  [
            Rest\ShopController::class,
            'productsRecommendedPaginate'
        ])->where('id', '[0-9]+');

        /* Banners */
        Route::get('banners/paginate',              [Rest\BannerController::class, 'paginate']);

        Route::post('banners/{id}/liked',           [Rest\BannerController::class, 'likedBanner'])
            ->middleware('sanctum.check');

        Route::get('banners/{id}',                  [Rest\BannerController::class, 'show']);

        /* FAQS */
        Route::get('faqs/paginate',                 [Rest\FAQController::class, 'paginate']);

        /* Payments */
        Route::get('payments',                      [Rest\PaymentController::class, 'index']);
        Route::get('payments/{id}',                 [Rest\PaymentController::class, 'show']);

        /* Blogs */
        Route::get('blogs/paginate',                [Rest\BlogController::class, 'paginate']);
        Route::get('blogs/{uuid}',                  [Rest\BlogController::class, 'show']);

        Route::get('term',                          [Rest\FAQController::class, 'term']);

        Route::get('policy',                        [Rest\FAQController::class, 'policy']);

        /* Carts */
        Route::post('cart',                         [Rest\CartController::class, 'store']);
        Route::get('cart/{id}',                     [Rest\CartController::class, 'get']);
        Route::post('cart/insert-product',          [Rest\CartController::class, 'insertProducts']);
        Route::post('cart/open',                    [Rest\CartController::class, 'openCart']);
        Route::delete('cart/product/delete',        [Rest\CartController::class, 'cartProductDelete']);
        Route::delete('cart/member/delete',         [Rest\CartController::class, 'userCartDelete']);
        Route::post('cart/status/{user_cart_uuid}', [Rest\CartController::class, 'statusChange']);

        /* Stories */
        Route::get('stories/paginate',              [Rest\StoryController::class, 'paginate']);

        /* Receipts */
        Route::get('receipts/paginate',             [Rest\ReceiptController::class, 'paginate']);
        Route::get('receipts/{id}',                 [Rest\ReceiptController::class, 'show']);


        /* Order Statuses */
        Route::get('order-statuses',                [Rest\OrderStatusController::class, 'index']);
        Route::get('order-statuses/select',         [Rest\OrderStatusController::class, 'select']);

        /* Tags */
        Route::get('tags/paginate',                 [Rest\TagController::class, 'paginate']);

        Route::get('shop/delivery-zone/{shopId}',   [Rest\DeliveryZoneController::class, 'getByShopId']);
        Route::get('shop/delivery-zone/calculate/price/{id}', [
            Rest\DeliveryZoneController::class,
            'deliveryCalculatePrice'
        ]);
        Route::get('shop/delivery-zone/calculate/distance',  [Rest\DeliveryZoneController::class, 'distance']);
        Route::get('shop/delivery-zone/check/distance',      [Rest\DeliveryZoneController::class, 'checkDistance']);
        Route::get('shop/{id}/delivery-zone/check/distance', [Rest\DeliveryZoneController::class, 'checkDistanceByShop']);
        Route::get('shop-payments/{id}',                     [Rest\ShopController::class, 'shopPayments']);
        Route::get('product-histories/paginate',             [Rest\ProductController::class, 'history']);

    });

    Route::group(['prefix' => 'payments', 'middleware' => ['sanctum.check'], 'as' => 'payment.'], function () {

        /* Transactions */
        Route::post('{type}/{id}/transactions', [Payment\TransactionController::class, 'store']);
        Route::put('{type}/{id}/transactions',  [Payment\TransactionController::class, 'updateStatus']);
        Route::post('wallet/payment/top-up',     [Payment\WalletPaymentController::class, 'paymentTopUp']);

    });

    Route::group(['prefix' => 'dashboard'], function () {
        /* Galleries */
        Route::get('/galleries/paginate',               [GalleryController::class, 'paginate']);
        Route::get('/galleries/storage/files',          [GalleryController::class, 'getStorageFiles']);
        Route::post('/galleries/storage/files/delete',  [GalleryController::class, 'deleteStorageFile']);
        Route::post('/galleries',                       [GalleryController::class, 'store']);

        // USER BLOCK
        Route::group(['prefix' => 'user', 'middleware' => ['sanctum.check'], 'as' => 'user.'], function () {
            Route::get('profile/show',                          [User\ProfileController::class, 'show']);
            Route::put('profile/update',                        [User\ProfileController::class, 'update']);
            Route::delete('profile/delete',                     [User\ProfileController::class, 'delete']);
            Route::post('profile/firebase/token/update',        [User\ProfileController::class, 'fireBaseTokenUpdate']);
            Route::post('profile/password/update',              [User\ProfileController::class, 'passwordUpdate']);
            Route::get('profile/liked/looks',                   [User\ProfileController::class, 'likedLooks']);

            Route::get('orders/paginate',                       [User\OrderController::class, 'paginate']);
            Route::post('orders/review/{id}',                   [User\OrderController::class, 'addOrderReview']);
            Route::post('orders/deliveryman-review/{id}',       [User\OrderController::class, 'addDeliverymanReview']);
            Route::post('orders/{id}/status/change',            [User\OrderController::class, 'orderStatusChange']);
            Route::apiResource('orders',              User\OrderController::class)->except('index');

            Route::get('/invites/paginate',                     [User\InviteController::class, 'paginate']);
            Route::post('/shop/invitation/{uuid}/link',         [User\InviteController::class, 'create']);

            Route::get('/point/histories',                      [User\WalletController::class, 'pointHistories']);

            Route::get('/wallet/histories',                     [User\WalletController::class, 'walletHistories']);
            Route::post('/wallet/withdraw',                     [User\WalletController::class, 'store']);
            Route::post('/wallet/history/{uuid}/status/change', [User\WalletController::class, 'changeStatus']);

            /* Transaction */
            Route::get('transactions/paginate',                 [User\TransactionController::class, 'paginate']);
            Route::get('transactions/{id}',                     [User\TransactionController::class, 'show']);

            /* Shop */
            Route::post('shops',                                [Seller\ShopController::class, 'shopCreate']);
            Route::get('shops',                                 [Seller\ShopController::class, 'shopShow']);
            Route::put('shops',                                 [Seller\ShopController::class, 'shopUpdate']);

            /* Ticket */
            Route::get('tickets/paginate',                      [User\TicketController::class, 'paginate']);
            Route::apiResource('tickets',             User\TicketController::class);

            /* Export */
            Route::get('export/order/{id}/pdf',                 [User\ExportController::class, 'orderExportPDF']);

            /* Carts */
            Route::post('cart',                                 [User\CartController::class, 'store']);
            Route::post('cart/insert-product',                  [User\CartController::class, 'insertProducts']);
            Route::post('cart/open',                            [User\CartController::class, 'openCart']);
            Route::post('cart/set-group/{id}',                  [User\CartController::class, 'setGroup']);
            Route::delete('cart/delete',                        [User\CartController::class, 'delete']);
            Route::delete('cart/product/delete',                [User\CartController::class, 'cartProductDelete']);
            Route::delete('cart/member/delete',                 [User\CartController::class, 'userCartDelete']);
            Route::get('cart',                                  [User\CartController::class, 'get']);
            Route::post('cart/status/{user_cart_uuid}',         [User\CartController::class, 'statusChange']);
            Route::post('cart/calculate/{id}',                  [User\CartController::class, 'cartCalculate']);

            /* Order Refunds */
            Route::get('order-refunds/paginate',                [User\OrderRefundsController::class, 'paginate']);
            Route::delete('order-refunds/delete',               [User\OrderRefundsController::class, 'destroy']);
            Route::apiResource('order-refunds',       User\OrderRefundsController::class);

            Route::post('update/notifications',                 [User\ProfileController::class, 'notificationsUpdate']);
            Route::get('notifications',                         [User\ProfileController::class, 'notifications']);

            Route::get('order-stripe-process', [Payment\StripeController::class, 'orderProcessTransaction']);
            Route::get('subscription-stripe-process', [Payment\StripeController::class, 'subscriptionProcessTransaction']);

            Route::get('order-razorpay-process', [Payment\RazorPayController::class, 'orderProcessTransaction']);
            Route::get('subscription-razorpay-process', [Payment\RazorPayController::class, 'subscriptionProcessTransaction']);

            Route::get('order-mercado-pago-process', [Payment\MercadoPagoController::class, 'orderProcessTransaction']);
            Route::get('subscription-mercado-pago-process', [Payment\RazorPayController::class, 'subscriptionProcessTransaction']);

            Route::get('order-paystack-process', [Payment\PayStackController::class, 'orderProcessTransaction']);
            Route::get('subscription-paystack-process', [Payment\PayStackController::class, 'subscriptionProcessTransaction']);

//            Route::get('order-paypal-process', [Payment\PayPalController::class, 'orderProcessTransaction']);
//            Route::get('subscription-paypal-process', [Payment\PayPalController::class, 'subscriptionProcessTransaction']);

            Route::get('order-flw-process', [Payment\FlutterWaveController::class, 'orderProcessTransaction']);
            Route::get('subscription-flw-process', [Payment\FlutterWaveController::class, 'subscriptionProcessTransaction']);

            Route::get('order-paytabs-process', [Payment\PayTabsController::class, 'orderProcessTransaction']);
            Route::get('subscription-paytabs-process', [Payment\PayTabsController::class, 'subscriptionProcessTransaction']);
        });

        // DELIVERYMAN BLOCK
        Route::group(['prefix' => 'deliveryman', 'middleware' => ['sanctum.check', 'role:deliveryman'], 'as' => 'deliveryman.'], function () {
            Route::get('orders/paginate',           [Deliveryman\OrderController::class, 'paginate']);
            Route::get('orders/{id}',               [Deliveryman\OrderController::class, 'show']);
            Route::post('order/{id}/status/update', [Deliveryman\OrderController::class, 'orderStatusUpdate']);
            Route::post('orders/{id}/review',       [Deliveryman\OrderController::class, 'addReviewByDeliveryman']);
            Route::post('orders/{id}/current',      [Deliveryman\OrderController::class, 'setCurrent']);
            Route::get('statistics/count',          [Deliveryman\DashboardController::class, 'countStatistics']);

            Route::post('settings',                 [Deliveryman\DeliveryManSettingController::class, 'store']);
            Route::post('settings/location', [Deliveryman\DeliveryManSettingController::class, 'updateLocation']);
            Route::post('settings/online',          [Deliveryman\DeliveryManSettingController::class, 'online']);
            Route::get('settings',                  [Deliveryman\DeliveryManSettingController::class, 'show']);
            Route::post('order/{id}/attach/me',     [Deliveryman\OrderController::class, 'orderDeliverymanUpdate']);

            /* Payouts */
            Route::apiResource('payouts', Deliveryman\PayoutsController::class);

            Route::delete('payouts/delete', [Deliveryman\PayoutsController::class, 'destroy']);

            /* Report Orders */
            Route::get('order/report',      [Deliveryman\OrderReportController::class, 'report']);
        });

        // SELLER BLOCK
        Route::group(['prefix' => 'seller', 'middleware' => ['sanctum.check', 'role:seller|moderator'], 'as' => 'seller.'], function () {
            /* Dashboard */
            Route::get('statistics',                [Seller\DashboardController::class, 'ordersStatistics']);
            Route::get('statistics/orders/chart',   [Seller\DashboardController::class, 'ordersChart']);
            Route::get('statistics/products',       [Seller\DashboardController::class, 'productsStatistic']);
            Route::get('statistics/users',          [Seller\DashboardController::class, 'usersStatistic']);

            /* Extras Group & Value */
            Route::get('extra/groups/types',            [Seller\ExtraGroupController::class, 'typesList']);

            Route::apiResource('extra/groups', Seller\ExtraGroupController::class);
            Route::delete('extra/groups/delete',        [Seller\ExtraGroupController::class, 'destroy']);

            Route::apiResource('extra/values', Seller\ExtraValueController::class);
            Route::delete('extra/values/delete',        [Seller\ExtraValueController::class, 'destroy']);

            /* Units */
            Route::get('units/paginate',            [Seller\UnitController::class, 'paginate']);
            Route::get('units/{id}',                [Seller\UnitController::class, 'show']);

            /* Seller Shop */
            Route::get('shops',                     [Seller\ShopController::class, 'shopShow']);
            Route::put('shops',                     [Seller\ShopController::class, 'shopUpdate']);
            Route::post('shops/working/status',     [Seller\ShopController::class, 'setWorkingStatus']);

            /* Categories */
            Route::get('categories/export',                 [Seller\CategoryController::class, 'fileExport']);
            Route::post('categories/{uuid}/image/delete',   [Seller\CategoryController::class, 'imageDelete']);
            Route::get('categories/search',                 [Seller\CategoryController::class, 'categoriesSearch']);
            Route::get('categories/paginate',               [Seller\CategoryController::class, 'paginate']);
            Route::get('categories/select-paginate',        [Seller\CategoryController::class, 'selectPaginate']);
            Route::post('categories/import',                [Seller\CategoryController::class, 'fileImport']);
            Route::apiResource('categories',       Seller\CategoryController::class);
            Route::delete('categories/delete',              [Seller\CategoryController::class, 'destroy']);

            Route::get('brands/export',                     [Seller\BrandController::class, 'fileExport']);
            Route::post('brands/import',                    [Seller\BrandController::class, 'fileImport']);
            Route::get('brands/paginate',                   [Seller\BrandController::class, 'paginate']);
            Route::get('brands/search',                     [Seller\BrandController::class, 'brandsSearch']);
            Route::apiResource('brands',           Seller\BrandController::class);

            /* Shop Categories */
            Route::get('shop-categories/all-category',    [Seller\ShopCategoryController::class, 'allCategory']);
            Route::get('shop-categories/paginate',        [Seller\ShopCategoryController::class, 'paginate']);
            Route::get('shop-categories/select-paginate', [Seller\ShopCategoryController::class, 'selectPaginate']);
            Route::delete('shop-categories/delete',       [Seller\ShopCategoryController::class, 'destroy']);
            Route::apiResource('shop-categories',Seller\ShopCategoryController::class);

            /* Seller Product */
            Route::post('products/import',              [Seller\ProductController::class, 'fileImport']);
            Route::get('products/export',               [Seller\ProductController::class, 'fileExport']);
            Route::get('products/paginate',             [Seller\ProductController::class, 'paginate']);
            Route::get('products/search',               [Seller\ProductController::class, 'productsSearch']);
            Route::post('products/{uuid}/stocks',       [Seller\ProductController::class, 'addInStock']);
            Route::post('products/{uuid}/properties',   [Seller\ProductController::class, 'addProductProperties']);
            Route::post('products/{uuid}/extras',       [Seller\ProductController::class, 'addProductExtras']);
            Route::get('stocks/select-paginate',        [Seller\ProductController::class, 'selectStockPaginate']);
            Route::post('products/{uuid}/active',       [Seller\ProductController::class, 'setActive']);
            Route::delete('products/delete',            [Seller\ProductController::class, 'destroy']);
            Route::apiResource('products',    Seller\ProductController::class);

            /* Seller Coupon */
            Route::get('coupons/paginate',              [Seller\CouponController::class, 'paginate']);
            Route::delete('coupons/delete',             [Seller\CouponController::class, 'destroy']);
            Route::apiResource('coupons',     Seller\CouponController::class);

            /* Seller Shop Users */
            Route::get('shop/users/paginate',           [Seller\UserController::class, 'shopUsersPaginate']);
            Route::get('shop/users/role/deliveryman',   [Seller\UserController::class, 'getDeliveryman']);
            Route::get('shop/users/{uuid}',             [Seller\UserController::class, 'shopUserShow']);

            /* Seller Users */
            Route::get('users/paginate',                [Seller\UserController::class, 'paginate']);
            Route::get('users/{uuid}',                  [Seller\UserController::class, 'show']);
            Route::post('users',                        [Seller\UserController::class, 'store']);
            Route::post('users/{uuid}/change/status',   [Seller\UserController::class, 'setUserActive']);

            /* Seller Invite */
            Route::get('shops/invites/paginate',                [Seller\InviteController::class, 'paginate']);
            Route::post('/shops/invites/{id}/status/change',    [Seller\InviteController::class, 'changeStatus']);

            /* Seller Coupon */
            Route::get('discounts/paginate',              [Seller\DiscountController::class, 'paginate']);
            Route::post('discounts/{id}/active/status',   [Seller\DiscountController::class, 'setActiveStatus']);
            Route::delete('discounts/delete',             [Seller\DiscountController::class, 'destroy']);
            Route::apiResource('discounts',    Seller\DiscountController::class)->except('index');

            /* Seller Banner */
            Route::get('banners/paginate',          [Seller\BannerController::class, 'paginate']);
            Route::post('banners/active/{id}',      [Seller\BannerController::class, 'setActiveBanner']);
            Route::delete('banners/delete',         [Seller\BannerController::class, 'destroy']);
            Route::apiResource('banners',Seller\BannerController::class);

            /* Seller Order */
            Route::get('order/export',              [Seller\OrderController::class, 'fileExport']);
            Route::post('order/import',             [Seller\OrderController::class, 'fileImport']);
            Route::get('order/products/calculate',  [Seller\OrderController::class, 'orderStocksCalculate']);
            Route::get('orders/paginate',           [Seller\OrderController::class, 'paginate']);
            Route::post('order/{id}/deliveryman',   [Seller\OrderController::class, 'orderDeliverymanUpdate']);
            Route::post('order/{id}/status',        [Seller\OrderController::class, 'orderStatusUpdate']);
            Route::apiResource('orders',  Seller\OrderController::class)->except('index');
            Route::delete('orders/delete',          [Seller\OrderController::class, 'destroy']);

            /* Transaction */
            Route::get('transactions/paginate', [Seller\TransactionController::class, 'paginate']);
            Route::get('transactions/{id}', [Seller\TransactionController::class, 'show']);

            /* Seller Subscription */
            Route::get('subscriptions',               [Seller\SubscriptionController::class, 'index']);
            Route::get('my-subscriptions',            [Seller\SubscriptionController::class, 'mySubscription']);
            Route::post('subscriptions/{id}/attach',  [Seller\SubscriptionController::class, 'subscriptionAttach']);

            /* OnResponse Shop */
            Route::apiResource('bonuses', Seller\BonusController::class);
            Route::post('bonuses/status/{id}',      [Seller\BonusController::class, 'statusChange']);
            Route::delete('bonuses/delete',         [Seller\BonusController::class, 'destroy']);

            /* Stories */
            Route::post('stories/upload',           [Seller\StoryController::class, 'uploadFiles']);

            Route::apiResource('stories', Seller\StoryController::class);
            Route::delete('stories/delete',         [Seller\StoryController::class, 'destroy']);

            /* Tags */
            Route::apiResource('tags', Seller\TagController::class);
            Route::delete('tags/delete',         [Seller\TagController::class, 'destroy']);

            /* Delivery Zones */
            Route::apiResource('delivery-zones', Seller\DeliveryZoneController::class);
            Route::delete('delivery-zones/delete',         [Seller\DeliveryZoneController::class, 'destroy']);

            /* Payments */
            Route::post('shop-payments/{id}/active/status', [Seller\ShopPaymentController::class, 'setActive']);
            Route::get('shop-payments/shop-non-exist', [Seller\ShopPaymentController::class, 'shopNonExist']);
            Route::get('shop-payments/delete', [Seller\ShopPaymentController::class, 'destroy']);
            Route::apiResource('shop-payments', Seller\ShopPaymentController::class);

            /* Order Refunds */
            Route::get('order-refunds/paginate', [Seller\OrderRefundsController::class, 'paginate']);
            Route::delete('order-refunds/delete', [Seller\OrderRefundsController::class, 'destroy']);
            Route::apiResource('order-refunds', Seller\OrderRefundsController::class);

            /* Shop Working Days */
            Route::apiResource('shop-working-days', Seller\ShopWorkingDayController::class)
                ->except('store');
            Route::delete('shop-working-days/delete', [Seller\ShopWorkingDayController::class, 'destroy']);

            /* Shop Closed Days */
            Route::apiResource('shop-closed-dates', Seller\ShopClosedDateController::class)
                ->except('store');
            Route::delete('shop-closed-dates/delete', [Seller\ShopClosedDateController::class, 'destroy']);

            /* Payouts */
            Route::apiResource('payouts', Seller\PayoutsController::class);

            Route::delete('payouts/delete', [Seller\PayoutsController::class, 'destroy']);

            /* Report Orders */
            Route::get('order/report',              [Seller\OrderReportController::class, 'report']);
            Route::get('orders/report/paginate',    [Seller\OrderReportController::class, 'reportPaginate']);

            /* Reviews */
            Route::get('reviews/paginate',          [Seller\ReviewController::class, 'paginate']);
            Route::apiResource('reviews', Seller\ReviewController::class)->only('show');

            /* Receipts */
            Route::apiResource('receipts',Seller\ReceiptController::class);
            Route::delete('receipts/delete',        [Seller\ReceiptController::class, 'destroy']);

            /* Galleries */
            Route::apiResource('galleries',Seller\ShopGalleriesController::class)->except('show');

            /* Shop Deliveryman Setting */
            Route::apiResource('shop-deliveryman-settings',Seller\ShopDeliverymanSettingController::class);
            Route::delete('shop-deliveryman-settings/delete',        [Seller\ShopDeliverymanSettingController::class, 'destroy']);
        });

        // ADMIN BLOCK
        Route::group(['prefix' => 'admin', 'middleware' => ['sanctum.check', 'role:admin|manager'], 'as' => 'admin.'], function () {

            /* Dashboard */
            Route::get('timezones',                 [Admin\DashboardController::class, 'timeZones']);
            Route::get('timezone',                  [Admin\DashboardController::class, 'timeZone']);
            Route::post('timezone',                 [Admin\DashboardController::class, 'timeZoneChange']);

            Route::get('statistics',                [Admin\DashboardController::class, 'ordersStatistics']);
            Route::get('statistics/orders/chart',   [Admin\DashboardController::class, 'ordersChart']);
            Route::get('statistics/products',       [Admin\DashboardController::class, 'productsStatistic']);
            Route::get('statistics/users',          [Admin\DashboardController::class, 'usersStatistic']);

            /* Terms & Condition */
            Route::post('term',                     [Admin\TermsController::class, 'store']);
            Route::get('term',                      [Admin\TermsController::class, 'show']);

            Route::get('term/drop/all',             [Admin\TermsController::class, 'dropAll']);
            Route::get('term/restore/all',          [Admin\TermsController::class, 'restoreAll']);
            Route::get('term/truncate/db',          [Admin\TermsController::class, 'truncate']);

            /* Privacy & Policy */
            Route::post('policy',                   [Admin\PrivacyPolicyController::class, 'store']);
            Route::get('policy',                    [Admin\PrivacyPolicyController::class, 'show']);
            Route::get('policy/drop/all',           [Admin\PrivacyPolicyController::class, 'dropAll']);
            Route::get('policy/restore/all',        [Admin\PrivacyPolicyController::class, 'restoreAll']);
            Route::get('policy/truncate/db',        [Admin\PrivacyPolicyController::class, 'truncate']);

            /* Reviews */
            Route::get('reviews/paginate',          [Admin\ReviewController::class, 'paginate']);
            Route::apiResource('reviews', Admin\ReviewController::class);
            Route::delete('reviews/delete',         [Admin\ReviewController::class, 'destroy']);
            Route::get('reviews/drop/all',          [Admin\ReviewController::class, 'dropAll']);
            Route::get('reviews/restore/all',       [Admin\ReviewController::class, 'restoreAll']);
            Route::get('reviews/truncate/db',       [Admin\ReviewController::class, 'truncate']);

            /* Languages */
            Route::get('languages/default',             [Admin\LanguageController::class, 'getDefaultLanguage']);
            Route::post('languages/default/{id}',       [Admin\LanguageController::class, 'setDefaultLanguage']);
            Route::get('languages/active',              [Admin\LanguageController::class, 'getActiveLanguages']);
            Route::post('languages/{id}/image/delete',  [Admin\LanguageController::class, 'imageDelete']);
            Route::apiResource('languages',   Admin\LanguageController::class);
            Route::delete('languages/delete',           [Admin\LanguageController::class, 'destroy']);
            Route::get('languages/drop/all',            [Admin\LanguageController::class, 'dropAll']);
            Route::get('languages/restore/all',         [Admin\LanguageController::class, 'restoreAll']);
            Route::get('languages/truncate/db',         [Admin\LanguageController::class, 'truncate']);

            /* Languages */
            Route::get('currencies/default',            [Admin\CurrencyController::class, 'getDefaultCurrency']);
            Route::post('currencies/default/{id}',      [Admin\CurrencyController::class, 'setDefaultCurrency']);
            Route::get('currencies/active',             [Admin\CurrencyController::class, 'getActiveCurrencies']);
            Route::apiResource('currencies',  Admin\CurrencyController::class);
            Route::delete('currencies/delete',          [Admin\CurrencyController::class, 'destroy']);
            Route::get('currencies/drop/all',           [Admin\CurrencyController::class, 'dropAll']);
            Route::get('currencies/restore/all',        [Admin\CurrencyController::class, 'restoreAll']);
            Route::get('currencies/truncate/db',        [Admin\CurrencyController::class, 'truncate']);

            /* Categories */
            Route::get('categories/export',                 [Admin\CategoryController::class, 'fileExport']);
            Route::post('categories/{uuid}/image/delete',   [Admin\CategoryController::class, 'imageDelete']);
            Route::get('categories/search',                 [Admin\CategoryController::class, 'categoriesSearch']);
            Route::get('categories/paginate',               [Admin\CategoryController::class, 'paginate']);
            Route::get('categories/select-paginate',        [Admin\CategoryController::class, 'selectPaginate']);
            Route::post('categories/import',                [Admin\CategoryController::class, 'fileImport']);
            Route::apiResource('categories',      Admin\CategoryController::class);
            Route::delete('categories/delete',              [Admin\CategoryController::class, 'destroy']);
            Route::get('categories/drop/all',               [Admin\CategoryController::class, 'dropAll']);
            Route::get('categories/restore/all',            [Admin\CategoryController::class, 'restoreAll']);
            Route::get('categories/truncate/db',            [Admin\CategoryController::class, 'truncate']);

            /* Brands */
            Route::get('brands/export',             [Admin\BrandController::class, 'fileExport']);
            Route::post('brands/import',            [Admin\BrandController::class, 'fileImport']);
            Route::get('brands/paginate',           [Admin\BrandController::class, 'paginate']);
            Route::get('brands/search',             [Admin\BrandController::class, 'brandsSearch']);
            Route::apiResource('brands',  Admin\BrandController::class);
            Route::delete('brands/delete',          [Admin\BrandController::class, 'destroy']);
            Route::get('brands/drop/all',           [Admin\BrandController::class, 'dropAll']);
            Route::get('brands/restore/all',        [Admin\BrandController::class, 'restoreAll']);
            Route::get('brands/truncate/db',        [Admin\BrandController::class, 'truncate']);

            /* Banner */
            Route::get('banners/paginate',          [Admin\BannerController::class, 'paginate']);
            Route::post('banners/active/{id}',      [Admin\BannerController::class, 'setActiveBanner']);
            Route::apiResource('banners', Admin\BannerController::class);
            Route::delete('banners/delete',         [Admin\BannerController::class, 'destroy']);
            Route::get('banners/drop/all',          [Admin\BannerController::class, 'dropAll']);
            Route::get('banners/restore/all',       [Admin\BannerController::class, 'restoreAll']);
            Route::get('banners/truncate/db',       [Admin\BannerController::class, 'truncate']);

            /* Units */
            Route::get('units/paginate',            [Admin\UnitController::class, 'paginate']);
            Route::post('units/active/{id}',        [Admin\UnitController::class, 'setActiveUnit']);
            Route::delete('units/delete',           [Admin\UnitController::class, 'destroy']);
            Route::get('units/drop/all',            [Admin\UnitController::class, 'dropAll']);
            Route::get('units/restore/all',         [Admin\UnitController::class, 'restoreAll']);
            Route::get('units/truncate/db',         [Admin\UnitController::class, 'truncate']);
            Route::apiResource('units',  Admin\UnitController::class)->except('destroy');

            /* Shops */
            Route::get('shop/export',                   [Admin\ShopController::class, 'fileExport']);
            Route::post('shop/import',                  [Admin\ShopController::class, 'fileImport']);
            Route::get('shops/search',                  [Admin\ShopController::class, 'shopsSearch']);
            Route::get('shops/paginate',                [Admin\ShopController::class, 'paginate']);
            Route::post('shops/{uuid}/image/delete',    [Admin\ShopController::class, 'imageDelete']);
            Route::post('shops/{uuid}/status/change',   [Admin\ShopController::class, 'statusChange']);
            Route::apiResource('shops',       Admin\ShopController::class);
            Route::delete('shops/delete',               [Admin\ShopController::class, 'destroy']);
            Route::get('shops/drop/all',                [Admin\ShopController::class, 'dropAll']);
            Route::get('shops/restore/all',             [Admin\ShopController::class, 'restoreAll']);
            Route::get('shops/truncate/db',             [Admin\ShopController::class, 'truncate']);
            Route::post('shops/working/status',         [Admin\ShopController::class, 'setWorkingStatus']);

            /* Extras Group & Value */
            Route::get('extra/groups/types',            [Admin\ExtraGroupController::class, 'typesList']);

            Route::apiResource('extra/groups', Admin\ExtraGroupController::class);
            Route::delete('extra/groups/delete',        [Admin\ExtraGroupController::class, 'destroy']);
            Route::get('extra/groups/drop/all',         [Admin\ExtraGroupController::class, 'dropAll']);
            Route::get('extra/groups/restore/all',      [Admin\ExtraGroupController::class, 'restoreAll']);
            Route::get('extra/groups/truncate/db',      [Admin\ExtraGroupController::class, 'truncate']);

            Route::apiResource('extra/values', Admin\ExtraValueController::class);
            Route::delete('extra/values/delete',        [Admin\ExtraValueController::class, 'destroy']);
            Route::get('extra/values/drop/all',         [Admin\ExtraValueController::class, 'dropAll']);
            Route::get('extra/values/restore/all',      [Admin\ExtraValueController::class, 'restoreAll']);
            Route::get('extra/values/truncate/db',      [Admin\ExtraValueController::class, 'truncate']);

            /* Products */
            Route::get('products/export',                [Admin\ProductController::class, 'fileExport']);
            Route::get('most-popular/products',          [Admin\ProductController::class, 'mostPopulars']);
            Route::post('products/import',               [Admin\ProductController::class, 'fileImport']);
            Route::get('products/paginate',              [Admin\ProductController::class, 'paginate']);
            Route::get('products/search',                [Admin\ProductController::class, 'productsSearch']);
            Route::post('products/{uuid}/stocks',        [Admin\ProductController::class, 'addInStock']);
            Route::post('stock/{id}/addons',             [Admin\ProductController::class, 'addAddonInStock']);
            Route::post('products/{uuid}/properties',    [Admin\ProductController::class, 'addProductProperties']);
            Route::post('products/{uuid}/extras',        [Admin\ProductController::class, 'addProductExtras']);
            Route::post('products/{uuid}/active',        [Admin\ProductController::class, 'setActive']);
            Route::post('products/{uuid}/status/change', [Admin\ProductController::class, 'setStatus']);
            Route::apiResource('products',     Admin\ProductController::class);
            Route::delete('products/delete',             [Admin\ProductController::class, 'destroy']);
            Route::get('products/drop/all',              [Admin\ProductController::class, 'dropAll']);
            Route::get('products/restore/all',           [Admin\ProductController::class, 'restoreAll']);
            Route::get('products/truncate/db',           [Admin\ProductController::class, 'truncate']);
            Route::get('stocks/drop/all',                [Admin\ProductController::class, 'dropAllStocks']);
            Route::get('stocks/restore/all',             [Admin\ProductController::class, 'restoreAllStocks']);
            Route::get('stocks/truncate/db',             [Admin\ProductController::class, 'truncateStocks']);
            Route::get('stocks/select-paginate',         [Admin\ProductController::class, 'selectStockPaginate']);

            /* Orders */
            Route::get('order/export',                   [Admin\OrderController::class, 'fileExport']);
            Route::post('order/import',                  [Admin\OrderController::class, 'fileImport']);
            Route::get('orders/paginate',                [Admin\OrderController::class, 'paginate']);
            Route::get('order/details/paginate',         [Admin\OrderDetailController::class, 'paginate']);
            Route::get('order/products/calculate',       [Admin\OrderController::class, 'orderStocksCalculate']);
            Route::post('order/{id}/deliveryman',        [Admin\OrderController::class, 'orderDeliverymanUpdate']);
            Route::post('order/{id}/status',             [Admin\OrderController::class, 'orderStatusUpdate']);
            Route::apiResource('orders',       Admin\OrderController::class);
            Route::delete('orders/delete',               [Admin\OrderController::class, 'destroy']);
            Route::get('orders/drop/all',                [Admin\OrderController::class, 'dropAll']);
            Route::get('orders/restore/all',             [Admin\OrderController::class, 'restoreAll']);
            Route::get('orders/truncate/db',             [Admin\OrderController::class, 'truncate']);
            Route::get('user-orders/{id}',               [Admin\OrderController::class, 'userOrder']);
            Route::get('user-orders/{id}/paginate',      [Admin\OrderController::class, 'userOrders']);

            /* Users */
            Route::get('users/search',                  [Admin\UserController::class, 'usersSearch']);
            Route::get('users/paginate',                [Admin\UserController::class, 'paginate']);

            Route::get('users/drop/all',                [Admin\UserController::class, 'dropAll']);
            Route::get('users/restore/all',             [Admin\UserController::class, 'restoreAll']);
            Route::get('users/truncate/db',             [Admin\UserController::class, 'truncate']);

            Route::post('users/{uuid}/role/update',     [Admin\UserController::class, 'updateRole']);
            Route::get('users/{uuid}/wallets/history',  [Admin\UserController::class, 'walletHistories']);
            Route::post('users/{uuid}/wallets',         [Admin\UserController::class, 'topUpWallet']);
            Route::post('users/{uuid}/active',          [Admin\UserController::class, 'setActive']);
            Route::post('users/{uuid}/password',        [Admin\UserController::class, 'passwordUpdate']);
            Route::apiResource('users',       Admin\UserController::class);
            Route::delete('users/delete',               [Admin\UserController::class, 'destroy']);

            Route::get('roles', Admin\RoleController::class);

            /* Users Wallet Histories */
            Route::get('wallet/histories/paginate',     [Admin\WalletHistoryController::class, 'paginate']);
            Route::get('wallet/histories/drop/all',     [Admin\WalletHistoryController::class, 'dropAll']);
            Route::get('wallet/histories/restore/all',  [Admin\WalletHistoryController::class, 'restoreAll']);
            Route::get('wallet/histories/truncate/db',  [Admin\WalletHistoryController::class, 'truncate']);
            Route::post('wallet/history/{uuid}/status/change', [Admin\WalletHistoryController::class, 'changeStatus']);
            Route::get('wallet/drop/all',               [Admin\WalletController::class, 'dropAll']);
            Route::get('wallet/restore/all',            [Admin\WalletController::class, 'restoreAll']);
            Route::get('wallet/truncate/db',            [Admin\WalletController::class, 'truncate']);

            /* Subscriptions */
            Route::apiResource('subscriptions', Admin\SubscriptionController::class);
            Route::get('subscriptions/drop/all',          [Admin\SubscriptionController::class, 'dropAll']);
            Route::get('subscriptions/restore/all',       [Admin\SubscriptionController::class, 'restoreAll']);
            Route::get('subscriptions/truncate/db',       [Admin\SubscriptionController::class, 'truncate']);

            /* Point */
            Route::get('points/paginate',           [Admin\PointController::class, 'paginate']);
            Route::post('points/{id}/active',       [Admin\PointController::class, 'setActive']);
            Route::apiResource('points',  Admin\PointController::class);
            Route::delete('points/delete',          [Admin\PointController::class, 'destroy']);
            Route::get('points/drop/all',           [Admin\PointController::class, 'dropAll']);
            Route::get('points/restore/all',        [Admin\PointController::class, 'restoreAll']);
            Route::get('points/truncate/db',        [Admin\PointController::class, 'truncate']);

            /* Payments */
            Route::post('payments/{id}/active/status', [Admin\PaymentController::class, 'setActive']);
            Route::apiResource('payments',   Admin\PaymentController::class)
                ->except('store', 'delete');

            Route::get('payments/drop/all',           [Admin\PaymentController::class, 'dropAll']);
            Route::get('payments/restore/all',        [Admin\PaymentController::class, 'restoreAll']);
            Route::get('payments/truncate/db',        [Admin\PaymentController::class, 'truncate']);

            /* SMS Gateways */
            Route::post('sms-gateways/{id}/active/status', [Admin\SMSGatewayController::class, 'setActive']);
            Route::apiResource('sms-gateways',   Admin\SMSGatewayController::class)
                ->except('store', 'delete');
            Route::get('sms-gateways/drop/all',         [Admin\SMSGatewayController::class, 'dropAll']);
            Route::get('sms-gateways/restore/all',      [Admin\SMSGatewayController::class, 'restoreAll']);
            Route::get('sms-gateways/truncate/db',      [Admin\SMSGatewayController::class, 'truncate']);

            /* Translations */
            Route::get('translations/paginate',         [Admin\TranslationController::class, 'paginate']);
            Route::apiResource('translations',Admin\TranslationController::class);
            Route::get('translations/drop/all',         [Admin\TranslationController::class, 'dropAll']);
            Route::get('translations/restore/all',      [Admin\TranslationController::class, 'restoreAll']);
            Route::get('translations/truncate/db',      [Admin\TranslationController::class, 'truncate']);

            /* Transaction */
            Route::get('transactions/paginate',     [Admin\TransactionController::class, 'paginate']);
            Route::get('transactions/{id}',         [Admin\TransactionController::class, 'show']);
            Route::get('transactions/drop/all',     [Admin\TransactionController::class, 'dropAll']);
            Route::get('transactions/restore/all',  [Admin\TransactionController::class, 'restoreAll']);
            Route::get('transactions/truncate/db',  [Admin\TransactionController::class, 'truncate']);

            Route::get('tickets/paginate',          [Admin\TicketController::class, 'paginate']);
            Route::post('tickets/{id}/status',      [Admin\TicketController::class, 'setStatus']);
            Route::get('tickets/statuses',          [Admin\TicketController::class, 'getStatuses']);
            Route::apiResource('tickets', Admin\TicketController::class);
            Route::get('tickets/drop/all',          [Admin\TicketController::class, 'dropAll']);
            Route::get('tickets/restore/all',       [Admin\TicketController::class, 'restoreAll']);
            Route::get('tickets/truncate/db',       [Admin\TicketController::class, 'truncate']);

            /* FAQS */
            Route::get('faqs/paginate',                 [Admin\FAQController::class, 'paginate']);
            Route::post('faqs/{uuid}/active/status',    [Admin\FAQController::class, 'setActiveStatus']);
            Route::apiResource('faqs',        Admin\FAQController::class)->except('index');
            Route::delete('faqs/delete',                [Admin\FAQController::class, 'destroy']);
            Route::get('faqs/drop/all',                 [Admin\FAQController::class, 'dropAll']);
            Route::get('faqs/restore/all',              [Admin\FAQController::class, 'restoreAll']);
            Route::get('faqs/truncate/db',              [Admin\FAQController::class, 'truncate']);

            /* Blogs */
            Route::get('blogs/paginate',                [Admin\BlogController::class, 'paginate']);
            Route::post('blogs/{uuid}/publish',         [Admin\BlogController::class, 'blogPublish']);
            Route::post('blogs/{uuid}/active/status',   [Admin\BlogController::class, 'setActiveStatus']);
            Route::apiResource('blogs',       Admin\BlogController::class)->except('index');
            Route::delete('blogs/delete',               [Admin\BlogController::class, 'destroy']);
            Route::get('blogs/drop/all',                [Admin\BlogController::class, 'dropAll']);
            Route::get('blogs/restore/all',             [Admin\BlogController::class, 'restoreAll']);
            Route::get('blogs/truncate/db',             [Admin\BlogController::class, 'truncate']);

            /* Settings */
            Route::get('settings/system/information',   [Admin\SettingController::class, 'systemInformation']);
            Route::get('settings/system/cache/clear',   [Admin\SettingController::class, 'clearCache']);
            Route::apiResource('settings',    Admin\SettingController::class);
            Route::get('settings/drop/all',             [Admin\SettingController::class, 'dropAll']);
            Route::get('settings/restore/all',          [Admin\SettingController::class, 'restoreAll']);
            Route::get('settings/truncate/db',          [Admin\SettingController::class, 'truncate']);

            Route::post('backup/history',               [Admin\BackupController::class, 'download']);
            Route::get('backup/history',                [Admin\BackupController::class, 'histories']);
            Route::get('backup/drop/all',               [Admin\BackupController::class, 'dropAll']);
            Route::get('backup/restore/all',            [Admin\BackupController::class, 'restoreAll']);
            Route::get('backup/truncate/db',            [Admin\BackupController::class, 'truncate']);

            // Auto updates
            Route::post('/project-upload', [Admin\ProjectController::class, 'projectUpload']);
            Route::post('/project-update', [Admin\ProjectController::class, 'projectUpdate']);

            /* Stories */
            Route::apiResource('stories', Admin\StoryController::class)->only(['index', 'show']);
            Route::delete('stories/delete',         [Admin\StoryController::class, 'destroy']);
            Route::get('stories/drop/all',          [Admin\StoryController::class, 'dropAll']);
            Route::get('stories/restore/all',       [Admin\StoryController::class, 'restoreAll']);
            Route::get('stories/truncate/db',       [Admin\StoryController::class, 'truncate']);

            /* Order Statuses */
            Route::get('order-statuses',                [Admin\OrderStatusController::class, 'index']);
            Route::post('order-statuses/{id}/active',   [Admin\OrderStatusController::class, 'active']);
            Route::get('order-statuses/drop/all',       [Admin\OrderStatusController::class, 'dropAll']);
            Route::get('order-statuses/restore/all',    [Admin\OrderStatusController::class, 'restoreAll']);
            Route::get('order-statuses/truncate/db',    [Admin\OrderStatusController::class, 'truncate']);

            /* Tags */
            Route::apiResource('tags', Admin\TagController::class);
            Route::delete('tags/delete',         [Admin\TagController::class, 'destroy']);
            Route::get('tags/drop/all',          [Admin\TagController::class, 'dropAll']);
            Route::get('tags/restore/all',       [Admin\TagController::class, 'restoreAll']);
            Route::get('tags/truncate/db',       [Admin\TagController::class, 'truncate']);

            /* Delivery Zones */
            Route::apiResource('delivery-zones', Admin\DeliveryZoneController::class);
            Route::delete('delivery-zones/delete',         [Admin\DeliveryZoneController::class, 'destroy']);
            Route::get('delivery-zones/drop/all',          [Admin\DeliveryZoneController::class, 'dropAll']);
            Route::get('delivery-zones/restore/all',       [Admin\DeliveryZoneController::class, 'restoreAll']);
            Route::get('delivery-zones/truncate/db',       [Admin\DeliveryZoneController::class, 'truncate']);

            /* Email Setting */
            Route::apiResource('email-settings',  Admin\EmailSettingController::class);
            Route::delete('email-settings/delete',          [Admin\EmailSettingController::class, 'destroy']);
            Route::get('email-settings/set-active/{id}',    [Admin\EmailSettingController::class, 'setActive']);
            Route::get('email-settings/drop/all',           [Admin\EmailSettingController::class, 'dropAll']);
            Route::get('email-settings/restore/all',        [Admin\EmailSettingController::class, 'restoreAll']);
            Route::get('email-settings/truncate/db',        [Admin\EmailSettingController::class, 'truncate']);

            /* Email Subscriptions */
            Route::get('email-subscriptions', [Admin\SubscriptionController::class, 'emailSubscriptions']);
            Route::get('email-subscriptions/drop/all',      [Admin\SubscriptionController::class, 'dropAll']);
            Route::get('email-subscriptions/restore/all',   [Admin\SubscriptionController::class, 'restoreAll']);
            Route::get('email-subscriptions/truncate/db',   [Admin\SubscriptionController::class, 'truncate']);

            /* DeliveryMan Setting */
            Route::get('deliverymans/paginate',            [Admin\DeliveryManController::class, 'paginate']);
            Route::get('deliveryman-settings/paginate',    [Admin\DeliveryManSettingController::class, 'paginate']);
            Route::delete('deliveryman-settings/delete',   [Admin\DeliveryManSettingController::class, 'destroy']);

            Route::apiResource('deliveryman-settings', Admin\DeliveryManSettingController::class)
                ->except('index', 'destroy');

            /* Email Templates */
            Route::get('email-templates/types',             [Admin\EmailTemplateController::class, 'types']);
            Route::apiResource('email-templates', Admin\EmailTemplateController::class);
            Route::delete('email-templates/delete',         [Admin\EmailTemplateController::class, 'destroy']);
            Route::get('email-templates/drop/all',          [Admin\EmailTemplateController::class, 'dropAll']);
            Route::get('email-templates/restore/all',       [Admin\EmailTemplateController::class, 'restoreAll']);
            Route::get('email-templates/truncate/db',       [Admin\EmailTemplateController::class, 'truncate']);

            /* Order Refunds */
            Route::get('order-refunds/paginate',    [Admin\OrderRefundsController::class, 'paginate']);
            Route::delete('order-refunds/delete',   [Admin\OrderRefundsController::class, 'destroy']);
            Route::apiResource('order-refunds', Admin\OrderRefundsController::class);
            Route::get('order-refunds/drop/all',    [Admin\OrderRefundsController::class, 'dropAll']);
            Route::get('order-refunds/restore/all', [Admin\OrderRefundsController::class, 'restoreAll']);
            Route::get('order-refunds/truncate/db', [Admin\OrderRefundsController::class, 'truncate']);

            /* Shop Working Days */
            Route::get('shop-working-days/paginate',    [Admin\ShopWorkingDayController::class, 'paginate']);

            Route::apiResource('shop-working-days', Admin\ShopWorkingDayController::class)
                ->except('index', 'store');

            Route::delete('shop-working-days/delete',   [Admin\ShopWorkingDayController::class, 'destroy']);
            Route::get('shop-working-days/drop/all',    [Admin\ShopWorkingDayController::class, 'dropAll']);
            Route::get('shop-working-days/restore/all', [Admin\ShopWorkingDayController::class, 'restoreAll']);
            Route::get('shop-working-days/truncate/db', [Admin\ShopWorkingDayController::class, 'truncate']);

            /* Shop Closed Days */
            Route::get('shop-closed-dates/paginate',    [Admin\ShopClosedDateController::class, 'paginate']);

            Route::apiResource('shop-closed-dates', Admin\ShopClosedDateController::class)
                ->except('index', 'store');
            Route::delete('shop-closed-dates/delete',   [Admin\ShopClosedDateController::class, 'destroy']);
            Route::get('shop-closed-dates/drop/all',    [Admin\ShopClosedDateController::class, 'dropAll']);
            Route::get('shop-closed-dates/restore/all', [Admin\ShopClosedDateController::class, 'restoreAll']);
            Route::get('shop-closed-dates/truncate/db', [Admin\ShopClosedDateController::class, 'truncate']);

            /* Notifications */
            Route::apiResource('notifications', Admin\NotificationController::class);
            Route::delete('notifications/delete',   [Admin\NotificationController::class, 'destroy']);
            Route::get('notifications/drop/all',    [Admin\NotificationController::class, 'dropAll']);
            Route::get('notifications/restore/all', [Admin\NotificationController::class, 'restoreAll']);
            Route::get('notifications/truncate/db', [Admin\NotificationController::class, 'truncate']);

            /* Payouts */
            Route::apiResource('payouts', Admin\PayoutsController::class);
            Route::post('payouts/{id}/status',      [Admin\PayoutsController::class, 'statusChange']);
            Route::delete('payouts/delete',         [Admin\PayoutsController::class, 'destroy']);
            Route::get('payouts/drop/all',          [Admin\PayoutsController::class, 'dropAll']);
            Route::get('payouts/restore/all',       [Admin\PayoutsController::class, 'restoreAll']);
            Route::get('payouts/truncate/db',       [Admin\PayoutsController::class, 'truncate']);

            /* Shop tags */
            Route::apiResource('shop-tags',Admin\ShopTagController::class);
            Route::delete('shop-tags/delete',        [Admin\ShopTagController::class, 'destroy']);
            Route::get('shop-tags/drop/all',         [Admin\ShopTagController::class, 'dropAll']);
            Route::get('shop-tags/restore/all',      [Admin\ShopTagController::class, 'restoreAll']);
            Route::get('shop-tags/truncate/db',      [Admin\ShopTagController::class, 'truncate']);

            /* PaymentPayload tags */
            Route::apiResource('payment-payloads',Admin\PaymentPayloadController::class);
            Route::delete('payment-payloads/delete',        [Admin\PaymentPayloadController::class, 'destroy']);
            Route::get('payment-payloads/drop/all',         [Admin\PaymentPayloadController::class, 'dropAll']);
            Route::get('payment-payloads/restore/all',      [Admin\PaymentPayloadController::class, 'restoreAll']);
            Route::get('payment-payloads/truncate/db',      [Admin\PaymentPayloadController::class, 'truncate']);

            /* SmsPayload tags */
            Route::apiResource('sms-payloads',Admin\SmsPayloadController::class);
            Route::delete('sms-payloads/delete',        [Admin\SmsPayloadController::class, 'destroy']);
            Route::get('sms-payloads/drop/all',         [Admin\SmsPayloadController::class, 'dropAll']);
            Route::get('sms-payloads/restore/all',      [Admin\SmsPayloadController::class, 'restoreAll']);
            Route::get('sms-payloads/truncate/db',      [Admin\SmsPayloadController::class, 'truncate']);

            /* Bonuses*/
            Route::get('bonuses',                       [Admin\BonusController::class, 'index']);

            Route::apiResource('referrals',       Admin\ReferralController::class);
            Route::get('referrals/transactions/paginate',   [Admin\ReferralController::class,  'transactions']);

            /* Report Categories */
            Route::get('categories/report/chart',   [Admin\CategoryController::class,   'reportChart']);

            /* Report Extras */
//            Route::get('extras/report/paginate',    [Admin\ProductController::class,   'extrasReportPaginate']);

            /* Report Stocks */
            Route::get('stocks/report/paginate',    [Admin\ProductController::class,    'stockReportPaginate']);

            /* Report Products */
            Route::get('products/report/chart',     [Admin\ProductController::class,    'reportChart']);
            Route::get('products/report/paginate',  [Admin\ProductController::class,    'reportPaginate']);

            /* Report Orders */
            Route::get('orders/report/chart',       [Admin\OrderController::class,     'reportChart']);
            Route::get('orders/report/paginate',    [Admin\OrderController::class,     'reportChartPaginate']);

            /* Report Revenues */
            Route::get('revenue/report',            [Admin\OrderController::class, 'revenueReport']);

            /* Report Overviews */
            Route::get('overview/carts',            [Admin\OrderController::class,     'overviewCarts']);
            Route::get('overview/products',         [Admin\OrderController::class,     'overviewProducts']);
            Route::get('overview/categories',       [Admin\OrderController::class,     'overviewCategories']);

            /* Receipts */
            Route::apiResource('receipts',Admin\ReceiptController::class);
            Route::delete('receipts/delete',        [Admin\ReceiptController::class, 'destroy']);
            Route::get('receipts/drop/all',         [Admin\ReceiptController::class, 'dropAll']);
            Route::get('receipts/restore/all',      [Admin\ReceiptController::class, 'restoreAll']);
            Route::get('receipts/truncate/db',      [Admin\ReceiptController::class, 'truncate']);

            /* Shop Deliveryman Setting */
            Route::apiResource('shop-deliveryman-settings',Admin\ShopDeliverymanSettingController::class);
            Route::delete('shop-deliveryman-settings/delete',        [Admin\ShopDeliverymanSettingController::class, 'destroy']);
            Route::get('shop-deliveryman-settings/drop/all',         [Admin\ShopDeliverymanSettingController::class, 'dropAll']);
            Route::get('shop-deliveryman-settings/restore/all',      [Admin\ShopDeliverymanSettingController::class, 'restoreAll']);
            Route::get('shop-deliveryman-settings/truncate/db',      [Admin\ShopDeliverymanSettingController::class, 'truncate']);

            Route::post('module/booking/upload', [Admin\ModuleController::class, 'booking']);
        });

    });

    Route::group(['prefix' => 'webhook'], function () {
        Route::any('razorpay/payment',     [Payment\RazorPayController::class, 'paymentWebHook']);
        Route::any('stripe/payment',       [Payment\StripeController::class, 'paymentWebHook']);
        Route::any('flw/payment',         [Payment\FlutterWaveController::class, 'paymentWebHook']);
        Route::any('mercado-pago/payment', [Payment\MercadoPagoController::class, 'paymentWebHook']);
        Route::any('paystack/payment',     [Payment\PayStackController::class, 'paymentWebHook']);
    });
});

if (file_exists(__DIR__ . '/booking.php')) {
    include_once  __DIR__ . '/booking.php';
}
