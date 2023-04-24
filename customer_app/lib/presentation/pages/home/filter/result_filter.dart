import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:pull_to_refresh/pull_to_refresh.dart';
import 'package:riverpodtemp/application/filter/filter_provider.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/presentation/components/app_bars/common_app_bar.dart';
import 'package:riverpodtemp/presentation/components/buttons/pop_button.dart';
import 'package:riverpodtemp/presentation/components/title_icon.dart';

import '../../../../application/filter/filter_notifier.dart';
import '../../../../infrastructure/services/tr_keys.dart';
import '../../../components/market_item.dart';
import '../../../theme/app_style.dart';
import '../shimmer/all_shop_shimmer.dart';
import '../shimmer/news_shop_shimmer.dart';

class ResultFilter extends ConsumerStatefulWidget {
  const ResultFilter({Key? key}) : super(key: key);

  @override
  ConsumerState<ResultFilter> createState() => _ResultFilterState();
}

class _ResultFilterState extends ConsumerState<ResultFilter> {
  late FilterNotifier event;
  final RefreshController _shopController = RefreshController();
  final RefreshController _restaurantController = RefreshController();

  @override
  void initState() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(filterProvider.notifier)
        ..fetchFilterShop(context)
        ..fetchRestaurant(context);
    });
    super.initState();
  }

  @override
  void didChangeDependencies() {
    event = ref.read(filterProvider.notifier);
    super.didChangeDependencies();
  }

  @override
  void dispose() {
    _shopController.dispose();
    _restaurantController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(filterProvider);
    return Scaffold(
      body: Column(
        children: [
          CommonAppBar(
            child: Text(
              AppHelpers.getTranslation(TrKeys.shopAndRestaurants),
              style: Style.interNoSemi(size: 18.sp),
            ),
          ),
          Expanded(
            child: SmartRefresher(
              controller: _restaurantController,
              enablePullUp: true,
              enablePullDown: true,
              onLoading: () {
                  event.fetchRestaurantPage(context, _restaurantController);
              },
              onRefresh: () {
               event
                  ..fetchRestaurantPage(context, _restaurantController,
                      isRefresh: true)
                  ..fetchFilterShopPage(context, _restaurantController,
                      isRefresh: true);
              },
              child: SingleChildScrollView(
                child: Column(
                  children: [
                    24.verticalSpace,
                    state.isShopLoading
                        ? NewsShopShimmer(
                            title: AppHelpers.getTranslation(TrKeys.shops),
                          )
                        : state.shops.isNotEmpty
                            ? Column(
                                children: [
                                  TitleAndIcon(
                                    title: AppHelpers.getTranslation(TrKeys.shops),
                                    rightTitle:
                                        "${AppHelpers.getTranslation(TrKeys.found)} ${state.shops.length.toString()} ${AppHelpers.getTranslation(TrKeys.results)}",
                                  ),
                                  12.verticalSpace,
                                  SizedBox(
                                      height: 246.h,
                                      child: SmartRefresher(
                                        scrollDirection: Axis.horizontal,
                                        controller: _shopController,
                                        enablePullDown: false,
                                        enablePullUp: true,
                                        onLoading: () async {
                                          await event.fetchFilterShopPage(
                                              context, _shopController);
                                        },
                                        child: ListView.builder(
                                          padding: EdgeInsets.only(left: 16.r),
                                          shrinkWrap: false,
                                          primary: false,
                                          scrollDirection: Axis.horizontal,
                                          itemCount: state.shops.length,
                                          itemBuilder: (context, index) => MarketItem(
                                          shop: state.shops[index],
                                          ),
                                        ),
                                      )),
                                  16.verticalSpace,
                                ],
                              )
                            : const SizedBox.shrink(),
                    state.isRestaurantLoading
                        ?   const AllShopShimmer() :
                    Column(
                      children: [
                        TitleAndIcon(
                          title: AppHelpers.getTranslation(TrKeys.restaurants),
                          rightTitle:
                              "${AppHelpers.getTranslation(TrKeys.found)} ${state.restaurant.length.toString()} ${AppHelpers.getTranslation(TrKeys.results)}",
                        ),
                        ListView.builder(
                          padding: EdgeInsets.only(top: 6.h),
                          shrinkWrap: true,
                          primary: false,
                          physics: const NeverScrollableScrollPhysics(),
                          scrollDirection: Axis.vertical,
                          itemCount: state.restaurant.length,
                          itemBuilder: (context, index) => MarketItem(
                            shop: state.restaurant[index],
                            isSimpleShop: true,
                          ),
                        ),
                      ],
                    ),

                  ],
                ),
              ),
            ),
          )
        ],
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.startFloat,
      floatingActionButton: Padding(
        padding: EdgeInsets.only(left: 16.w),
        child: const PopButton(),
      ),
    );
  }
}
