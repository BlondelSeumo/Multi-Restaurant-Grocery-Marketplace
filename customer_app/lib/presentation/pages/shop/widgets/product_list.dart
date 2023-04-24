import 'package:flutter/cupertino.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:flutter_staggered_animations/flutter_staggered_animations.dart';
import 'package:lottie/lottie.dart';
import 'package:pull_to_refresh/pull_to_refresh.dart';
import 'package:riverpodtemp/infrastructure/models/models.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/tr_keys.dart';
import 'package:riverpodtemp/presentation/components/title_icon.dart';

import 'package:riverpodtemp/presentation/theme/app_style.dart';

import '../../../../application/shop/shop_provider.dart';
import '../../product/product_page.dart';
import 'shimmer_product_list.dart';
import 'shop_product_item.dart';

class ProductsList extends ConsumerStatefulWidget {
  final CategoryData? categoryData;
  final String shopId;
  final int? index;
  final String? cartId;

  const ProductsList( {
    Key? key,
    this.categoryData,
    this.index,
    this.cartId,
    required this.shopId,
  }) : super(key: key);

  @override
  ConsumerState<ProductsList> createState() => _ProductsListState();
}

class _ProductsListState extends ConsumerState<ProductsList> {
  late RefreshController refreshController = RefreshController();

  @override
  void initState() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      widget.index == null
          ? ref.read(shopProvider.notifier).fetchProductsByCategory(
              context, widget.shopId, widget.categoryData!.id ?? 0)
          : widget.index == 0
              ? ref
                  .read(shopProvider.notifier)
                  .fetchProducts(context, widget.shopId)
              : ref
                  .read(shopProvider.notifier)
                  .fetchProductsPopular(context, widget.shopId);
    });
    super.initState();
  }

  @override
  void dispose() {
    refreshController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(shopProvider);
    return SmartRefresher(
      controller: refreshController,
      enablePullUp: state.products.isNotEmpty,
      enablePullDown: false,
      onRefresh: () {
        refreshController.refreshCompleted();
      },
      onLoading: () {
        widget.index == 0
            ? ref.read(shopProvider.notifier).fetchProductsPage(
                  context,
                  widget.shopId,
                )
            : widget.index == 1
                ? ref.read(shopProvider.notifier).fetchProductsPopularPage(
                      context,
                      widget.shopId,
                    )
                : ref.read(shopProvider.notifier).fetchProductsByCategoryPage(
                    context, widget.shopId, widget.categoryData?.id ?? 0);
        refreshController.loadComplete();
      },
      child: SingleChildScrollView(
        physics: const NeverScrollableScrollPhysics(),
        child: Column(
          children: [
            TitleAndIcon(
                title: widget.index == null
                    ? widget.categoryData?.translation?.title ?? ""
                    : widget.index == 0
                        ? AppHelpers.getTranslation(TrKeys.all)
                        : AppHelpers.getTranslation(TrKeys.popular)),
            12.verticalSpace,
            state.isProductLoading
                ? const ShimmerProductList()
                : state.products.isEmpty
                    ? _resultEmpty()
                    : AnimationLimiter(
                        child: GridView.builder(
                          padding: EdgeInsets.only(
                              right: 12.w, left: 12.w, bottom: 96.h),
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          gridDelegate:
                              SliverGridDelegateWithFixedCrossAxisCount(
                                  childAspectRatio: 0.66.r, crossAxisCount: 2,mainAxisExtent: 250.r),
                          itemCount: state.products.length,
                          itemBuilder: (context, index) {
                            return AnimationConfiguration.staggeredGrid(
                              columnCount: state.products.length,
                              position: index,
                              duration: const Duration(milliseconds: 375),
                              child: ScaleAnimation(
                                scale: 0.5,
                                child: FadeInAnimation(
                                  child: GestureDetector(
                                    onTap: () {
                                      AppHelpers.showCustomModalBottomSheet(
                                        paddingTop:
                                            MediaQuery.of(context).padding.top +
                                                100.h,
                                        context: context,
                                        modal: ProductScreen(
                                          cartId: widget.cartId,
                                          data: state.products[index],
                                        ),
                                        isDarkMode: false,
                                        isDrag: true,
                                        radius: 16,
                                      );
                                    },
                                    child: ShopProductItem(
                                      productName: state.products[index]
                                              .translation?.title ??
                                          "",
                                      productDescription: state.products[index]
                                              .translation?.description ??
                                          "",
                                      productImage:
                                          state.products[index].img ?? "",
                                      price: (state
                                          .products[index].stocks?.first.totalPrice ?? 0),
                                      count: state.products[index].count ?? 0,
                                      isBonus: state.products[index].stocks
                                              ?.first.bonus !=
                                          null,
                                      bonus: state
                                          .products[index].stocks?.first.bonus,
                                      discount: state
                                          .products[index].stocks?.first.discount != null ?( (state
                                          .products[index].stocks?.first.price ?? 0 ) + (state
                                          .products[index].stocks?.first.tax ?? 0 ) ): null,
                                    ),
                                  ),
                                ),
                              ),
                            );
                          },
                        ),
                      ),
            state.isProductPageLoading
                ? const CupertinoActivityIndicator()
                : const SizedBox.shrink()
          ],
        ),
      ),
    );
  }

  Widget _resultEmpty() {
    return Column(
      children: [
        Lottie.asset("assets/lottie/empty-box.json"),
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
