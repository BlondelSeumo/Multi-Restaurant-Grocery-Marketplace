import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:pull_to_refresh/pull_to_refresh.dart';
import 'package:riverpodtemp/application/home/home_provider.dart';
import 'package:riverpodtemp/application/like/like_notifier.dart';
import 'package:riverpodtemp/application/like/like_provider.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/tr_keys.dart';
import 'package:riverpodtemp/presentation/components/app_bars/common_app_bar.dart';
import 'package:riverpodtemp/presentation/components/market_item.dart';
import 'package:riverpodtemp/presentation/pages/home/shimmer/banner_shimmer.dart';
import 'package:riverpodtemp/presentation/pages/home/widgets/banner_item.dart';
import 'package:riverpodtemp/presentation/theme/theme.dart';

import '../../../application/main/main_provider.dart';
import '../home/shimmer/all_shop_shimmer.dart';

class LikePage extends ConsumerStatefulWidget {
  const LikePage({Key? key}) : super(key: key);

  @override
  ConsumerState<LikePage> createState() => _LikePageState();
}

class _LikePageState extends ConsumerState<LikePage> {
  late LikeNotifier event;
  final RefreshController _bannerController = RefreshController();
  final RefreshController _likeShopController = RefreshController();
  final ScrollController _controller = ScrollController();

  @override
  void initState() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(likeProvider.notifier).fetchLikeShop(context);
    });
    _controller.addListener(listen);
    super.initState();
  }

  @override
  void didChangeDependencies() {
    event = ref.read(likeProvider.notifier);
    super.didChangeDependencies();
  }

  @override
  void dispose() {
    _bannerController.dispose();
    _likeShopController.dispose();
    _controller.removeListener(listen);
    super.dispose();
  }

  void listen() {
    final direction = _controller.position.userScrollDirection;
    if (direction == ScrollDirection.reverse) {
      ref.read(mainProvider.notifier).changeScrolling(true);
    } else if (direction == ScrollDirection.forward) {
      ref.read(mainProvider.notifier).changeScrolling(false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(likeProvider);
    return Scaffold(
      backgroundColor: Style.bgGrey,
      body: Column(
        children: [
          CommonAppBar(
            child: Text(
              AppHelpers.getTranslation(TrKeys.likeRestaurants),
              style: Style.interNoSemi(
                size: 18,
                color: Style.black,
              ),
            ),
          ),
          Expanded(
            child: SmartRefresher(
              enablePullDown: true,
              enablePullUp: false,
              physics: const BouncingScrollPhysics(),
              controller: _likeShopController,
              scrollController: _controller,
              onLoading: () {},
              onRefresh: () {
                event.fetchLikeShop(context);
                ref.read(homeProvider.notifier).fetchBannerPage(
                    context, _likeShopController,
                    isRefresh: true);
              },
              child: SingleChildScrollView(
                padding: EdgeInsets.only(
                    top: 24.h, bottom: MediaQuery.of(context).padding.bottom),
                child: Column(
                  children: [
                    ref.watch(homeProvider).isBannerLoading
                        ? const BannerShimmer()
                        : SizedBox(
                            height: 200.h,
                            child: SmartRefresher(
                              scrollDirection: Axis.horizontal,
                              enablePullDown: false,
                              enablePullUp: true,
                              controller: _bannerController,
                              onLoading: () async {
                                await ref
                                    .read(homeProvider.notifier)
                                    .fetchBannerPage(
                                        context, _bannerController);
                              },
                              child: ListView.builder(
                                shrinkWrap: false,
                                scrollDirection: Axis.horizontal,
                                itemCount:
                                    ref.watch(homeProvider).banners.length,
                                padding: EdgeInsets.only(left: 16.w),
                                itemBuilder: (context, index) => BannerItem(
                                  banner:
                                      ref.watch(homeProvider).banners[index],
                                ),
                              ),
                            ),
                          ),
                    24.verticalSpace,
                    state.isShopLoading
                        ? const AllShopShimmer(
                            isTitle: false,
                          )
                        : state.shops.isEmpty
                            ? _resultEmpty()
                            : ListView.builder(
                                padding: EdgeInsets.only(top: 6.h),
                                shrinkWrap: true,
                                physics: const NeverScrollableScrollPhysics(),
                                scrollDirection: Axis.vertical,
                                itemCount: state.shops.length,
                                itemBuilder: (context, index) => MarketItem(
                                  shop: state.shops[index],
                                  isSimpleShop: true,
                                ),
                              ),
                  ],
                ),
              ),
            ),
          )
        ],
      ),
    );
  }

  Widget _resultEmpty() {
    return Column(
      children: [
        32.verticalSpace,
        Image.asset("assets/images/notFound.png"),
        Text(
          AppHelpers.getTranslation(TrKeys.nothingFound),
          style: Style.interSemi(size: 18.sp),
        ),
        Padding(
          padding: EdgeInsets.symmetric(horizontal: 32.w),
          child: Text(
            AppHelpers.getTranslation(TrKeys.trySearchingAgain),
            style: Style.interRegular(size: 14.sp),
            textAlign: TextAlign.center,
          ),
        ),
      ],
    );
  }
}
