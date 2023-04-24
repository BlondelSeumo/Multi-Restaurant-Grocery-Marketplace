import 'package:auto_route/auto_route.dart';
import 'package:riverpodtemp/presentation/pages/auth/login/login_page.dart';
import 'package:riverpodtemp/presentation/pages/auth/confirmation/register_confirmation_page.dart';
import 'package:riverpodtemp/presentation/pages/auth/register/register_page.dart';
import 'package:riverpodtemp/presentation/pages/auth/reset/reset_password_page.dart';
import 'package:riverpodtemp/presentation/pages/generate_image/generate_image_page.dart';
import 'package:riverpodtemp/presentation/pages/initial/no_connection/no_connection_page.dart';
import 'package:riverpodtemp/presentation/pages/initial/splash/splash_page.dart';
import 'package:riverpodtemp/presentation/pages/main/main_page.dart';
import 'package:riverpodtemp/presentation/pages/order/order_screen/order_screen.dart';
import 'package:riverpodtemp/presentation/pages/order/orders_page.dart';
import 'package:riverpodtemp/presentation/pages/profile/wallet_history.dart';
import 'package:riverpodtemp/presentation/pages/setting/setting_page.dart';
import 'package:riverpodtemp/presentation/pages/shop/shop_detail.dart';
import 'package:riverpodtemp/presentation/pages/shop/shop_page.dart';
import 'package:riverpodtemp/presentation/pages/view_map/map_search_page.dart';
import 'package:riverpodtemp/presentation/pages/view_map/view_map_page.dart';

import '../pages/chat/chat/chat_page.dart';
import '../pages/home/filter/result_filter.dart';
import '../pages/home/widgets/shops_banner_page.dart';
import '../pages/order/order_screen/order_progress_screen.dart';
import '../pages/profile/become_seller/create_shop.dart';
import '../pages/profile/help_page.dart';
import '../pages/home/widgets/recommended_screen.dart';
import '../pages/profile/share_referral_faq.dart';
import '../pages/profile/share_referral_page.dart';
import '../pages/story_page/story_page.dart';

@MaterialAutoRouter(
  replaceInRouteName: 'Page,Route',
  routes: [
    MaterialRoute(
      path: '/',
      page: SplashPage,
    ),
    MaterialRoute(
      path: '/no-connection',
      page: NoConnectionPage,
    ),
    MaterialRoute(
      path: '/login',
      page: LoginPage,
    ),
    MaterialRoute(
      path: '/reset',
      page: ResetPasswordPage,
    ),
    MaterialRoute(
      path: '/register-confirmation',
      page: RegisterConfirmationPage,
    ),
    MaterialRoute(
      path: '/register',
      page: RegisterPage,
    ),
    MaterialRoute(
      path: '/main',
      page: MainPage,
    ),
    MaterialRoute(
      path: '/shop',
      page: ShopPage,
    ),
    MaterialRoute(
      path: '/order',
      page: OrdersListPage,
    ),
    MaterialRoute(
      path: '/setting',
      page: SettingPage,
    ),
    MaterialRoute(
      path: '/orderScreen',
      page: OrderScreen,
    ),
    MaterialRoute(
      path: '/map',
      page: ViewMapPage,
    ),
    MaterialRoute(
      path: "/storyList",
      page: StoryList,
    ),
    MaterialRoute(
      path: '/recommended',
      page: RecommendedPage,
    ),
    MaterialRoute(
      path: '/map_search',
      page: MapSearchPage,
    ),
    MaterialRoute(
      path: '/help',
      page: HelpPage,
    ),
    MaterialRoute(
        path: '/order_progress',
        page: OrderProgressPage,
    ),
    MaterialRoute(
      path: '/result_filter',
      page: ResultFilter,
    ),
    MaterialRoute(
      path: '/wallet_history',
      page: WalletHistory,
    ),
    MaterialRoute(
      path: '/create_shop',
      page: CreateShop,
    ),
    MaterialRoute(
      path: '/shops_banner',
      page: ShopsBannerPage,
    ),
    MaterialRoute(
      path: '/shops_detail',
      page: ShopDetailPage,
    ),
    MaterialRoute(
      path: '/share_referral',
      page: ShareReferralPage,
    ),
    MaterialRoute(
      path: '/share_referral_faq',
      page: ShareReferralFaq,
    ),
    MaterialRoute(
      path: '/chat',
      page: ChatPage,
    ),
    MaterialRoute(
      path: '/generate_image',
      page: GenerateImagePage,
    )
  ],
)
class $AppRouter {}
