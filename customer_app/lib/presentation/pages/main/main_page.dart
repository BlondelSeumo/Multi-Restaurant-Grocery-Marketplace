// ignore_for_file: use_build_context_synchronously

import 'package:auto_route/auto_route.dart';
import 'package:firebase_dynamic_links/firebase_dynamic_links.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:flutter_remix/flutter_remix.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:riverpodtemp/application/profile/profile_provider.dart';
import 'package:riverpodtemp/application/shop_order/shop_order_provider.dart';
import 'package:riverpodtemp/infrastructure/models/data/remote_message_data.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/tr_keys.dart';
import 'package:riverpodtemp/presentation/components/buttons/animation_button_effect.dart';
import 'package:riverpodtemp/presentation/components/custom_network_image.dart';
import 'package:riverpodtemp/presentation/components/keyboard_dismisser.dart';
import 'package:riverpodtemp/presentation/pages/home/home_page.dart';
import 'package:riverpodtemp/presentation/pages/like/like_page.dart';
import 'package:riverpodtemp/presentation/pages/profile/profile_page.dart';
import 'package:riverpodtemp/presentation/pages/search/search_page.dart';
import 'package:riverpodtemp/presentation/routes/app_router.gr.dart';
import 'package:riverpodtemp/presentation/theme/theme.dart';
import '../../../application/main/main_provider.dart';
import '../../../infrastructure/services/local_storage.dart';
import '../../components/blur_wrap.dart';
import 'widgets/bottom_navigator_item.dart';
import 'package:proste_indexed_stack/proste_indexed_stack.dart';

class MainPage extends StatefulWidget {
  const MainPage({
    Key? key,
  }) : super(key: key);

  @override
  State<MainPage> createState() => _MainPageState();
}

class _MainPageState extends State<MainPage> {
  final FirebaseDynamicLinks dynamicLinks = FirebaseDynamicLinks.instance;
  List<IndexedStackChild> list = [
    IndexedStackChild(child: const HomePage(), preload: true),
    IndexedStackChild(child: const SearchPage()),
    IndexedStackChild(child: const LikePage()),
    IndexedStackChild(child: const ProfilePage(), preload: true),
  ];

  @override
  void initState() {
    initDynamicLinks();
    FirebaseMessaging.instance.requestPermission(
      sound: true,
      alert: true,
      badge: false,
    );

    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      RemoteMessageData data = RemoteMessageData.fromJson(message.data);
      context.router.popUntilRoot();
      context.pushRoute(
        OrderProgressRoute(
          orderId: data.id,
        ),
      );
    });
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      RemoteMessageData data = RemoteMessageData.fromJson(message.data);
      AppHelpers.showCheckTopSnackBarInfo(
          context,
          "${AppHelpers.getTranslation(TrKeys.id)} #${message.notification?.title} ${message.notification?.body}",
          onTap: ()
      {
        context.router.popUntilRoot();
        context.pushRoute(
          OrderProgressRoute(
            orderId: data.id,
          ),
        );
      });
    });
    super.initState();
  }

  Future<void> initDynamicLinks() async {
    dynamicLinks.onLink.listen((dynamicLinkData) {
      String link = dynamicLinkData.link.toString();
      if (link.contains("group")) {
        context.router.popUntilRoot();
        context.pushRoute(
          ShopRoute(
            shopId: link.substring(
                link.lastIndexOf("/") + 1, link.lastIndexOf("?")),
            cartId: link.substring(
                link.lastIndexOf("g") + 2, link.lastIndexOf("&o")),
            ownerId: int.tryParse(
              link.substring(
                link.lastIndexOf("&o") + 3,
                link.lastIndexOf("&"),
              ),
            ),
          ),
        );
      } else if (!link.contains("product") &&
          (link.contains("shop") || link.contains("restaurant"))) {
        context.router.popUntilRoot();
        context.pushRoute(
          ShopRoute(
            shopId: link.substring(link.lastIndexOf("/") + 1),
          ),
        );
      } else if (link.contains("shop")) {
        context.router.popUntilRoot();
        context.pushRoute(
          ShopRoute(
              shopId: link.substring(
                  link.indexOf("shop/") + 5, link.lastIndexOf("?")),
              productId:
              link.substring(link.indexOf("="), link.lastIndexOf("/"))),
        );
      } else if (link.contains("restaurant")) {
        context.router.popUntilRoot();
        context.pushRoute(
          ShopRoute(
              shopId: link.substring(
                  link.indexOf("restaurant/") + 11, link.lastIndexOf("?")),
              productId:
              link.substring(link.indexOf("=") + 1, link.lastIndexOf("/"))),
        );
      }
    }).onError((error) {
      debugPrint(error.message);
    });

    final PendingDynamicLinkData? data =
    await FirebaseDynamicLinks.instance.getInitialLink();
    final Uri? deepLink = data?.link;
    if (deepLink.toString().contains("group")) {
      context.router.popUntilRoot();
      context.pushRoute(
        ShopRoute(
          shopId: deepLink.toString().substring(
              deepLink.toString().lastIndexOf("/") + 1,
              deepLink.toString().lastIndexOf("?")),
          cartId: deepLink.toString().substring(
              deepLink.toString().lastIndexOf("g") + 2,
              deepLink.toString().lastIndexOf("&o")),
          ownerId: int.tryParse(
            deepLink.toString().substring(
              deepLink.toString().lastIndexOf("&o") + 3,
              deepLink.toString().lastIndexOf("&"),
            ),
          ),
        ),
      );
    } else if (!deepLink.toString().contains("product") &&
        (deepLink.toString().contains("shop") ||
            deepLink.toString().contains("restaurant"))) {
      context.pushRoute(
        ShopRoute(
          shopId: deepLink
              .toString()
              .substring(deepLink.toString().lastIndexOf("/") + 1),
        ),
      );
    } else if (deepLink.toString().contains("shop")) {
      context.pushRoute(
        ShopRoute(
            shopId: deepLink.toString().substring(
                deepLink.toString().indexOf("shop/") + 5,
                deepLink.toString().lastIndexOf("?")),
            productId: deepLink.toString().substring(
                deepLink.toString().indexOf("="),
                deepLink.toString().lastIndexOf("/"))),
      );
    } else if (deepLink.toString().contains("restaurant")) {
      context.pushRoute(
        ShopRoute(
            shopId: deepLink.toString().substring(
                deepLink.toString().indexOf("restaurant/") + 11,
                deepLink.toString().lastIndexOf("?")),
            productId: deepLink.toString().substring(
                deepLink.toString().indexOf("=") + 1,
                deepLink.toString().lastIndexOf("/"))),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return KeyboardDismisser(
        child: Scaffold(
          resizeToAvoidBottomInset: false,
          body: Consumer(
            builder: (BuildContext context, WidgetRef ref, Widget? child) {
              final index = ref
                  .watch(mainProvider)
                  .selectIndex;
              return ProsteIndexedStack(
                index: index,
                children: list,
              );
            },
          ),
          floatingActionButtonLocation: FloatingActionButtonLocation
              .centerFloat,
          floatingActionButton: Consumer(builder: (context, ref, child) {
            final index = ref
                .watch(mainProvider)
                .selectIndex;
            final user = ref
                .watch(profileProvider)
                .userData;
            final orders = ref
                .watch(shopOrderProvider)
                .cart;
            final event = ref.read(mainProvider.notifier);
            return Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                BlurWrap(
                  radius: BorderRadius.circular(100.r),
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 500),
                    decoration: BoxDecoration(
                        color: Style.bottomNavigationBarColor.withOpacity(0.6),
                        borderRadius: BorderRadius.all(Radius.circular(100.r))),
                    height: 60.r,
                    child: Padding(
                      padding: EdgeInsets.symmetric(horizontal: 10.r),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        mainAxisAlignment: MainAxisAlignment.spaceAround,
                        children: [
                          BottomNavigatorItem(
                            isScrolling: index == 3
                                ? false
                                : ref
                                .watch(mainProvider)
                                .isScrolling,
                            selectItem: () {
                              event.changeScrolling(false);
                              event.selectIndex(0);
                            },
                            index: 0,
                            currentIndex: index,
                            selectIcon: FlutterRemix.restaurant_fill,
                            unSelectIcon: FlutterRemix.restaurant_line,
                          ),
                          BottomNavigatorItem(
                            isScrolling: index == 3
                                ? false
                                : ref
                                .watch(mainProvider)
                                .isScrolling,
                            selectItem: () {
                              event.changeScrolling(false);
                              event.selectIndex(1);
                            },
                            currentIndex: index,
                            index: 1,
                            selectIcon: FlutterRemix.search_fill,
                            unSelectIcon: FlutterRemix.search_line,
                          ),
                          BottomNavigatorItem(
                            isScrolling: index == 3
                                ? false
                                : ref
                                .watch(mainProvider)
                                .isScrolling,
                            selectItem: () {
                              event.changeScrolling(false);
                              event.selectIndex(2);
                            },
                            currentIndex: index,
                            index: 2,
                            selectIcon: FlutterRemix.heart_fill,
                            unSelectIcon: FlutterRemix.heart_line,
                          ),
                          GestureDetector(
                            onTap: () {
                              if (event.checkGuest()) {
                                event.selectIndex(0);
                                event.changeScrolling(false);
                                context.replaceRoute(const LoginRoute());
                              } else {
                                event.changeScrolling(false);
                                event.selectIndex(3);
                              }
                            },
                            child: Container(
                              width: 40.r,
                              height: 40.r,
                              decoration: BoxDecoration(
                                  border: Border.all(
                                      color: index == 3
                                          ? Style.brandGreen
                                          : Style.transparent,
                                      width: 2.w),
                                  shape: BoxShape.circle),
                              child: CustomNetworkImage(
                                url: user?.img ??
                                    LocalStorage.instance.getProfileImage(),
                                height: 40.r,
                                width: 40
                                  ..r,
                                radius: 20.r,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                orders == null ||
                    (orders.userCarts?.isEmpty ?? true) ||
                    ((orders.userCarts?.isEmpty ?? true)
                        ? true
                        : (orders.userCarts?.first.cartDetails?.isEmpty ??
                        true)) || orders.ownerId != LocalStorage.instance.getUserId()
                    ? const SizedBox.shrink()
                    : AnimationButtonEffect(
                  child: GestureDetector(
                    onTap: () {
                      context.pushRoute(const OrderScreen());
                    },
                    child: Container(
                      margin: EdgeInsets.only(left: 8.w),
                      width: 56.r,
                      height: 56.r,
                      decoration: const BoxDecoration(
                        shape: BoxShape.circle,
                        color: Style.brandGreen,
                      ),
                      child: const Icon(FlutterRemix.shopping_bag_3_line),
                    ),
                  ),
                )
              ],
            );
          }),
        ));
  }
}
