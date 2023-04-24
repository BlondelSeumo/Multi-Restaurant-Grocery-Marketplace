// **************************************************************************
// AutoRouteGenerator
// **************************************************************************

// GENERATED CODE - DO NOT MODIFY BY HAND

// **************************************************************************
// AutoRouteGenerator
// **************************************************************************
//
// ignore_for_file: type=lint

// ignore_for_file: no_leading_underscores_for_library_prefixes
import 'package:auto_route/auto_route.dart' as _i27;
import 'package:flutter/material.dart' as _i28;
import 'package:pull_to_refresh/pull_to_refresh.dart' as _i31;

import '../../infrastructure/models/data/shop_data.dart' as _i30;
import '../../infrastructure/models/data/user.dart' as _i29;
import '../pages/auth/confirmation/register_confirmation_page.dart' as _i5;
import '../pages/auth/login/login_page.dart' as _i3;
import '../pages/auth/register/register_page.dart' as _i6;
import '../pages/auth/reset/reset_password_page.dart' as _i4;
import '../pages/chat/chat/chat_page.dart' as _i25;
import '../pages/generate_image/generate_image_page.dart' as _i26;
import '../pages/home/filter/result_filter.dart' as _i18;
import '../pages/home/widgets/recommended_screen.dart' as _i14;
import '../pages/home/widgets/shops_banner_page.dart' as _i21;
import '../pages/initial/no_connection/no_connection_page.dart' as _i2;
import '../pages/initial/splash/splash_page.dart' as _i1;
import '../pages/main/main_page.dart' as _i7;
import '../pages/order/order_screen/order_progress_screen.dart' as _i17;
import '../pages/order/order_screen/order_screen.dart' as _i11;
import '../pages/order/orders_page.dart' as _i9;
import '../pages/profile/become_seller/create_shop.dart' as _i20;
import '../pages/profile/help_page.dart' as _i16;
import '../pages/profile/share_referral_faq.dart' as _i24;
import '../pages/profile/share_referral_page.dart' as _i23;
import '../pages/profile/wallet_history.dart' as _i19;
import '../pages/setting/setting_page.dart' as _i10;
import '../pages/shop/shop_detail.dart' as _i22;
import '../pages/shop/shop_page.dart' as _i8;
import '../pages/story_page/story_page.dart' as _i13;
import '../pages/view_map/map_search_page.dart' as _i15;
import '../pages/view_map/view_map_page.dart' as _i12;

class AppRouter extends _i27.RootStackRouter {
  AppRouter([_i28.GlobalKey<_i28.NavigatorState>? navigatorKey])
      : super(navigatorKey);

  @override
  final Map<String, _i27.PageFactory> pagesMap = {
    SplashRoute.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i1.SplashPage(),
      );
    },
    NoConnectionRoute.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i2.NoConnectionPage(),
      );
    },
    LoginRoute.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i3.LoginPage(),
      );
    },
    ResetPasswordRoute.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i4.ResetPasswordPage(),
      );
    },
    RegisterConfirmationRoute.name: (routeData) {
      final args = routeData.argsAs<RegisterConfirmationRouteArgs>();
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: _i5.RegisterConfirmationPage(
          key: args.key,
          userModel: args.userModel,
          isResetPassword: args.isResetPassword,
          verificationId: args.verificationId,
        ),
      );
    },
    RegisterRoute.name: (routeData) {
      final args = routeData.argsAs<RegisterRouteArgs>();
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: _i6.RegisterPage(
          key: args.key,
          isOnlyEmail: args.isOnlyEmail,
        ),
      );
    },
    MainRoute.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i7.MainPage(),
      );
    },
    ShopRoute.name: (routeData) {
      final args = routeData.argsAs<ShopRouteArgs>();
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: _i8.ShopPage(
          key: args.key,
          shopId: args.shopId,
          productId: args.productId,
          cartId: args.cartId,
          shop: args.shop,
          ownerId: args.ownerId,
        ),
      );
    },
    OrdersListRoute.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i9.OrdersListPage(),
      );
    },
    SettingRoute.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i10.SettingPage(),
      );
    },
    OrderScreen.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i11.OrderScreen(),
      );
    },
    ViewMapRoute.name: (routeData) {
      final args = routeData.argsAs<ViewMapRouteArgs>(
          orElse: () => const ViewMapRouteArgs());
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: _i12.ViewMapPage(
          key: args.key,
          isShopLocation: args.isShopLocation,
          shopId: args.shopId,
        ),
      );
    },
    StoryList.name: (routeData) {
      final args = routeData.argsAs<StoryListArgs>();
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: _i13.StoryList(
          key: args.key,
          index: args.index,
          controller: args.controller,
        ),
      );
    },
    RecommendedRoute.name: (routeData) {
      final args = routeData.argsAs<RecommendedRouteArgs>(
          orElse: () => const RecommendedRouteArgs());
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: _i14.RecommendedPage(
          key: args.key,
          isNewsOfPage: args.isNewsOfPage,
          isShop: args.isShop,
        ),
      );
    },
    MapSearchRoute.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i15.MapSearchPage(),
      );
    },
    HelpRoute.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i16.HelpPage(),
      );
    },
    OrderProgressRoute.name: (routeData) {
      final args = routeData.argsAs<OrderProgressRouteArgs>(
          orElse: () => const OrderProgressRouteArgs());
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: _i17.OrderProgressPage(
          key: args.key,
          orderId: args.orderId,
        ),
      );
    },
    ResultFilter.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i18.ResultFilter(),
      );
    },
    WalletHistory.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i19.WalletHistory(),
      );
    },
    CreateShop.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i20.CreateShop(),
      );
    },
    ShopsBannerRoute.name: (routeData) {
      final args = routeData.argsAs<ShopsBannerRouteArgs>();
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: _i21.ShopsBannerPage(
          key: args.key,
          bannerId: args.bannerId,
          title: args.title,
        ),
      );
    },
    ShopDetailRoute.name: (routeData) {
      final args = routeData.argsAs<ShopDetailRouteArgs>();
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: _i22.ShopDetailPage(
          key: args.key,
          shop: args.shop,
          workTime: args.workTime,
        ),
      );
    },
    ShareReferralRoute.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i23.ShareReferralPage(),
      );
    },
    ShareReferralFaq.name: (routeData) {
      final args = routeData.argsAs<ShareReferralFaqArgs>();
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: _i24.ShareReferralFaq(
          key: args.key,
          terms: args.terms,
        ),
      );
    },
    ChatRoute.name: (routeData) {
      final args = routeData.argsAs<ChatRouteArgs>();
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: _i25.ChatPage(
          key: args.key,
          roleId: args.roleId,
          name: args.name,
        ),
      );
    },
    GenerateImageRoute.name: (routeData) {
      return _i27.MaterialPageX<dynamic>(
        routeData: routeData,
        child: const _i26.GenerateImagePage(),
      );
    },
  };

  @override
  List<_i27.RouteConfig> get routes => [
        _i27.RouteConfig(
          SplashRoute.name,
          path: '/',
        ),
        _i27.RouteConfig(
          NoConnectionRoute.name,
          path: '/no-connection',
        ),
        _i27.RouteConfig(
          LoginRoute.name,
          path: '/login',
        ),
        _i27.RouteConfig(
          ResetPasswordRoute.name,
          path: '/reset',
        ),
        _i27.RouteConfig(
          RegisterConfirmationRoute.name,
          path: '/register-confirmation',
        ),
        _i27.RouteConfig(
          RegisterRoute.name,
          path: '/register',
        ),
        _i27.RouteConfig(
          MainRoute.name,
          path: '/main',
        ),
        _i27.RouteConfig(
          ShopRoute.name,
          path: '/shop',
        ),
        _i27.RouteConfig(
          OrdersListRoute.name,
          path: '/order',
        ),
        _i27.RouteConfig(
          SettingRoute.name,
          path: '/setting',
        ),
        _i27.RouteConfig(
          OrderScreen.name,
          path: '/orderScreen',
        ),
        _i27.RouteConfig(
          ViewMapRoute.name,
          path: '/map',
        ),
        _i27.RouteConfig(
          StoryList.name,
          path: '/storyList',
        ),
        _i27.RouteConfig(
          RecommendedRoute.name,
          path: '/recommended',
        ),
        _i27.RouteConfig(
          MapSearchRoute.name,
          path: '/map_search',
        ),
        _i27.RouteConfig(
          HelpRoute.name,
          path: '/help',
        ),
        _i27.RouteConfig(
          OrderProgressRoute.name,
          path: '/order_progress',
        ),
        _i27.RouteConfig(
          ResultFilter.name,
          path: '/result_filter',
        ),
        _i27.RouteConfig(
          WalletHistory.name,
          path: '/wallet_history',
        ),
        _i27.RouteConfig(
          CreateShop.name,
          path: '/create_shop',
        ),
        _i27.RouteConfig(
          ShopsBannerRoute.name,
          path: '/shops_banner',
        ),
        _i27.RouteConfig(
          ShopDetailRoute.name,
          path: '/shops_detail',
        ),
        _i27.RouteConfig(
          ShareReferralRoute.name,
          path: '/share_referral',
        ),
        _i27.RouteConfig(
          ShareReferralFaq.name,
          path: '/share_referral_faq',
        ),
        _i27.RouteConfig(
          ChatRoute.name,
          path: '/chat',
        ),
        _i27.RouteConfig(
          GenerateImageRoute.name,
          path: '/generate_image',
        ),
      ];
}

/// generated route for
/// [_i1.SplashPage]
class SplashRoute extends _i27.PageRouteInfo<void> {
  const SplashRoute()
      : super(
          SplashRoute.name,
          path: '/',
        );

  static const String name = 'SplashRoute';
}

/// generated route for
/// [_i2.NoConnectionPage]
class NoConnectionRoute extends _i27.PageRouteInfo<void> {
  const NoConnectionRoute()
      : super(
          NoConnectionRoute.name,
          path: '/no-connection',
        );

  static const String name = 'NoConnectionRoute';
}

/// generated route for
/// [_i3.LoginPage]
class LoginRoute extends _i27.PageRouteInfo<void> {
  const LoginRoute()
      : super(
          LoginRoute.name,
          path: '/login',
        );

  static const String name = 'LoginRoute';
}

/// generated route for
/// [_i4.ResetPasswordPage]
class ResetPasswordRoute extends _i27.PageRouteInfo<void> {
  const ResetPasswordRoute()
      : super(
          ResetPasswordRoute.name,
          path: '/reset',
        );

  static const String name = 'ResetPasswordRoute';
}

/// generated route for
/// [_i5.RegisterConfirmationPage]
class RegisterConfirmationRoute
    extends _i27.PageRouteInfo<RegisterConfirmationRouteArgs> {
  RegisterConfirmationRoute({
    _i28.Key? key,
    required _i29.UserModel userModel,
    bool isResetPassword = false,
    required String verificationId,
  }) : super(
          RegisterConfirmationRoute.name,
          path: '/register-confirmation',
          args: RegisterConfirmationRouteArgs(
            key: key,
            userModel: userModel,
            isResetPassword: isResetPassword,
            verificationId: verificationId,
          ),
        );

  static const String name = 'RegisterConfirmationRoute';
}

class RegisterConfirmationRouteArgs {
  const RegisterConfirmationRouteArgs({
    this.key,
    required this.userModel,
    this.isResetPassword = false,
    required this.verificationId,
  });

  final _i28.Key? key;

  final _i29.UserModel userModel;

  final bool isResetPassword;

  final String verificationId;

  @override
  String toString() {
    return 'RegisterConfirmationRouteArgs{key: $key, userModel: $userModel, isResetPassword: $isResetPassword, verificationId: $verificationId}';
  }
}

/// generated route for
/// [_i6.RegisterPage]
class RegisterRoute extends _i27.PageRouteInfo<RegisterRouteArgs> {
  RegisterRoute({
    _i28.Key? key,
    required bool isOnlyEmail,
  }) : super(
          RegisterRoute.name,
          path: '/register',
          args: RegisterRouteArgs(
            key: key,
            isOnlyEmail: isOnlyEmail,
          ),
        );

  static const String name = 'RegisterRoute';
}

class RegisterRouteArgs {
  const RegisterRouteArgs({
    this.key,
    required this.isOnlyEmail,
  });

  final _i28.Key? key;

  final bool isOnlyEmail;

  @override
  String toString() {
    return 'RegisterRouteArgs{key: $key, isOnlyEmail: $isOnlyEmail}';
  }
}

/// generated route for
/// [_i7.MainPage]
class MainRoute extends _i27.PageRouteInfo<void> {
  const MainRoute()
      : super(
          MainRoute.name,
          path: '/main',
        );

  static const String name = 'MainRoute';
}

/// generated route for
/// [_i8.ShopPage]
class ShopRoute extends _i27.PageRouteInfo<ShopRouteArgs> {
  ShopRoute({
    _i28.Key? key,
    required String shopId,
    String? productId,
    String? cartId,
    _i30.ShopData? shop,
    int? ownerId,
  }) : super(
          ShopRoute.name,
          path: '/shop',
          args: ShopRouteArgs(
            key: key,
            shopId: shopId,
            productId: productId,
            cartId: cartId,
            shop: shop,
            ownerId: ownerId,
          ),
        );

  static const String name = 'ShopRoute';
}

class ShopRouteArgs {
  const ShopRouteArgs({
    this.key,
    required this.shopId,
    this.productId,
    this.cartId,
    this.shop,
    this.ownerId,
  });

  final _i28.Key? key;

  final String shopId;

  final String? productId;

  final String? cartId;

  final _i30.ShopData? shop;

  final int? ownerId;

  @override
  String toString() {
    return 'ShopRouteArgs{key: $key, shopId: $shopId, productId: $productId, cartId: $cartId, shop: $shop, ownerId: $ownerId}';
  }
}

/// generated route for
/// [_i9.OrdersListPage]
class OrdersListRoute extends _i27.PageRouteInfo<void> {
  const OrdersListRoute()
      : super(
          OrdersListRoute.name,
          path: '/order',
        );

  static const String name = 'OrdersListRoute';
}

/// generated route for
/// [_i10.SettingPage]
class SettingRoute extends _i27.PageRouteInfo<void> {
  const SettingRoute()
      : super(
          SettingRoute.name,
          path: '/setting',
        );

  static const String name = 'SettingRoute';
}

/// generated route for
/// [_i11.OrderScreen]
class OrderScreen extends _i27.PageRouteInfo<void> {
  const OrderScreen()
      : super(
          OrderScreen.name,
          path: '/orderScreen',
        );

  static const String name = 'OrderScreen';
}

/// generated route for
/// [_i12.ViewMapPage]
class ViewMapRoute extends _i27.PageRouteInfo<ViewMapRouteArgs> {
  ViewMapRoute({
    _i28.Key? key,
    bool isShopLocation = false,
    int? shopId,
  }) : super(
          ViewMapRoute.name,
          path: '/map',
          args: ViewMapRouteArgs(
            key: key,
            isShopLocation: isShopLocation,
            shopId: shopId,
          ),
        );

  static const String name = 'ViewMapRoute';
}

class ViewMapRouteArgs {
  const ViewMapRouteArgs({
    this.key,
    this.isShopLocation = false,
    this.shopId,
  });

  final _i28.Key? key;

  final bool isShopLocation;

  final int? shopId;

  @override
  String toString() {
    return 'ViewMapRouteArgs{key: $key, isShopLocation: $isShopLocation, shopId: $shopId}';
  }
}

/// generated route for
/// [_i13.StoryList]
class StoryList extends _i27.PageRouteInfo<StoryListArgs> {
  StoryList({
    _i28.Key? key,
    required int index,
    required _i31.RefreshController controller,
  }) : super(
          StoryList.name,
          path: '/storyList',
          args: StoryListArgs(
            key: key,
            index: index,
            controller: controller,
          ),
        );

  static const String name = 'StoryList';
}

class StoryListArgs {
  const StoryListArgs({
    this.key,
    required this.index,
    required this.controller,
  });

  final _i28.Key? key;

  final int index;

  final _i31.RefreshController controller;

  @override
  String toString() {
    return 'StoryListArgs{key: $key, index: $index, controller: $controller}';
  }
}

/// generated route for
/// [_i14.RecommendedPage]
class RecommendedRoute extends _i27.PageRouteInfo<RecommendedRouteArgs> {
  RecommendedRoute({
    _i28.Key? key,
    bool isNewsOfPage = false,
    bool isShop = false,
  }) : super(
          RecommendedRoute.name,
          path: '/recommended',
          args: RecommendedRouteArgs(
            key: key,
            isNewsOfPage: isNewsOfPage,
            isShop: isShop,
          ),
        );

  static const String name = 'RecommendedRoute';
}

class RecommendedRouteArgs {
  const RecommendedRouteArgs({
    this.key,
    this.isNewsOfPage = false,
    this.isShop = false,
  });

  final _i28.Key? key;

  final bool isNewsOfPage;

  final bool isShop;

  @override
  String toString() {
    return 'RecommendedRouteArgs{key: $key, isNewsOfPage: $isNewsOfPage, isShop: $isShop}';
  }
}

/// generated route for
/// [_i15.MapSearchPage]
class MapSearchRoute extends _i27.PageRouteInfo<void> {
  const MapSearchRoute()
      : super(
          MapSearchRoute.name,
          path: '/map_search',
        );

  static const String name = 'MapSearchRoute';
}

/// generated route for
/// [_i16.HelpPage]
class HelpRoute extends _i27.PageRouteInfo<void> {
  const HelpRoute()
      : super(
          HelpRoute.name,
          path: '/help',
        );

  static const String name = 'HelpRoute';
}

/// generated route for
/// [_i17.OrderProgressPage]
class OrderProgressRoute extends _i27.PageRouteInfo<OrderProgressRouteArgs> {
  OrderProgressRoute({
    _i28.Key? key,
    num? orderId,
  }) : super(
          OrderProgressRoute.name,
          path: '/order_progress',
          args: OrderProgressRouteArgs(
            key: key,
            orderId: orderId,
          ),
        );

  static const String name = 'OrderProgressRoute';
}

class OrderProgressRouteArgs {
  const OrderProgressRouteArgs({
    this.key,
    this.orderId,
  });

  final _i28.Key? key;

  final num? orderId;

  @override
  String toString() {
    return 'OrderProgressRouteArgs{key: $key, orderId: $orderId}';
  }
}

/// generated route for
/// [_i18.ResultFilter]
class ResultFilter extends _i27.PageRouteInfo<void> {
  const ResultFilter()
      : super(
          ResultFilter.name,
          path: '/result_filter',
        );

  static const String name = 'ResultFilter';
}

/// generated route for
/// [_i19.WalletHistory]
class WalletHistory extends _i27.PageRouteInfo<void> {
  const WalletHistory()
      : super(
          WalletHistory.name,
          path: '/wallet_history',
        );

  static const String name = 'WalletHistory';
}

/// generated route for
/// [_i20.CreateShop]
class CreateShop extends _i27.PageRouteInfo<void> {
  const CreateShop()
      : super(
          CreateShop.name,
          path: '/create_shop',
        );

  static const String name = 'CreateShop';
}

/// generated route for
/// [_i21.ShopsBannerPage]
class ShopsBannerRoute extends _i27.PageRouteInfo<ShopsBannerRouteArgs> {
  ShopsBannerRoute({
    _i28.Key? key,
    required int bannerId,
    required String title,
  }) : super(
          ShopsBannerRoute.name,
          path: '/shops_banner',
          args: ShopsBannerRouteArgs(
            key: key,
            bannerId: bannerId,
            title: title,
          ),
        );

  static const String name = 'ShopsBannerRoute';
}

class ShopsBannerRouteArgs {
  const ShopsBannerRouteArgs({
    this.key,
    required this.bannerId,
    required this.title,
  });

  final _i28.Key? key;

  final int bannerId;

  final String title;

  @override
  String toString() {
    return 'ShopsBannerRouteArgs{key: $key, bannerId: $bannerId, title: $title}';
  }
}

/// generated route for
/// [_i22.ShopDetailPage]
class ShopDetailRoute extends _i27.PageRouteInfo<ShopDetailRouteArgs> {
  ShopDetailRoute({
    _i28.Key? key,
    required _i30.ShopData shop,
    required String workTime,
  }) : super(
          ShopDetailRoute.name,
          path: '/shops_detail',
          args: ShopDetailRouteArgs(
            key: key,
            shop: shop,
            workTime: workTime,
          ),
        );

  static const String name = 'ShopDetailRoute';
}

class ShopDetailRouteArgs {
  const ShopDetailRouteArgs({
    this.key,
    required this.shop,
    required this.workTime,
  });

  final _i28.Key? key;

  final _i30.ShopData shop;

  final String workTime;

  @override
  String toString() {
    return 'ShopDetailRouteArgs{key: $key, shop: $shop, workTime: $workTime}';
  }
}

/// generated route for
/// [_i23.ShareReferralPage]
class ShareReferralRoute extends _i27.PageRouteInfo<void> {
  const ShareReferralRoute()
      : super(
          ShareReferralRoute.name,
          path: '/share_referral',
        );

  static const String name = 'ShareReferralRoute';
}

/// generated route for
/// [_i24.ShareReferralFaq]
class ShareReferralFaq extends _i27.PageRouteInfo<ShareReferralFaqArgs> {
  ShareReferralFaq({
    _i28.Key? key,
    required String terms,
  }) : super(
          ShareReferralFaq.name,
          path: '/share_referral_faq',
          args: ShareReferralFaqArgs(
            key: key,
            terms: terms,
          ),
        );

  static const String name = 'ShareReferralFaq';
}

class ShareReferralFaqArgs {
  const ShareReferralFaqArgs({
    this.key,
    required this.terms,
  });

  final _i28.Key? key;

  final String terms;

  @override
  String toString() {
    return 'ShareReferralFaqArgs{key: $key, terms: $terms}';
  }
}

/// generated route for
/// [_i25.ChatPage]
class ChatRoute extends _i27.PageRouteInfo<ChatRouteArgs> {
  ChatRoute({
    _i28.Key? key,
    required String roleId,
    required String name,
  }) : super(
          ChatRoute.name,
          path: '/chat',
          args: ChatRouteArgs(
            key: key,
            roleId: roleId,
            name: name,
          ),
        );

  static const String name = 'ChatRoute';
}

class ChatRouteArgs {
  const ChatRouteArgs({
    this.key,
    required this.roleId,
    required this.name,
  });

  final _i28.Key? key;

  final String roleId;

  final String name;

  @override
  String toString() {
    return 'ChatRouteArgs{key: $key, roleId: $roleId, name: $name}';
  }
}

/// generated route for
/// [_i26.GenerateImagePage]
class GenerateImageRoute extends _i27.PageRouteInfo<void> {
  const GenerateImageRoute()
      : super(
          GenerateImageRoute.name,
          path: '/generate_image',
        );

  static const String name = 'GenerateImageRoute';
}
