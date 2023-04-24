<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Http\Controllers\Controller;
use App\Models\ActiveReferral;
use App\Models\AssignShopTag;
use App\Models\BackupHistory;
use App\Models\Banner;
use App\Models\BannerShop;
use App\Models\BannerTranslation;
use App\Models\Blog;
use App\Models\BlogTranslation;
use App\Models\Bonus;
use App\Models\Booking\Booking;
use App\Models\Booking\ShopSection;
use App\Models\Booking\ShopSectionTranslation;
use App\Models\Booking\Table;
use App\Models\Booking\UserBooking;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Coupon;
use App\Models\CouponTranslation;
use App\Models\Currency;
use App\Models\DeliveryManSetting;
use App\Models\DeliveryTranslation;
use App\Models\DeliveryZone;
use App\Models\Discount;
use App\Models\EmailSetting;
use App\Models\EmailSubscription;
use App\Models\EmailTemplate;
use App\Models\ExtraGroup;
use App\Models\ExtraGroupTranslation;
use App\Models\ExtraValue;
use App\Models\Faq;
use App\Models\FaqTranslation;
use App\Models\Gallery;
use App\Models\Invitation;
use App\Models\Language;
use App\Models\Like;
use App\Models\MetaTag;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Order;
use App\Models\OrderCoupon;
use App\Models\OrderDetail;
use App\Models\OrderRefund;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\PaymentPayload;
use App\Models\PaymentProcess;
use App\Models\Payout;
use App\Models\Point;
use App\Models\PointHistory;
use App\Models\PrivacyPolicy;
use App\Models\PrivacyPolicyTranslation;
use App\Models\Product;
use App\Models\ProductAddon;
use App\Models\ProductDiscount;
use App\Models\ProductExtra;
use App\Models\ProductProperties;
use App\Models\ProductTranslation;
use App\Models\Receipt;
use App\Models\ReceiptIngredient;
use App\Models\ReceiptInstruction;
use App\Models\ReceiptNutrition;
use App\Models\ReceiptNutritionTranslation;
use App\Models\ReceiptStock;
use App\Models\ReceiptTranslation;
use App\Models\Referral;
use App\Models\ReferralTranslation;
use App\Models\Review;
use App\Models\Settings;
use App\Models\Shop;
use App\Models\ShopCategory;
use App\Models\ShopClosedDate;
use App\Models\ShopDeliverymanSetting;
use App\Models\ShopGallery;
use App\Models\ShopPayment;
use App\Models\ShopSubscription;
use App\Models\ShopTag;
use App\Models\ShopTagTranslation;
use App\Models\ShopTranslation;
use App\Models\ShopWorkingDay;
use App\Models\SmsGateway;
use App\Models\SmsPayload;
use App\Models\SocialProvider;
use App\Models\Stock;
use App\Models\StockAddon;
use App\Models\StockExtra;
use App\Models\Story;
use App\Models\Subscription;
use App\Models\Tag;
use App\Models\TagTranslation;
use App\Models\TermCondition;
use App\Models\TermConditionTranslation;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Translation;
use App\Models\Unit;
use App\Models\UnitTranslation;
use App\Models\User;
use App\Models\UserCart;
use App\Models\UserPoint;
use App\Models\Wallet;
use App\Models\WalletHistory;
use App\Services\UserServices\UserActivityService;
use App\Traits\ApiResponse;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    use ApiResponse;

    public function bosyaTest(Request $request)
    {
        $model = (new UserActivityService);
        $model->create(1, 'bosya', 'bosya', 1, User::first());
    }

    public function gigLogistic(): array
    {
        $headers = [
            'Content-Type' => 'application/json'
        ];

        $response = Http::withHeaders($headers)->post(
            'https://giglthirdpartyapitestenv.azurewebsites.net/api/thirdparty/login',
            [
                'username' => '',
                'Password' => '',
//                'SessionObj' => '',
            ]
        );

        $test = Http::withHeaders($headers)->post(
            'http://test.giglogisticsse.com/api/thirdparty/login',
            [
                'username' => '',
                'Password' => '',
//                'SessionObj' => '',
            ]
        );

        return [
            'production' => [
                'url'    => 'https://giglthirdpartyapitestenv.azurewebsites.net/api/thirdparty/login',
                'e_uri'  => $response->effectiveUri(),
                'status' => $response->status(),
                'body'   => json_decode($response->body()),
            ],
            'test' => [
                'url'    => 'http://test.giglogisticsse.com/api/thirdparty/login',
                'e_uri'  => $response->effectiveUri(),
                'status' => $test->status(),
                'body'   => json_decode($test->body()),
            ]
        ];
    }

    public function allModels(): bool|string
    {
        $tables = collect([
            (new ActiveReferral)->getTable(),
            (new AssignShopTag)->getTable(),
            (new BackupHistory)->getTable(),
            (new Banner)->getTable(),
            (new BannerShop)->getTable(),
            (new BannerTranslation)->getTable(),
            (new Blog)->getTable(),
            (new BlogTranslation)->getTable(),
            (new Bonus)->getTable(),
            (new Booking)->getTable(),
            (new Brand)->getTable(),
            (new Cart)->getTable(),
            (new CartDetail)->getTable(),
            (new Category)->getTable(),
            (new CategoryTranslation)->getTable(),
            (new Coupon)->getTable(),
            (new CouponTranslation)->getTable(),
            (new Currency)->getTable(),
            (new DeliveryManSetting)->getTable(),
            (new DeliveryTranslation)->getTable(),
            (new DeliveryZone)->getTable(),
            (new Discount)->getTable(),
            (new EmailSetting)->getTable(),
            (new EmailSubscription)->getTable(),
            (new EmailTemplate)->getTable(),
            (new ExtraGroup)->getTable(),
            (new ExtraGroupTranslation)->getTable(),
            (new ExtraValue)->getTable(),
            (new Faq)->getTable(),
            (new FaqTranslation)->getTable(),
            (new Gallery)->getTable(),
            (new Invitation)->getTable(),
            (new Language)->getTable(),
            (new Like)->getTable(),
            (new MetaTag)->getTable(),
            (new Notification)->getTable(),
            (new NotificationUser)->getTable(),
            (new Order)->getTable(),
            (new OrderCoupon)->getTable(),
            (new OrderDetail)->getTable(),
            (new OrderRefund)->getTable(),
            (new OrderStatus)->getTable(),
            (new Payment)->getTable(),
            (new PaymentPayload)->getTable(),
            (new PaymentProcess)->getTable(),
            (new Payout)->getTable(),
            (new Point)->getTable(),
            (new PointHistory)->getTable(),
            (new PrivacyPolicy)->getTable(),
            (new PrivacyPolicyTranslation)->getTable(),
            (new Product)->getTable(),
            (new ProductAddon)->getTable(),
            (new ProductDiscount)->getTable(),
            (new ProductExtra)->getTable(),
            (new ProductProperties)->getTable(),
            (new ProductTranslation)->getTable(),
            (new Receipt)->getTable(),
            (new ReceiptIngredient)->getTable(),
            (new ReceiptInstruction)->getTable(),
            (new ReceiptNutrition)->getTable(),
            (new ReceiptNutritionTranslation)->getTable(),
            (new ReceiptStock)->getTable(),
            (new ReceiptTranslation)->getTable(),
            (new Referral)->getTable(),
            (new ReferralTranslation)->getTable(),
            (new Review)->getTable(),
            (new Settings)->getTable(),
            (new Shop)->getTable(),
            (new ShopCategory)->getTable(),
            (new ShopClosedDate)->getTable(),
            (new ShopDeliverymanSetting)->getTable(),
            (new ShopGallery)->getTable(),
            (new ShopPayment)->getTable(),
            (new ShopSection)->getTable(),
            (new ShopSectionTranslation)->getTable(),
            (new ShopSubscription)->getTable(),
            (new ShopTag)->getTable(),
            (new ShopTagTranslation)->getTable(),
            (new ShopTranslation)->getTable(),
            (new ShopWorkingDay)->getTable(),
            (new SmsGateway)->getTable(),
            (new SmsPayload)->getTable(),
            (new SocialProvider)->getTable(),
            (new Stock)->getTable(),
            (new StockAddon)->getTable(),
            (new StockExtra)->getTable(),
            (new Story)->getTable(),
            (new Subscription)->getTable(),
            (new Table)->getTable(),
            (new Tag)->getTable(),
            (new TagTranslation)->getTable(),
            (new TermCondition)->getTable(),
            (new TermConditionTranslation)->getTable(),
            (new Ticket)->getTable(),
            (new Transaction)->getTable(),
            (new Translation)->getTable(),
            (new Unit)->getTable(),
            (new UnitTranslation)->getTable(),
            (new User)->getTable(),
            (new UserBooking)->getTable(),
            (new UserCart)->getTable(),
            (new UserPoint)->getTable(),
            (new Wallet)->getTable(),
            (new WalletHistory)->getTable(),
        ]);

        $columns = collect();

        foreach ($tables as $table) {

            $list = DB::getSchemaBuilder()->getColumnListing($table);

            if (!empty($list)) {
                $columns->push(...$list);
            }

        }

        return json_encode($columns->unique()->values()->toArray());
    }
}
