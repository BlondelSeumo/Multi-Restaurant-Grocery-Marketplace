import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter_remix/flutter_remix.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:pull_to_refresh/pull_to_refresh.dart';
import 'package:riverpodtemp/application/home/home_provider.dart';
import 'package:riverpodtemp/application/language/language_provider.dart';
import 'package:riverpodtemp/application/orders_list/orders_list_provider.dart';
import 'package:riverpodtemp/application/profile/profile_provider.dart';
import 'package:riverpodtemp/application/shop_order/shop_order_provider.dart';
import 'package:riverpodtemp/infrastructure/services/app_constants.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/local_storage.dart';
import 'package:riverpodtemp/infrastructure/services/tr_keys.dart';
import 'package:riverpodtemp/presentation/components/app_bars/common_app_bar.dart';
import 'package:riverpodtemp/presentation/components/custom_network_image.dart';
import 'package:riverpodtemp/presentation/components/loading.dart';
import 'package:riverpodtemp/application/like/like_provider.dart';
import 'package:riverpodtemp/presentation/pages/profile/currency_page.dart';
import 'package:riverpodtemp/presentation/pages/profile/delete_screen.dart';
import 'package:riverpodtemp/presentation/routes/app_router.gr.dart';
import 'package:riverpodtemp/presentation/theme/theme.dart';
import 'package:intl/intl.dart' as intl;
import 'package:url_launcher/url_launcher.dart';
import 'edit_profile_page.dart';
import '../../../../application/edit_profile/edit_profile_provider.dart';
import 'language_page.dart';
import 'widgets/profile_item.dart';

class ProfilePage extends ConsumerStatefulWidget {
  const ProfilePage({Key? key}) : super(key: key);

  @override
  ConsumerState<ProfilePage> createState() => _ProfilePageState();
}

class _ProfilePageState extends ConsumerState<ProfilePage>
    with SingleTickerProviderStateMixin {
  late RefreshController refreshController;

  @override
  void initState() {
    refreshController = RefreshController();
    if (LocalStorage.instance.getToken().isNotEmpty) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        ref.read(profileProvider.notifier).fetchUser(context);
        ref.read(ordersListProvider.notifier).fetchActiveOrders(context);
      });
    }

    super.initState();
  }

  getAllInformation() {
    ref.read(homeProvider.notifier)
      ..setAddress()
      ..fetchBanner(context)
      ..fetchRestaurant(context)
      ..fetchShopRecommend(context)
      ..fetchShop(context)
      ..fetchStore(context)
      ..fetchRestaurantNew(context)
      ..fetchRestaurant(context)
      ..fetchCategories(context);
    ref.read(shopOrderProvider.notifier).getCart(context, () {});

    ref.read(likeProvider.notifier).fetchLikeShop(context);

    ref.read(profileProvider.notifier).fetchUser(context);
  }

  @override
  void dispose() {
    refreshController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final bool isDarkMode = LocalStorage.instance.getAppThemeMode();
    final bool isLtr = LocalStorage.instance.getLangLtr();
    final state = ref.watch(profileProvider);
    ref.listen(languageProvider, (previous, next) {
      if (next.isSuccess && next.isSuccess != previous!.isSuccess) {
        getAllInformation();
      }
    });

    return Directionality(
      textDirection: isLtr ? TextDirection.ltr : TextDirection.rtl,
      child: Scaffold(
        resizeToAvoidBottomInset: false,
        backgroundColor: isDarkMode ? Style.mainBackDark : Style.bgGrey,
        body: state.isLoading
            ? const Loading()
            : Column(
                children: [
                  CommonAppBar(
                      child: Row(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          CustomNetworkImage(
                              url: state.userData?.img ?? "",
                              height: 40.r,
                              width: 40.r,
                              radius: 30.r),
                          12.horizontalSpace,
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            mainAxisAlignment: MainAxisAlignment.end,
                            children: [
                              Text(
                                "${state.userData?.firstname ?? ""} ${state.userData?.lastname ?? ""}",
                                style: Style.interNormal(
                                  size: 14,
                                  color: Style.black,
                                ),
                              ),
                              SizedBox(
                                width: MediaQuery.of(context).size.width-150.w,
                                child: Text(
                                  state.userData?.email ?? " ",
                                  style: Style.interRegular(
                                    size: 12.sp,
                                    color: Style.textGrey,
                                  ),
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                ),
                              ),
                            ],
                          )
                        ],
                      ),
                      IconButton(
                          onPressed: () {
                            AppHelpers.showAlertDialog(
                                context: context, child: const DeleteScreen());
                          },
                          icon: const Icon(FlutterRemix.logout_circle_r_line))
                    ],
                  )),
                  Expanded(
                    child: SmartRefresher(
                      onRefresh: () {
                        ref.read(profileProvider.notifier).fetchUser(context,
                            refreshController: refreshController);
                        ref
                            .read(ordersListProvider.notifier)
                            .fetchActiveOrders(context);
                      },
                      controller: refreshController,
                      child: SingleChildScrollView(
                        padding: EdgeInsets.only(
                            top: 24.h, right: 16.w, left: 16.w, bottom: 120.h),
                        child: Column(
                          children: [
                            ProfileItem(
                              isLtr: isLtr,
                              title:
                                  "${AppHelpers.getTranslation(TrKeys.wallet)}: ${intl.NumberFormat.currency(
                                symbol: LocalStorage.instance
                                    .getSelectedCurrency()
                                    .symbol,
                              ).format(state.userData?.wallet?.price ?? 0)}",
                              icon: FlutterRemix.wallet_3_line,
                              onTap: () {
                                context.pushRoute(const WalletHistory());
                              },
                            ),
                            AppHelpers.getReferralActive()
                                ? ProfileItem(
                                    isLtr: isLtr,
                                    title: AppHelpers.getTranslation(
                                        TrKeys.inviteFriend),
                                    icon: FlutterRemix.money_dollar_circle_line,
                                    onTap: () {
                                      context.pushRoute(
                                          const ShareReferralRoute());
                                    },
                                  )
                                : const SizedBox.shrink(),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(TrKeys.order),
                              icon: FlutterRemix.history_line,
                              isCount: true,
                              onTap: () {
                                context.pushRoute(const OrdersListRoute());
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(
                                  TrKeys.becomeSeller),
                              icon: FlutterRemix.user_star_line,
                              onTap: () {
                                context.pushRoute(const CreateShop());
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(
                                  TrKeys.chatWithAdmin),
                              icon: FlutterRemix.chat_1_line,
                              onTap: () {
                                context.pushRoute( ChatRoute(roleId: "admin", name: "Admin"));
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(
                                  TrKeys.profileSettings),
                              icon: FlutterRemix.user_settings_line,
                              onTap: () {
                                ref.refresh(editProfileProvider);
                                AppHelpers.showCustomModalBottomSheet(
                                  paddingTop:
                                      MediaQuery.of(context).padding.top,
                                  context: context,
                                  modal: const EditProfileScreen(),
                                  isDarkMode: isDarkMode,
                                );
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(TrKeys.language),
                              icon: FlutterRemix.global_line,
                              onTap: () {
                                AppHelpers.showCustomModalBottomSheet(
                                  isDismissible: false,
                                  context: context,
                                  modal: LanguageScreen(
                                    onSave: () {
                                      Navigator.pop(context);
                                    },
                                  ),
                                  isDarkMode: isDarkMode,
                                );
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title:
                                  AppHelpers.getTranslation(TrKeys.currencies),
                              icon: FlutterRemix.bank_card_line,
                              onTap: () {
                                AppHelpers.showCustomModalBottomSheet(
                                  context: context,
                                  modal: const CurrencyScreen(),
                                  isDarkMode: isDarkMode,
                                );
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(
                                  TrKeys.notification),
                              icon: FlutterRemix.settings_4_line,
                              onTap: () {
                                context.pushRoute(const SettingRoute());
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(TrKeys.help),
                              icon: FlutterRemix.question_line,
                              onTap: () {
                                context.pushRoute(const HelpRoute());
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(TrKeys.about),
                              icon: FlutterRemix.bill_line,
                              onTap: () async {
                                // ignore: deprecated_member_use
                                await launch(
                                  "${AppConstants.webUrl}/about",
                                  forceSafariVC: true,
                                  forceWebView: true,
                                  enableJavaScript: true,
                                );
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(TrKeys.careers),
                              icon: FlutterRemix.empathize_line,
                              onTap: () async {
                                // ignore: deprecated_member_use
                                await launch(
                                  "${AppConstants.webUrl}/careers",
                                  forceSafariVC: true,
                                  forceWebView: true,
                                  enableJavaScript: true,
                                );
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(TrKeys.blogs),
                              icon: FlutterRemix.article_line,
                              onTap: () async {
                                // ignore: deprecated_member_use
                                await launch(
                                  "${AppConstants.webUrl}/blog",
                                  forceSafariVC: true,
                                  forceWebView: true,
                                  enableJavaScript: true,
                                );
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(
                                  TrKeys.privacyPolicy),
                              icon: FlutterRemix.information_line,
                              onTap: () async {
                                // ignore: deprecated_member_use
                                await launch(
                                  "${AppConstants.webUrl}/privacy",
                                  forceSafariVC: true,
                                  forceWebView: true,
                                  enableJavaScript: true,
                                );
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(TrKeys.terms),
                              icon: FlutterRemix.file_info_line,
                              onTap: () async {
                                // ignore: deprecated_member_use
                                await launch(
                                  "${AppConstants.webUrl}/terms",
                                  forceSafariVC: true,
                                  forceWebView: true,
                                  enableJavaScript: true,
                                );
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(
                                  TrKeys.signUpToDeliver),
                              icon: FlutterRemix.external_link_line,
                              onTap: () {
                                context.pushRoute(const HelpRoute());
                              },
                            ),
                            ProfileItem(
                              isLtr: isLtr,
                              title: AppHelpers.getTranslation(
                                  TrKeys.deleteAccount),
                              icon: FlutterRemix.logout_box_r_line,
                              onTap: () {
                                AppHelpers.showAlertDialog(
                                  context: context,
                                  child: const DeleteScreen(
                                    isDeleteAccount: true,
                                  ),
                                );
                              },
                            ),
                          ],
                        ),
                      ),
                    ),
                  )
                ],
              ),
      ),
    );
  }
}
