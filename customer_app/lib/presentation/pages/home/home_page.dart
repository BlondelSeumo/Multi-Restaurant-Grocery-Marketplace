import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:flutter_staggered_animations/flutter_staggered_animations.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:pull_to_refresh/pull_to_refresh.dart';
import 'package:riverpodtemp/application/currency/currency_provider.dart';
import 'package:riverpodtemp/application/home/home_notifier.dart';
import 'package:riverpodtemp/application/home/home_provider.dart';
import 'package:riverpodtemp/application/home/home_state.dart';
import 'package:riverpodtemp/application/main/main_provider.dart';
import 'package:riverpodtemp/application/map/view_map_provider.dart';
import 'package:riverpodtemp/application/shop_order/shop_order_provider.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/local_storage.dart';
import 'package:riverpodtemp/infrastructure/services/tr_keys.dart';

import 'package:riverpodtemp/presentation/components/loading.dart';
import 'package:riverpodtemp/presentation/components/market_item.dart';
import 'package:riverpodtemp/presentation/components/title_icon.dart';
import 'package:riverpodtemp/presentation/pages/home/app_bar_home.dart';
import 'package:riverpodtemp/presentation/pages/home/category_screen.dart';
import 'package:riverpodtemp/presentation/pages/home/filter_category_shop.dart';
import 'package:riverpodtemp/presentation/routes/app_router.gr.dart';
import 'package:riverpodtemp/presentation/theme/theme.dart';
import 'package:upgrader/upgrader.dart';
import 'shimmer/all_shop_shimmer.dart';
import 'shimmer/banner_shimmer.dart';
import 'shimmer/news_shop_shimmer.dart';
import 'shimmer/recommend_shop_shimmer.dart';
import 'shimmer/shop_shimmer.dart';
import 'widgets/add_address.dart';
import 'widgets/banner_item.dart';
import 'widgets/recommended_item.dart';
import 'widgets/shop_bar_item.dart';

class HomePage extends ConsumerStatefulWidget {
  const HomePage({
    Key? key,
  }) : super(key: key);

  @override
  ConsumerState<HomePage> createState() => _HomePageState();
}

class _HomePageState extends ConsumerState<HomePage> {
  late HomeNotifier event;
  late RefreshController _bannerController;
  late RefreshController _shopController;
  late RefreshController _restaurantController;
  late RefreshController _categoryController;
  late RefreshController _storyController;
  late ScrollController _controller;

  @override
  void initState() {
    _bannerController = RefreshController();
    _shopController = RefreshController();
    _restaurantController = RefreshController();
    _categoryController = RefreshController();
    _storyController = RefreshController();
    _controller = ScrollController();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(homeProvider.notifier)
        ..setAddress()
        ..fetchBanner(context)
        ..fetchShopRecommend(context)
        ..fetchShop(context)
        ..fetchStore(context)
        ..fetchRestaurant(context)
        ..fetchRestaurantNew(context)
        ..fetchCategories(context);
      ref.read(viewMapProvider.notifier).checkAddress();
      ref.read(currencyProvider.notifier).fetchCurrency(context);
      if(LocalStorage.instance.getToken().isNotEmpty){
        ref.read(shopOrderProvider.notifier).getCart(context, () {});
      }
    });
    _controller.addListener(listen);
    super.initState();
  }

  @override
  void didChangeDependencies() {
    event = ref.read(homeProvider.notifier);
    super.didChangeDependencies();
  }

  @override
  void dispose() {
    _bannerController.dispose();
    _categoryController.dispose();
    _shopController.dispose();
    _restaurantController.dispose();
    _storyController.dispose();
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

  void _onLoading(HomeState state) {
    if (state.isSelectCategoryLoading == 0) {
      event.fetchRestaurantPage(context, _restaurantController);
    } else {
      event.fetchFilterRestaurantPage(context, _restaurantController);
    }
  }

  void _onRefresh(HomeState state) {
    state.isSelectCategoryLoading == 0
        ? (event
          ..fetchBannerPage(context, _restaurantController, isRefresh: true)
          ..fetchRestaurantPage(context, _restaurantController, isRefresh: true)
          ..fetchCategoriesPage(context, _restaurantController, isRefresh: true)
          ..fetchStorePage(context, _restaurantController, isRefresh: true)
          ..fetchShopPage(context, _restaurantController, isRefresh: true)
          ..fetchRestaurantPageNew(context, _restaurantController,
              isRefresh: true)
          ..fetchShopPageRecommend(context, _restaurantController,
              isRefresh: true))
        : event
      ..fetchFilterRestaurantPage(context, _restaurantController,
          isRefresh: true)
      ..fetchFilterShopPage(context, _restaurantController, isRefresh: true);
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(homeProvider);
    final bool isDarkMode = LocalStorage.instance.getAppThemeMode();
    final bool isLtr = LocalStorage.instance.getLangLtr();
    ref.listen(viewMapProvider, (previous, next) {
      if (!next.isSetAddress &&
          !(previous?.isSetAddress ?? false == next.isSetAddress)) {
        AppHelpers.showAlertDialog(context: context, child: const AddAddress());
      }
    });
    return UpgradeAlert(
      child: Directionality(
        textDirection: isLtr ? TextDirection.ltr : TextDirection.rtl,
        child: Scaffold(
          backgroundColor: isDarkMode ? Style.mainBackDark : Style.bgGrey,
          body: SmartRefresher(
            enablePullDown: true,
            enablePullUp: true,
            physics: const BouncingScrollPhysics(),
            controller: _restaurantController,
            scrollController: _controller,
            header:  WaterDropMaterialHeader(
              distance: 160.h,
              backgroundColor: Style.white,
              color: Style.textGrey,
            ),
            onLoading: () => _onLoading(state),
            onRefresh: () => _onRefresh(state),
            child: SingleChildScrollView(
              child: Padding(
                padding: EdgeInsets.only( bottom: 56.h),
                child: Column(
                  children: [
                    AppBarHome(state: state, event: event),
                    24.verticalSpace,
                    CategoryScreen(
                        state: state,
                        event: event,
                        categoryController: _categoryController,
                        restaurantController: _restaurantController,
                        shopController: _shopController),
                    state.isSelectCategoryLoading == -1
                        ? const Loading()
                        : state.isSelectCategoryLoading == 0
                            ? _body(state, context)
                            : FilterCategoryShop(
                                state: state,
                                event: event,
                                shopController: _shopController),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _body(HomeState state, BuildContext context) {
    return Column(
      children: [
        state.isBannerLoading
            ? const BannerShimmer()
            : Container(
                height: state.banners.isNotEmpty ? 200.h : 0,
                margin: EdgeInsets.only(
                    bottom: state.banners.isNotEmpty ? 30.h : 0),
                child: SmartRefresher(
                  scrollDirection: Axis.horizontal,
                  enablePullDown: false,
                  enablePullUp: true,
                  primary: false,
                  controller: _bannerController,
                  onLoading: () async {
                    await event.fetchBannerPage(context, _bannerController);
                  },
                  child: AnimationLimiter(
                    child: ListView.builder(
                      shrinkWrap: true,
                      primary: false,
                      scrollDirection: Axis.horizontal,
                      itemCount: state.banners.length,
                      padding: EdgeInsets.only(left: 16.w),
                      itemBuilder: (context, index) =>
                          AnimationConfiguration.staggeredList(
                        position: index,
                        duration: const Duration(milliseconds: 375),
                        child: SlideAnimation(
                          verticalOffset: 50.0,
                          child: FadeInAnimation(
                            child: BannerItem(
                              banner: state.banners[index],
                            ),
                          ),
                        ),
                      ),
                    ),
                  ),
                ),
              ),
        state.story?.isNotEmpty ?? false
            ? SizedBox(
          height: 200.r,
          child: SmartRefresher(
            controller: _storyController,
            scrollDirection: Axis.horizontal,
            enablePullDown: false,
            enablePullUp: true,
            primary: false,
            onLoading: () async {
              await event.fetchStorePage(context, _storyController);
            },
            child: AnimationLimiter(
              child: ListView.builder(
                shrinkWrap: true,
                primary: false,
                scrollDirection: Axis.horizontal,
                itemCount: state.story?.length ?? 0,
                padding: EdgeInsets.only(left: 16.w),
                itemBuilder: (context, index) =>
                    AnimationConfiguration.staggeredList(
                        position: index,
                        duration: const Duration(milliseconds: 375),
                        child: SlideAnimation(
                          verticalOffset: 50.0,
                          child: FadeInAnimation(
                            child: ShopBarItem(
                              index: index,
                              controller: _storyController,
                              story: state.story?[index]?.first,
                            ),
                          ),
                        )),
              ),
            ),
          ),
        )
            : const SizedBox.shrink(),
        24.verticalSpace,
        state.isShopRecommendLoading
            ? const RecommendShopShimmer()
            : state.shopsRecommend.isNotEmpty
                ? Column(
                    children: [
                      TitleAndIcon(
                        rightTitle: AppHelpers.getTranslation(TrKeys.seeAll),
                        isIcon: true,
                        title: AppHelpers.getTranslation(TrKeys.recommended),
                        onRightTap: () {
                          context.pushRoute(RecommendedRoute());
                        },
                      ),
                      12.verticalSpace,
                      SizedBox(
                          height: 170.h,
                          child: AnimationLimiter(
                            child: ListView.builder(
                              shrinkWrap: false,
                              primary: false,
                              scrollDirection: Axis.horizontal,
                              padding: EdgeInsets.symmetric(horizontal: 16.w),
                              itemCount: state.shopsRecommend.length,
                              itemBuilder: (context, index) =>
                                  AnimationConfiguration.staggeredList(
                                position: index,
                                duration: const Duration(milliseconds: 375),
                                child: SlideAnimation(
                                  verticalOffset: 50.0,
                                  child: FadeInAnimation(
                                    child: RecommendedItem(
                                      shop: state.shopsRecommend[index],
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          )),
                      30.verticalSpace,
                    ],
                  )
                : const SizedBox.shrink(),
        state.isShopLoading
            ? ShopShimmer(
                title: AppHelpers.getTranslation(TrKeys.shops),
              )
            : state.shops.isNotEmpty
            ? Column(
                children: [
                  TitleAndIcon(
                    rightTitle: AppHelpers.getTranslation(TrKeys.seeAll),
                    isIcon: true,
                    title: AppHelpers.getTranslation(TrKeys.shops),
                    onRightTap: () {
                      context.pushRoute(RecommendedRoute(isShop: true));
                    },
                  ),
                  12.verticalSpace,
                  SizedBox(
                    height: 140.r,
                    child: AnimationLimiter(
                            child: ListView.builder(
                              padding: EdgeInsets.only(left: 16.r),
                              primary: false,
                              scrollDirection: Axis.horizontal,
                              itemCount: state.shops.length,
                              itemBuilder: (context, index) =>
                                  AnimationConfiguration.staggeredList(
                                position: index,
                                duration: const Duration(milliseconds: 375),
                                child: SlideAnimation(
                                  verticalOffset: 50.0,
                                  child: FadeInAnimation(
                                    child: MarketItem(
                                      isShop: true,
                                      shop: state.shops[index],
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          )
                  ),
                  30.verticalSpace,
                ],
              ) : const SizedBox.shrink(),
        state.isRestaurantNewLoading
            ? NewsShopShimmer(
                title: AppHelpers.getTranslation(TrKeys.newsOfWeek),
              )
            : state.newRestaurant.isNotEmpty ?
        Column(
                children: [
                  TitleAndIcon(
                    rightTitle: AppHelpers.getTranslation(TrKeys.seeAll),
                    isIcon: true,
                    title: AppHelpers.getTranslation(TrKeys.newsOfWeek),
                    onRightTap: () {
                      context.pushRoute(RecommendedRoute(isNewsOfPage: true));
                    },
                  ),
                  12.verticalSpace,
                  SizedBox(
                    height: 250.h,
                    child:  AnimationLimiter(
                            child: ListView.builder(
                              padding: EdgeInsets.only(left: 16.r),
                              primary: false,
                              scrollDirection: Axis.horizontal,
                              itemCount: state.newRestaurant.length,
                              itemBuilder: (context, index) =>
                                  AnimationConfiguration.staggeredList(
                                position: index,
                                duration: const Duration(milliseconds: 375),
                                child: SlideAnimation(
                                  verticalOffset: 50.0,
                                  child: FadeInAnimation(
                                    child: MarketItem(
                                      shop: state.newRestaurant[index],
                                    ),
                                  ),
                                ),
                              ),
                            ),
                          )
                  ),
                ],
              ) : const SizedBox.shrink(),
        30.verticalSpace,
        state.isRestaurantLoading
            ? const AllShopShimmer()
            : Column(
                children: [
                  TitleAndIcon(
                    title: AppHelpers.getTranslation(TrKeys.allRestaurants),
                  ),
                  state.restaurant.isNotEmpty
                      ? AnimationLimiter(
                          child: ListView.builder(
                            padding: EdgeInsets.only(top: 6.h),
                            shrinkWrap: true,
                            primary: false,
                            physics: const NeverScrollableScrollPhysics(),
                            scrollDirection: Axis.vertical,
                            itemCount: state.restaurant.length,
                            itemBuilder: (context, index) =>
                                AnimationConfiguration.staggeredList(
                              position: index,
                              duration: const Duration(milliseconds: 375),
                              child: SlideAnimation(
                                verticalOffset: 50.0,
                                child: FadeInAnimation(
                                  child: MarketItem(
                                    shop: state.restaurant[index],
                                    isSimpleShop: true,
                                  ),
                                ),
                              ),
                            ),
                          ),
                        )
                      : SvgPicture.asset(
                          "assets/svgs/empty.svg",
                          height: 100.h,
                        )
                ],
              ),
      ],
    );
  }
}
