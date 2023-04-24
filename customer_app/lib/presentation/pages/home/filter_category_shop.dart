import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:pull_to_refresh/pull_to_refresh.dart';
import 'package:riverpodtemp/application/home/home_notifier.dart';
import 'package:riverpodtemp/application/home/home_state.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/tr_keys.dart';
import 'package:riverpodtemp/presentation/components/market_item.dart';
import 'package:riverpodtemp/presentation/components/title_icon.dart';

import '../../theme/app_style.dart';
import 'shimmer/news_shop_shimmer.dart';

class FilterCategoryShop extends StatelessWidget {
  final HomeState state;
  final HomeNotifier event;
  final RefreshController shopController;

  const FilterCategoryShop({Key? key, required this.state, required this.event, required this.shopController}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        state.isShopLoading
            ? NewsShopShimmer(
                title: AppHelpers.getTranslation(TrKeys.shops),
              )
            : state.filterMarket.isNotEmpty
                ? Column(
                    children: [
                      TitleAndIcon(
                        title: AppHelpers.getTranslation(TrKeys.shops),
                        rightTitle:
                            "${AppHelpers.getTranslation(TrKeys.found)} ${state.filterMarket.length.toString()} ${AppHelpers.getTranslation(TrKeys.results)}",
                      ),
                      12.verticalSpace,
                      SizedBox(
                          height: 246.h,
                          child: SmartRefresher(
                            scrollDirection: Axis.horizontal,
                            controller: shopController,
                            enablePullDown: false,
                            enablePullUp: true,
                            onLoading: () async {
                              await event.fetchFilterShopPage(
                                  context, shopController);
                            },
                            child: ListView.builder(
                              padding: EdgeInsets.only(left: 16.r),
                              shrinkWrap: false,
                              primary: false,
                              scrollDirection: Axis.horizontal,
                              itemCount: state.filterMarket.length,
                              itemBuilder: (context, index) => MarketItem(
                                shop: state.filterMarket[index],
                              ),
                            ),
                          )),
                      16.verticalSpace,
                    ],
                  )
                : const SizedBox.shrink(),
        TitleAndIcon(
          title: AppHelpers.getTranslation(TrKeys.restaurants),
          rightTitle:
              "${AppHelpers.getTranslation(TrKeys.found)} ${state.filterShops.length.toString()} ${AppHelpers.getTranslation(TrKeys.results)}",
        ),
        state.filterShops.isNotEmpty ? ListView.builder(
          padding: EdgeInsets.only(top: 6.h),
          shrinkWrap: true,
          primary: false,
          physics: const NeverScrollableScrollPhysics(),
          scrollDirection: Axis.vertical,
          itemCount: state.filterShops.length,
          itemBuilder: (context, index) => MarketItem(
            shop: state.filterShops[index],
            isSimpleShop: true,
          ),
        ) : Padding(
          padding: EdgeInsets.only(top: 24.h),
          child: _resultEmpty(),
        ),
      ],
    );
  }
}


Widget _resultEmpty() {
  return Column(
    children: [
      Image.asset("assets/images/notFound.png"),
      Text(
        AppHelpers.getTranslation(TrKeys.nothingFound),
        style: Style.interSemi(size: 18.sp),
      ),
      Padding(
        padding: EdgeInsets.symmetric(
          horizontal: 32.w,
        ),
        child: Text(
          AppHelpers.getTranslation(TrKeys.trySearchingAgain),
          style: Style.interRegular(size: 14.sp),
          textAlign: TextAlign.center,
        ),
      ),
    ],
  );
}