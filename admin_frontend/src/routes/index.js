// ** admin routes **
import AppRoutes from './admin/app';
import AddonRoutes from './admin/addon';
import BannerRoutes from './admin/banner';
import BlogRoutes from './admin/blog';
import BrandRoutes from './admin/brand';
import CategoryImport from './admin/category';
import CouponRoutes from './admin/coupon';
import CurrencyRoutes from './admin/currency';
import DeliveryRoutes from './admin/deliveries';
import EmailProvidersRoutes from './admin/email-provider';
import ExtrasRoutes from './admin/extras';
import FaqRoutes from './admin/faq';
import FoodRoutes from './admin/food';
import GalleryRoutes from './admin/gallery';
import LanguagesRoutes from './admin/language';
import MessageSubscriber from './admin/message-subscriber';
import NotificationRoutes from './admin/notification';
import OrderRoutes from './admin/order';
import RefundsRoutes from './admin/refunds';
import RestraurantRoutes from './admin/restaurant';
import ReviewRoutes from './admin/reviews';
import SettingsRoutes from './admin/settings';
import ShopCategoryRoutes from './admin/shop-category';
import ShopTag from './admin/shop-tag';
import ShopRoutes from './admin/shop';
import SubscriptionsRoutes from './admin/subscriptions';
import UnitRoutes from './admin/unit';
import UsersRoutes from './admin/user';
import ReportRoutes from './admin/report';
import BranchRoutes from './admin/branches';

// ** seller routes ** ----------------
import SellerAddonRoutes from './seller/addon';
import SellerAppRoutes from './seller/app';
import SellerBonusRoutes from './seller/bonus';
import SellerBranchRoutes from './seller/branch';
import SellerBrandRoutes from './seller/brand';
import SellerCategoryImport from './seller/category';
import SellerExtrasImport from './seller/extras';
import SellerFoodRoutes from './seller/food';
import SellerGalleryRoutes from './seller/gallery';
import SellerOrderRoutes from './seller/order';
import SellerPaymentRoutes from './seller/payments';
import SellerReceptCategoryRoutes from './seller/recept-category';
import SellerReceptRoutes from './seller/recept';
import SellerRefundsRoutes from './seller/refunds';
import SellerReportRoutes from './seller/report';
import SellerReviewRoutes from './seller/reviews';
import SellerStoryRoutes from './seller/story';
import SellerSubscriptionsRoutes from './seller/subscriptions';

// ** Merge Routes
const AllRoutes = [
  ...AppRoutes,
  ...AddonRoutes,
  ...BannerRoutes,
  ...BlogRoutes,
  ...BrandRoutes,
  ...CategoryImport,
  ...CouponRoutes,
  ...CurrencyRoutes,
  ...DeliveryRoutes,
  ...EmailProvidersRoutes,
  ...ExtrasRoutes,
  ...FaqRoutes,
  ...FoodRoutes,
  ...GalleryRoutes,
  ...LanguagesRoutes,
  ...MessageSubscriber,
  ...NotificationRoutes,
  ...OrderRoutes,
  ...RefundsRoutes,
  ...RestraurantRoutes,
  ...ReviewRoutes,
  ...SettingsRoutes,
  ...ShopCategoryRoutes,
  ...ShopTag,
  ...ShopRoutes,
  ...SubscriptionsRoutes,
  ...UnitRoutes,
  ...UsersRoutes,
  ...ReportRoutes,
  ...BranchRoutes,

  // seller routes
  ...SellerAppRoutes,
  ...SellerAddonRoutes,
  ...SellerBrandRoutes,
  ...SellerCategoryImport,
  ...SellerFoodRoutes,
  ...SellerGalleryRoutes,
  ...SellerOrderRoutes,
  ...SellerRefundsRoutes,
  ...SellerReviewRoutes,
  ...SellerSubscriptionsRoutes,
  ...SellerReportRoutes,
  ...SellerBranchRoutes,
  ...SellerBonusRoutes,
  ...SellerExtrasImport,
  ...SellerPaymentRoutes,
  ...SellerReceptCategoryRoutes,
  ...SellerReceptRoutes,
  ...SellerStoryRoutes,
];

export { AllRoutes };
