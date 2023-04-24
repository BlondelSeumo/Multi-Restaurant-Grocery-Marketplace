import { WelcomeLayout } from './layout/welcome-layout';
import React, { Suspense, useEffect, useState } from 'react';
import { ToastContainer } from 'react-toastify';
import {
  Routes,
  Route,
  BrowserRouter as Router,
  Navigate,
} from 'react-router-dom';
import Loading from './components/loading';
import Providers from './providers';
import Check from './components/check';
import { ProtectedRoute } from './context/protected-route';
import PageLoading from './components/pageLoading';
import { PathLogout } from './context/path-logout';
import AppLayout from './layout/app-layout';
import i18n from './configs/i18next';
import informationService from './services/rest/information';
import SellerOrdersBoard from './views/seller-views/order/orders-board';
import AppSettings from './views/settings/app-setting';
import SellerCheck from './components/seller-check';
import NotificationClone from './views/notification/notification-clone';

const SellerOrderEdit = React.lazy(() =>
  import('./views/seller-views/order/order-edit')
);
const BonusList = React.lazy(() => import('./views/bonus'));
const NotFound = React.lazy(() => import('./views/not-found'));
const GalleryLanguages = React.lazy(() =>
  import('./views/gallery/gallery-languages')
);
const LanguageEdit = React.lazy(() => import('./views/languages/language-add'));
const CategoryAdd = React.lazy(() => import('./views/categories/category-add'));
const ShopCategoryAdd = React.lazy(() =>
  import('./views/shop-categories/category-add')
);
const BrandsAdd = React.lazy(() => import('./views/brands/brands-add'));
const ProductsAdd = React.lazy(() => import('./views/products/products-add'));

const Brands = React.lazy(() => import('./views/brands'));
const PosSystem = React.lazy(() => import('./views/pos-system'));
const Products = React.lazy(() => import('./views/products'));
const Languages = React.lazy(() => import('./views/languages'));
const LanguageAdd = React.lazy(() => import('./views/languages/language-add'));
const Currencies = React.lazy(() => import('./views/currencies'));
const Gallery = React.lazy(() => import('./views/gallery'));
const SubscriptionsEdit = React.lazy(() =>
  import('./views/subscriptions/subscriptions-edit')
);
const Subscriptions = React.lazy(() => import('./views/subscriptions'));
const Categories = React.lazy(() => import('./views/categories'));
const ShopCategories = React.lazy(() => import('./views/shop-categories'));
const Settings = React.lazy(() => import('./views/settings/settings'));
const GlobalSettings = React.lazy(() =>
  import('./views/global-settings/global-settings')
);
const OrderDetails = React.lazy(() => import('./views/order/order-details'));
const OrdersAdd = React.lazy(() => import('./views/order/orders-add'));
const OrderEdit = React.lazy(() => import('./views/order/order-edit'));
const Delivery = React.lazy(() => import('./views/delivery'));
const Admin = React.lazy(() => import('./views/user/admin'));
const RoleList = React.lazy(() => import('./views/user/role-list'));
const Banners = React.lazy(() => import('./views/banners'));
const BannerAdd = React.lazy(() => import('./views/banners/banner-add'));
const Notification = React.lazy(() => import('./views/notification'));
const NotificationAdd = React.lazy(() =>
  import('./views/notification/notification-add')
);
const Chat = React.lazy(() => import('./views/chat'));
const GeneralSettings = React.lazy(() =>
  import('./views/settings/general-settings')
);
const Catalog = React.lazy(() => import('./views/catalog'));

const Coupon = React.lazy(() => import('./views/coupons'));
const CouponAdd = React.lazy(() => import('./views/coupons/coupon-add'));
const CouponEdit = React.lazy(() => import('./views/coupons/coupon-edit'));

const MyShop = React.lazy(() => import('./views/seller-views/my-shop'));
const Translations = React.lazy(() => import('./views/translations'));
const Welcome = React.lazy(() => import('./views/welcome/welcome'));
const SellerCategories = React.lazy(() =>
  import('./views/seller-views/categories')
);
const SellerBrands = React.lazy(() => import('./views/seller-views/brands'));
const CategoryEdit = React.lazy(() =>
  import('./views/categories/category-edit')
);
const ShopCategoryEdit = React.lazy(() =>
  import('./views/shop-categories/category-edit')
);
const BrandsEdit = React.lazy(() => import('./views/brands/brands-edit'));
const Backup = React.lazy(() => import('./views/backup'));
const SystemInformation = React.lazy(() =>
  import('./views/system-information')
);
const BannerEdit = React.lazy(() => import('./views/banners/banner-edit'));
const ProductsEdit = React.lazy(() => import('./views/products/product-edit'));
const Units = React.lazy(() => import('./views/units'));
const UnitAdd = React.lazy(() => import('./views/units/unit-add'));
const UnitEdit = React.lazy(() => import('./views/units/unit-edit'));
const SellerProducts = React.lazy(() =>
  import('./views/seller-views/products/products')
);
const MyShopEdit = React.lazy(() =>
  import('./views/seller-views/my-shop/edit')
);
const SellerInvites = React.lazy(() => import('./views/seller-views/invites'));
const CurrencyEdit = React.lazy(() =>
  import('./views/currencies/currency-edit')
);
const CurrencyAdd = React.lazy(() =>
  import('./views/currencies/currencies-add')
);
const SellerDiscounts = React.lazy(() =>
  import('./views/seller-views/discounts')
);
const DiscountEdit = React.lazy(() =>
  import('./views/seller-views/discounts/discount-edit')
);
const DiscountAdd = React.lazy(() =>
  import('./views/seller-views/discounts/discount-add')
);
const SellerProductAdd = React.lazy(() =>
  import('./views/seller-views/products/products-add')
);
const SellerProductEdit = React.lazy(() =>
  import('./views/seller-views/products/product-edit')
);
const NotificationEdit = React.lazy(() =>
  import('./views/notification/notification-edit')
);
const Payments = React.lazy(() => import('./views/payments'));

// user
const User = React.lazy(() => import('./views/user/user'));
const Users = React.lazy(() => import('./views/user'));
const UserEdit = React.lazy(() => import('./views/user/user-edit'));
const UserAdd = React.lazy(() => import('./views/user/user-add'));
const UserAddRole = React.lazy(() => import('./views/user/user-add-role'));
const UserClone = React.lazy(() => import('./views/user/user-clone'));
const UserDetail = React.lazy(() => import('./views/user/user-detail'));
const ShopUsers = React.lazy(() =>
  import('./views/seller-views/user/shop-users')
);

const FAQ = React.lazy(() => import('./views/faq'));
const FaqAdd = React.lazy(() => import('./views/faq/faq-add'));
const FaqEdit = React.lazy(() => import('./views/faq/faq-edit'));
const Transactions = React.lazy(() => import('./views/transactions'));
const SmsGateways = React.lazy(() => import('./views/sms-gateways'));
const SellerDelivery = React.lazy(() =>
  import('./views/seller-views/delivery')
);
const SellerDeliveryAdd = React.lazy(() =>
  import('./views/seller-views/delivery/delivery-add')
);
const SellerDeliverymans = React.lazy(() =>
  import('./views/seller-views/delivery/deliverymans')
);
const SellerPosSystem = React.lazy(() =>
  import('./views/seller-views/pos-system')
);

const SellerBranch = React.lazy(() => import('./views/seller-views/branch'));
const SellerBranchAdd = React.lazy(() =>
  import('./views/seller-views/branch/branch-add')
);
const SellerBranchEdit = React.lazy(() =>
  import('./views/seller-views/branch/branch-edit')
);

const SellerOrder = React.lazy(() =>
  import('./views/seller-views/order/order')
);
const SellerOrderDetails = React.lazy(() =>
  import('./views/seller-views/order/order-details')
);
const Terms = React.lazy(() => import('./views/privacy/terms'));
const Policy = React.lazy(() => import('./views/privacy/policy'));

// review
const Reviews = React.lazy(() => import('./views/reviews'));
const ProductReviews = React.lazy(() =>
  import('./views/reviews/productReviews')
);
const OrderReviews = React.lazy(() => import('./views/reviews/orderReviews'));
const DeliveryBoyReviews = React.lazy(() =>
  import('./views/reviews/deliveryBoyReviews')
);

// seller review
const SellerReviews = React.lazy(() => import('./views/seller-views/reviews'));
const SellerProductReviews = React.lazy(() =>
  import('./views/seller-views/reviews/productReviews')
);
const SellerOrderReviews = React.lazy(() =>
  import('./views/seller-views/reviews/orderReviews')
);
const SellerDeliveryBoyReviews = React.lazy(() =>
  import('./views/seller-views/reviews/deliveryBoyReviews')
);

const Update = React.lazy(() => import('./views/update'));
const FirebaseConfig = React.lazy(() =>
  import('./views/settings/firebaseConfig')
);
const Wallets = React.lazy(() => import('./views/wallet'));
const SellerPayouts = React.lazy(() => import('./views/seller-views/payouts'));
const PayoutRequests = React.lazy(() => import('./views/payout-requests'));
const SocialSettings = React.lazy(() =>
  import('./views/settings/socialSettings')
);
const SellerSubscriptions = React.lazy(() =>
  import('./views/seller-views/subscriptions')
);
const DeliverymanOrders = React.lazy(() =>
  import('./views/deliveryman-orders')
);
const DeliverymanOrderDetails = React.lazy(() =>
  import('./views/deliveryman-orders/order-details')
);
const Cashback = React.lazy(() => import('./views/cashback'));
const BrandImport = React.lazy(() => import('./views/brands/brand-import'));
const CategoryImport = React.lazy(() =>
  import('./views/categories/category-import')
);
const ShopCategoryImport = React.lazy(() =>
  import('./views/shop-categories/category-import')
);
const ProductImport = React.lazy(() =>
  import('./views/products/product-import')
);
const SellerProductImport = React.lazy(() =>
  import('./views/seller-views/products/product-import')
);
const Storeis = React.lazy(() => import('./views/seller-views/story'));
const StoresAdd = React.lazy(() =>
  import('./views/seller-views/story/story-add')
);
const StoreisEdit = React.lazy(() =>
  import('./views/seller-views/story/story-edit')
);
const Restaurants = React.lazy(() => import('./views/restaurant'));
const RestaurantAdd = React.lazy(() =>
  import('./views/restaurant/restaurant-add')
);
const OrderStatus = React.lazy(() => import('./views/order-status'));
const ShopsAdd = React.lazy(() => import('./views/shops/shops-add'));
const Shops = React.lazy(() => import('./views/shops'));
const Story = React.lazy(() => import('./views/story'));

const ProductBonus = React.lazy(() =>
  import('./views/seller-views/product-bonus')
);
const ShopBonus = React.lazy(() => import('./views/seller-views/shop-bonus'));
const ProductBonusAdd = React.lazy(() =>
  import('./views/seller-views/product-bonus/product-bonus-add')
);
const ProductBonusEdit = React.lazy(() =>
  import('./views/seller-views/product-bonus/product-bonus-edit')
);
const ShopBonusAdd = React.lazy(() =>
  import('./views/seller-views/shop-bonus/shop-bonus-add')
);
const ShopBonusEdit = React.lazy(() =>
  import('./views/seller-views/shop-bonus/shop-bonus-edit')
);
const Bonus = React.lazy(() => import('./views/seller-views/bonus'));
const Subscriber = React.lazy(() => import('./views/subscriber'));
const EmailSubscriber = React.lazy(() => import('./views/email-subscribers'));
const MessageSubciribed = React.lazy(() =>
  import('./views/message-subscribers')
);
const MessageSubciribedAdd = React.lazy(() =>
  import('./views/message-subscribers/subciribed-add')
);
const MessageSubciribedEdit = React.lazy(() =>
  import('./views/message-subscribers/subciribed-edit')
);
const ExtraValue = React.lazy(() =>
  import('./views/products/Extras/extra-value')
);
const ExtraGroup = React.lazy(() =>
  import('./views/products/Extras/extra-group')
);
const SellerCategoryAdd = React.lazy(() =>
  import('./views/seller-views/categories/category-add')
);
const SellerCategoryEdit = React.lazy(() =>
  import('./views/seller-views/categories/category-edit')
);

const SellerPayment = React.lazy(() => import('./views/seller-views/payment'));
const SellerPaymentAdd = React.lazy(() =>
  import('./views/seller-views/payment/payment-add')
);
const SellerPaymentEdit = React.lazy(() =>
  import('./views/seller-views/payment/payment-edit')
);
const SellerBrandAdd = React.lazy(() =>
  import('./views/seller-views/brands/brand-add')
);
const SellerBrandEdit = React.lazy(() =>
  import('./views/seller-views/brands/brand-edit')
);
const ShopsEdit = React.lazy(() => import('./views/shops/shop-edit'));
const RestaurantEdit = React.lazy(() =>
  import('./views/restaurant/restaurant-edit')
);
const ProductsClone = React.lazy(() =>
  import('./views/products/product-clone')
);
const SellerProductsClone = React.lazy(() =>
  import('./views/seller-views/products/product-clone')
);
const Refunds = React.lazy(() => import('./views/refund'));
const RefundDetails = React.lazy(() => import('./views/refund/refund-details'));
const SellerRefunds = React.lazy(() => import('./views/seller-views/refund'));
const SellerRefundDetails = React.lazy(() =>
  import('./views/seller-views/refund/refund-details')
);

const Report = React.lazy(() => import('./views/report'));
const Overview = React.lazy(() => import('./views/report-overview'));
const ReportProducts = React.lazy(() => import('./views/report-products'));
const ReportRevenue = React.lazy(() => import('./views/report-revenue'));
const ReportOrders = React.lazy(() => import('./views/report-orders'));
const ReportVariation = React.lazy(() => import('./views/report-variation'));
const ReportCategories = React.lazy(() => import('./views/report-categories'));
const ReportStock = React.lazy(() => import('./views/report-stock'));
const ReportExtras = React.lazy(() => import('./views/report-extras'));
const SellerTransactions = React.lazy(() =>
  import('./views/seller-views/transactions')
);

const DeliveriesMap = React.lazy(() =>
  import('./views/deliveriesMap/deliveriesMap')
);
const DeliveriesList = React.lazy(() =>
  import('./views/deliveryList/deliveriesList')
);
const DeliveryOrders = React.lazy(() => import('./views/delivery-orders'));
const CashClear = React.lazy(() => import('./views/cache'));

const Addons = React.lazy(() => import('./views/addons'));
const AddonsImport = React.lazy(() => import('./views/addons/addons-import'));
const AddonAdd = React.lazy(() => import('./views/addons/addons-add'));
const AddonEdit = React.lazy(() => import('./views/addons/addons-edit'));
const AddonClone = React.lazy(() => import('./views/addons/addons-clone'));
const CategoryClone = React.lazy(() =>
  import('./views/categories/category-clone')
);
const ShopCategoryClone = React.lazy(() =>
  import('./views/shop-categories/category-clone')
);
const BrandClone = React.lazy(() => import('./views/brands/brands-clone'));
const ShopClone = React.lazy(() => import('./views/shops/shop-clone'));
const RestaurantClone = React.lazy(() =>
  import('./views/restaurant/restaurant-clone')
);
const DeliveryManOrder = React.lazy(() =>
  import('./views/delivery-orders/order-delivery')
);
const DeliveryStatistics = React.lazy(() =>
  import('./views/delivery-statistics/delivery-statistics')
);

const DeliveryMapOrders = React.lazy(() =>
  import('./views/deliveriesMap/delivery-map-orders')
);
const SellerAddons = React.lazy(() => import('./views/seller-views/addons'));
const SellerAddonClone = React.lazy(() =>
  import('./views/seller-views/addons/addons-clone')
);
const SellerAddonsImport = React.lazy(() =>
  import('./views/seller-views/addons/addons-import')
);
const SellerddonEdit = React.lazy(() =>
  import('./views/seller-views/addons/addons-edit')
);
const SellerAddonAdd = React.lazy(() =>
  import('./views/seller-views/addons/addons-add')
);
const BannerClone = React.lazy(() => import('./views/banners/banner-clone'));
const ReferralSetting = React.lazy(() =>
  import('./views/settings/referral-setting')
);

// blog
const Blogs = React.lazy(() => import('./views/blog'));
const BlogEdit = React.lazy(() => import('./views/blog/blog-edit'));
const BlogClone = React.lazy(() => import('./views/blog/blog-clone'));
const BlogAdd = React.lazy(() => import('./views/blog/blog-add'));

const SellerExtraGroup = React.lazy(() =>
  import('./views/seller-views/products/Extras/extra-group')
);
const SellerExtraValue = React.lazy(() =>
  import('./views/seller-views/products/Extras/extra-value')
);
const Tag = React.lazy(() => import('./views/shop-tag'));
const TagAdd = React.lazy(() => import('./views/shop-tag/tag-add'));
const TagEdit = React.lazy(() => import('./views/shop-tag/tag-edit'));
const TagClone = React.lazy(() => import('./views/shop-tag/tag-clone'));
const AdminPayouts = React.lazy(() => import('./views/admin-payouts'));

// email provider settings
const EmailProvider = React.lazy(() => import('./views/email-provider'));
const EmailProviderAdd = React.lazy(() =>
  import('./views/email-provider/email-add')
);
const EmailProviderEdit = React.lazy(() =>
  import('./views/email-provider/email-edit')
);
const ExtrasList = React.lazy(() => import('./views/extras'));
const OrderBoard = React.lazy(() => import('./views/order/order-board'));
const Order = React.lazy(() => import('./views/order/order-list'));
const Login = React.lazy(() => import('./views/login'));
const Dashboard = React.lazy(() => import('./views/dashboard'));

const PaymentPayload = React.lazy(() => import('./views/payment-payloads'));
const PaymentPayloadAdd = React.lazy(() =>
  import('./views/payment-payloads/payload-add')
);
const PaymentPayloadEdit = React.lazy(() =>
  import('./views/payment-payloads/payload-edit')
);

const Reciepts = React.lazy(() => import('./views/recepts'));
const RecieptAdd = React.lazy(() => import('./views/recepts/recept-add'));
const RecieptEdit = React.lazy(() => import('./views/recepts/recept-edit'));

const SellerReciepts = React.lazy(() => import('./views/seller-views/recepts'));
const SellerRecieptAdd = React.lazy(() =>
  import('./views/seller-views/recepts/recept-add')
);
const SellerRecieptEdit = React.lazy(() =>
  import('./views/seller-views/recepts/recept-edit')
);

const RecipeCategories = React.lazy(() => import('./views/recipe-categories'));
const RecipeCategoryAdd = React.lazy(() =>
  import('./views/recipe-categories/category-add')
);
const RecipeCategoryEdit = React.lazy(() =>
  import('./views/recipe-categories/category-edit')
);
const RecipeCategoryClone  = React.lazy(() => import('./views/recipe-categories/category-clone'))
const RecipeCategoryImport = React.lazy(() => import('./views/recipe-categories/category-import'))

const SellerRecipeCategories = React.lazy(() => import('./views/seller-views/recipe-categories'));
const SellerRecipeCategoryAdd = React.lazy(() =>
  import('./views/recipe-categories/category-add')
);
const SellerRecipeCategoryEdit = React.lazy(() =>
  import('./views/seller-views/recipe-categories/category-edit')
);
const SellerRecipeCategoryClone  = React.lazy(() => import('./views/seller-views/recipe-categories/category-clone'))
const SellerRecipeCategoryImport = React.lazy(() => import('./views/seller-views/recipe-categories/category-import'))

const App = () => {
  const [loading, setLoading] = useState(false);

  function fetchTranslations() {
    const params = { lang: i18n.language };
    setLoading(true);
    informationService
      .translations(params)
      .then(({ data }) =>
        i18n.addResourceBundle(i18n.language, 'translation', data)
      )
      .finally(() => setLoading(false));
  }

  useEffect(() => {
    fetchTranslations();
  }, []);

  return (
    <Providers>
      <Router>
        <Routes>
          <Route
            index
            path='/login'
            element={
              <PathLogout>
                <Suspense fallback={<Loading />}>
                  <Login />
                </Suspense>
              </PathLogout>
            }
          />

          <Route
            path='/welcome'
            element={
              <WelcomeLayout>
                <Suspense fallback={<Loading />}>
                  <Welcome />
                </Suspense>
              </WelcomeLayout>
            }
          />
          <Route
            path='/installation'
            element={
              <WelcomeLayout>
                <Suspense fallback={<Loading />}>
                  <GlobalSettings />
                </Suspense>
              </WelcomeLayout>
            }
          />

          <Route
            path=''
            element={
              <ProtectedRoute>
                <AppLayout />
              </ProtectedRoute>
            }
          >
            {/*app  page*/}
            <Route path='/' element={<Navigate to='dashboard' />} />
            <Route
              path='dashboard'
              element={
                <Suspense fallback={<Loading />}>
                  <Dashboard />
                </Suspense>
              }
            />
            <Route path='payouts' element={<AdminPayouts />} />
            <Route path='blogs' element={<Blogs />} />
            <Route path='shop-tag' element={<Tag />} />
            <Route path='restaurants' element={<Restaurants />} />
            <Route path='pos-system' element={<PosSystem />} />
            <Route path='currencies' element={<Currencies />} />
            <Route path='gallery' element={<Gallery />} />
            <Route path='shops' element={<Shops />} />
            <Route path='stories' element={<Story />} />
            <Route path='subscriptions' element={<Subscriptions />} />
            <Route path='email/subscriber' element={<EmailSubscriber />} />
            <Route path='subscriber' element={<Subscriber />} />
            <Route path='message/subscriber' element={<MessageSubciribed />} />
            <Route path='bonus/list' element={<BonusList />} />
            <Route path='orders' element={<Order />} />
            <Route path='orders/:type' element={<Order />} />
            <Route path='orders-board' element={<OrderBoard />} />
            <Route path='orders-board/:type' element={<OrderBoard />} />
            <Route path='orders/generate-invoice/:id' element={<Check />} />
            <Route
              path='seller/generate-invoice/:id'
              element={<SellerCheck />}
            />
            <Route path='banners' element={<Banners />} />
            <Route path='notifications' element={<Notification />} />
            <Route path='chat' element={<Chat />} />
            <Route path='coupons' element={<Coupon />} />
            <Route path='my-shop' element={<MyShop />} />
            <Route path='transactions' element={<Transactions />} />
            <Route path='wallets' element={<Wallets />} />
            <Route path='payout-requests' element={<PayoutRequests />} />
            <Route path='refunds' element={<Refunds />} />
            <Route path='deliveries' element={<Delivery />} />

            <Route path='reviews' element={<Reviews />} />
            <Route path='reviews/product' element={<ProductReviews />} />
            <Route path='reviews/order' element={<OrderReviews />} />
            <Route
              path='reviews/deliveryboy'
              element={<DeliveryBoyReviews />}
            />

            {/* settings */}
            <Route path='settings' element={<Settings />} />
            <Route path='settings/general' element={<GeneralSettings />} />
            <Route path='settings/referal' element={<ReferralSetting />} />
            <Route path='settings/languages' element={<Languages />} />
            <Route path='settings/translations' element={<Translations />} />
            <Route path='settings/backup' element={<Backup />} />
            <Route path='settings/cashClear' element={<CashClear />} />
            <Route
              path='settings/system-information'
              element={<SystemInformation />}
            />
            <Route path='settings/payments' element={<Payments />} />
            <Route path='settings/faqs' element={<FAQ />} />
            <Route path='settings/sms-gateways' element={<SmsGateways />} />
            <Route path='settings/terms' element={<Terms />} />
            <Route path='settings/policy' element={<Policy />} />
            <Route path='settings/update' element={<Update />} />
            <Route path='settings/firebase' element={<FirebaseConfig />} />
            <Route path='settings/social' element={<SocialSettings />} />
            <Route path='settings/app' element={<AppSettings />} />
            <Route path='settings/orderStatus' element={<OrderStatus />} />

            {/* email provider settings */}
            <Route path='settings/emailProviders' element={<EmailProvider />} />
            <Route
              path='settings/emailProviders/add'
              element={<EmailProviderAdd />}
            />
            <Route
              path='settings/emailProviders/:id'
              element={<EmailProviderEdit />}
            />

            {/* catalog */}
            <Route path='catalog' element={<Catalog />} />
            <Route path='delivery' element={<Delivery />} />
            <Route path='catalog/products' element={<Products />} />
            <Route path='catalog/product/import' element={<ProductImport />} />
            <Route path='catalog/addon/import' element={<AddonsImport />} />
            <Route path='catalog/addons' element={<Addons />} />
            <Route path='catalog/recept' element={<Reciepts />} />
            <Route path='catalog/addons/const' element={<AddonsImport />} />
            <Route path='catalog/extras/list' element={<ExtrasList />} />
            <Route path='catalog/extras' element={<ExtraGroup />} />
            <Route path='catalog/extras/value' element={<ExtraValue />} />
            <Route path='catalog/categories' element={<Categories />} />
            <Route path='catalog/recipe-categories' element={<RecipeCategories />} />
            <Route
              path='catalog/categories/import'
              element={<CategoryImport />}
            />
            <Route
              path='catalog/shop/categories'
              element={<ShopCategories />}
            />
            <Route
              path='catalog/shop/categories/import'
              element={<ShopCategoryImport />}
            />
            <Route path='catalog/brands' element={<Brands />} />
            <Route path='catalog/brands/const' element={<BrandImport />} />
            <Route path='catalog/units' element={<Units />} />
            {/* Reports admin */}
            <Route path='report' element={<Report />} />
            <Route path='report/overview' element={<Overview />} />
            <Route path='report/products' element={<ReportProducts />} />
            <Route path='report/revenue' element={<ReportRevenue />} />
            <Route path='report/orders' element={<ReportOrders />} />
            <Route path='report/variation' element={<ReportVariation />} />
            <Route path='report/categories' element={<ReportCategories />} />
            <Route path='report/stock' element={<ReportStock />} />
            <Route path='report/extras' element={<ReportExtras />} />
            {/* bonus */}
            <Route path='seller/bonus' element={<Bonus />} />
            <Route path='seller/bonus/product' element={<ProductBonus />} />
            <Route
              path='seller/product-bonus/add'
              element={<ProductBonusAdd />}
            />
            <Route
              path='seller/product-bonus/:id'
              element={<ProductBonusEdit />}
            />
            <Route path='seller/branch' element={<SellerBranch />} />
            <Route path='seller/branch/add' element={<SellerBranchAdd />} />
            <Route path='seller/branch/:id' element={<SellerBranchEdit />} />
            <Route path='seller/bonus/shop' element={<ShopBonus />} />
            <Route path='seller/shop-bonus/add' element={<ShopBonusAdd />} />
            <Route path='seller/shop-bonus/:id' element={<ShopBonusEdit />} />
            <Route path='seller/payments' element={<SellerPayment />} />
            <Route path='seller/payments/add' element={<SellerPaymentAdd />} />
            <Route path='seller/payments/:id' element={<SellerPaymentEdit />} />
            <Route
              path='seller/transactions'
              element={<SellerTransactions />}
            />
            <Route path='seller/stories' element={<Storeis />} />
            <Route path='seller/story/add' element={<StoresAdd />} />
            <Route path='seller/story/:id' element={<StoreisEdit />} />

            <Route path='seller/categories' element={<SellerCategories />} />
            <Route path='seller/category/add' element={<SellerCategoryAdd />} />
            <Route
              path='seller/category/:uuid'
              element={<SellerCategoryEdit />}
            />
            
            <Route path='seller/brands' element={<SellerBrands />} />
            <Route path='seller/refunds' element={<SellerRefunds />} />
            <Route
              path='seller/refund/details/:id'
              element={<SellerRefundDetails />}
            />
            <Route path='seller/brand/add' element={<SellerBrandAdd />} />
            <Route path='seller/brand/:id' element={<SellerBrandEdit />} />
            <Route path='seller/products' element={<SellerProducts />} />
            <Route path='seller/addons' element={<SellerAddons />} />
            <Route path='seller/invites' element={<SellerInvites />} />
            <Route path='seller/discounts' element={<SellerDiscounts />} />
            <Route path='seller/product/add' element={<SellerProductAdd />} />
            <Route path='seller/addon/add' element={<SellerAddonAdd />} />
            <Route
              path='seller/product/:uuid'
              element={<SellerProductEdit />}
            />
            <Route path='extras' element={<SellerExtraGroup />} />
            <Route path='extras/value' element={<SellerExtraValue />} />
            <Route path='seller/addon/:uuid' element={<SellerddonEdit />} />
            <Route
              path='seller/product-clone/:uuid'
              element={<SellerProductsClone />}
            />
            <Route
              path='seller/addon-clone/:uuid'
              element={<SellerAddonClone />}
            />
            <Route
              path='seller/product/import'
              element={<SellerProductImport />}
            />
            <Route
              path='seller/addon/import'
              element={<SellerAddonsImport />}
            />
            <Route path='seller/delivery/list' element={<SellerDelivery />} />
            <Route
              path='seller/deliveries/add'
              element={<SellerDeliveryAdd />}
            />
            <Route path='seller/delivery/:id' element={<SellerDeliveryAdd />} />
            <Route
              path='seller/delivery/deliveryman'
              element={<SellerDeliverymans />}
            />
            <Route path='seller/pos-system' element={<SellerPosSystem />} />

            <Route path='seller/recept' element={<SellerReciepts />} />
            <Route path='seller/recept/add' element={<SellerRecieptAdd />} />
            <Route path='seller/recept/edit/:id' element={<SellerRecieptEdit />} />

            <Route path='seller/orders' element={<SellerOrder />} />
            <Route path='seller/orders/:type' element={<SellerOrder />} />
            <Route path='seller/orders-board' element={<SellerOrdersBoard />} />
            <Route
              path='seller/orders-board/:type'
              element={<SellerOrdersBoard />}
            />
            <Route path='seller/order/:id' element={<SellerOrderEdit />} />
            <Route
              path='seller/order/details/:id'
              element={<SellerOrderDetails />}
            />
            <Route path='seller/shop-users' element={<ShopUsers />} />
            <Route path='seller/payouts' element={<SellerPayouts />} />
            <Route
              path='seller/subscriptions'
              element={<SellerSubscriptions />}
            />
            <Route path='seller/recipe-categories' element={<SellerRecipeCategories />} />
            <Route path="seller/recipe-category/add" element={<SellerRecipeCategoryAdd />} />
            <Route path="seller/recipe-category/edit/:uuid" element={<SellerRecipeCategoryEdit />} />
            <Route path='seller/recipe-category-clone/:uuid' element={<SellerRecipeCategoryClone />} />
            <Route path="seller/recipe-categories/import" element={<SellerRecipeCategoryImport />} />
            {/* seller review */}
            <Route path='seller/reviews' element={<SellerReviews />} />
            <Route
              path='seller/reviews/product'
              element={<SellerProductReviews />}
            />
            <Route
              path='seller/reviews/order'
              element={<SellerOrderReviews />}
            />
            <Route
              path='seller/reviews/deliveryboy'
              element={<SellerDeliveryBoyReviews />}
            />

            {/* delivery */}
            <Route path='deliveries/list' element={<DeliveriesList />} />
            <Route path='deliveries/map' element={<DeliveriesMap />} />
            <Route path='deliveries/map/:id' element={<DeliveryMapOrders />} />
            <Route path='delivery/orders/:id' element={<DeliveryManOrder />} />
            <Route path='delivery/orders' element={<DeliveryOrders />} />
            <Route
              path='delivery/statistics'
              element={<DeliveryStatistics />}
            />
            <Route path='deliveryman/orders' element={<DeliverymanOrders />} />
            <Route
              path='deliveryman/order/details/:id'
              element={<DeliverymanOrderDetails />}
            />
            <Route path='cashback' element={<Cashback />} />
            {/* user */}
            <Route path='users' element={<Users />} />
            <Route path='users/user' element={<User />} />
            <Route path='users/admin' element={<Admin />} />
            <Route path='users/user/:id' element={<UserDetail />} />
            <Route path='users/role' element={<RoleList />} />
            {/*app add for page*/}
            <Route path='restaurant/add' element={<RestaurantAdd />} />
            <Route
              path='message/subscriber/add'
              element={<MessageSubciribedAdd />}
            />
            <Route path='shop/add' element={<ShopsAdd />} />
            <Route path='brand/add' element={<BrandsAdd />} />
            <Route path='category/add' element={<CategoryAdd />} />
            <Route path='shop/category/add' element={<ShopCategoryAdd />} />
            <Route path='product/add' element={<ProductsAdd />} />
            <Route path='addon/add' element={<AddonAdd />} />
            <Route path='language/add' element={<LanguageAdd />} />
            <Route path='currency/add' element={<CurrencyAdd />} />
            <Route path='order/add' element={<OrdersAdd />} />
            <Route path='user/add' element={<UserAdd />} />
            <Route path='user/add/:role' element={<UserAddRole />} />
            <Route path='add/user/delivery/:role' element={<UserAddRole />} />
            <Route path='banner/add' element={<BannerAdd />} />
            <Route path='notification/add' element={<NotificationAdd />} />
            <Route path='coupon/add' element={<CouponAdd />} />
            <Route path='unit/add' element={<UnitAdd />} />
            <Route path='discount/add' element={<DiscountAdd />} />
            <Route path='faq/add' element={<FaqAdd />} />
            <Route path='blog/add' element={<BlogAdd />} />
            <Route path='shop-tag/add' element={<TagAdd />} />

            {/*app edit for page*/}
            <Route path='language/:id' element={<LanguageEdit />} />
            <Route path='category/:uuid' element={<CategoryEdit />} />
            <Route path='category-clone/:uuid' element={<CategoryClone />} />
            <Route
              path='message/subscriber/:id'
              element={<MessageSubciribedEdit />}
            />
            <Route path='shop/category/:uuid' element={<ShopCategoryEdit />} />
            <Route
              path='shop/category-clone/:uuid'
              element={<ShopCategoryClone />}
            />
            <Route path='currency/:id' element={<CurrencyEdit />} />
            <Route path='brand/:id' element={<BrandsEdit />} />
            <Route path='brand-clone/:id' element={<BrandClone />} />
            <Route path='restaurant/:uuid' element={<RestaurantEdit />} />
            <Route
              path='restaurant-clone/:uuid'
              element={<RestaurantClone />}
            />
            <Route path='coupon/:id' element={<CouponEdit />} />
            <Route path='shop/:uuid' element={<ShopsEdit />} />
            <Route path='shop-clone/:uuid' element={<ShopClone />} />
            <Route path='product/:uuid' element={<ProductsEdit />} />
            <Route path='addon/:uuid' element={<AddonEdit />} />
            <Route path='product-clone/:uuid' element={<ProductsClone />} />
            <Route path='addon-clone/:uuid' element={<AddonClone />} />
            <Route path='subscriptions/edit' element={<SubscriptionsEdit />} />
            <Route path='order/details/:id' element={<OrderDetails />} />
            <Route path='refund/details/:id' element={<RefundDetails />} />
            <Route path='order/:id' element={<OrderEdit />} />
            <Route path='user/:uuid' element={<UserEdit />} />
            <Route path='user/delivery/:uuid' element={<UserEdit />} />
            <Route path='user-clone/:uuid' element={<UserClone />} />
            <Route path='banner/:id' element={<BannerEdit />} />
            <Route path='banner/clone/:id' element={<BannerClone />} />
            <Route path='blog/:uuid' element={<BlogEdit />} />
            <Route path='blog/clone/:uuid' element={<BlogClone />} />
            <Route path='shop-tag/:id' element={<TagEdit />} />
            <Route path='shop-tag/clone/:id' element={<TagClone />} />
            <Route path='notification/:uuid' element={<NotificationEdit />} />
            <Route
              path='notification-clone/:uuid'
              element={<NotificationClone />}
            />
            <Route path='coupon/:id' element={<CouponAdd />} />
            <Route path='unit/:id' element={<UnitEdit />} />
            <Route path='my-shop/edit' element={<MyShopEdit />} />
            <Route path='discount/:id' element={<DiscountEdit />} />
            <Route path='faq/:uuid' element={<FaqEdit />} />
            <Route path='gallery/:type' element={<GalleryLanguages />} />
            <Route path='payment-payloads' element={<PaymentPayload />} />
            <Route
              path='payment-payloads/add'
              element={<PaymentPayloadAdd />}
            />
            <Route
              path='payment-payloads/edit/:id'
              element={<PaymentPayloadEdit />}
            />
            <Route path='recept/edit/:id' element={<RecieptEdit />} />
            <Route path='recept/add' element={<RecieptAdd />} />
            <Route path="recipe-category/add" element={<RecipeCategoryAdd />} />
            <Route path="recipe-category/edit/:uuid" element={<RecipeCategoryEdit />} />
            <Route path='recipe-category-clone/:uuid' element={<RecipeCategoryClone />} />
            <Route path="recipe-categories/import" element={<RecipeCategoryImport />} />
          </Route>

          <Route
            path='*'
            element={
              <Suspense fallback={<Loading />}>
                <NotFound />
              </Suspense>
            }
          />
        </Routes>
        <ToastContainer
          className='antd-toast'
          position='top-right'
          autoClose={3000}
          hideProgressBar
          closeOnClick
          pauseOnHover
          draggable
        />
        {loading && <PageLoading />}
      </Router>
    </Providers>
  );
};
export default App;
