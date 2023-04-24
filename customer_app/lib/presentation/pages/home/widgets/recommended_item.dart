import 'dart:ui';

import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:riverpodtemp/infrastructure/models/data/shop_data.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/tr_keys.dart';
import 'package:riverpodtemp/presentation/components/custom_network_image.dart';
import 'package:riverpodtemp/presentation/components/shop_avarat.dart';
import 'package:riverpodtemp/presentation/routes/app_router.gr.dart';
import 'package:riverpodtemp/presentation/theme/theme.dart';

class RecommendedItem extends StatelessWidget {
  final ShopData shop;

  const RecommendedItem({
    Key? key,
    required this.shop,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () {
        context.pushRoute(ShopRoute(shopId: (shop.id ?? 0).toString()));
      },
      child: Container(
        margin: EdgeInsets.only(left: 0, right: 9.r),
        width: MediaQuery.of(context).size.width / 3,
        height: 190.h,
        decoration: BoxDecoration(
            color: Style.recommendBg,
            borderRadius: BorderRadius.all(Radius.circular(10.r))),
        child: Stack(
          children: [
            CustomNetworkImage(
                url: shop.backgroundImg ?? "",
                width: MediaQuery.of(context).size.width / 2,
                height: 190.h,
                radius: 10.r),
            Padding(
              padding: EdgeInsets.all(12.r),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      ShopAvatar(
                        shopImage: shop.logoImg ?? "",
                        size: 36,
                        padding: 4,
                      ),
                      8.horizontalSpace,
                      Expanded(
                        child: Text(
                          shop.translation?.title ?? "",
                          style: Style.interNormal(
                            size: 12,
                            color: Style.black,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  ClipRRect(
                    borderRadius: BorderRadius.all(Radius.circular(100.r)),
                    child: BackdropFilter(
                      filter: ImageFilter.blur(sigmaX: 10.0, sigmaY: 10.0),
                      child: Container(
                        padding: EdgeInsets.symmetric(
                            vertical: 4.h, horizontal: 12.w),
                        decoration: BoxDecoration(
                            color: Style.black.withOpacity(0.4),
                            borderRadius:
                                BorderRadius.all(Radius.circular(100.r))),
                        child: Text(
                          "${shop.productsCount ?? 0}  ${AppHelpers.getTranslation(TrKeys.products)}",
                          style: Style.interNormal(
                            size: 12,
                            color: Style.white,
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
