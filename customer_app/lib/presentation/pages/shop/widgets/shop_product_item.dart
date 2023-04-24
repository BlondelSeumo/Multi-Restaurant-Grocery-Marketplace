
import 'package:flutter/material.dart';
import 'package:flutter_remix/flutter_remix.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:riverpodtemp/infrastructure/models/data/bonus_data.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/local_storage.dart';
import 'package:riverpodtemp/presentation/components/buttons/animation_button_effect.dart';
import 'package:riverpodtemp/presentation/components/custom_network_image.dart';
import 'package:riverpodtemp/presentation/theme/theme.dart';

import 'bonus_screen.dart';
import 'package:intl/intl.dart' as intl;

class ShopProductItem extends StatelessWidget {
  final String productImage;
  final String productName;
  final String productDescription;
  final num? price;
  final num? discount;
  final int count;
  final bool isBonus;
  final BonusModel? bonus;

  const ShopProductItem(
      {Key? key,
      required this.productImage,
      required this.productName,
      required this.productDescription,
      required this.price,
      required this.count,
      required this.isBonus,
      required this.bonus,
      required this.discount})
      : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: EdgeInsets.all(4.r),
      decoration: BoxDecoration(
          color: Style.white,
          borderRadius: BorderRadius.all(Radius.circular(10.r))),
      child: Padding(
        padding: EdgeInsets.all(14.r),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            CustomNetworkImage(
                url: productImage,
                height: 100.h,
                width: double.infinity,
                radius: 0),
            8.verticalSpace,
            Text(
              productName,
              style: Style.interNoSemi(
                size: 14,
                color: Style.black,
              ),
              maxLines: 2,
            ),
            Text(
              productDescription,
              style: Style.interRegular(
                size: 12,
                color: Style.textGrey,
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
            Row(
              crossAxisAlignment: CrossAxisAlignment.end,
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      intl.NumberFormat.currency(
                        symbol:
                            LocalStorage.instance.getSelectedCurrency().symbol,
                      ).format(discount ?? price),
                      style: Style.interNoSemi(
                          size: 16,
                          color: Style.black,
                          decoration: discount == null
                              ? TextDecoration.none
                              : TextDecoration.lineThrough),
                    ),
                    discount == null
                        ? const SizedBox.shrink()
                        : Container(
                            margin: EdgeInsets.only(top: 8.r),
                            decoration: BoxDecoration(
                                color: Style.redBg,
                                borderRadius: BorderRadius.circular(30.r)),
                            padding: EdgeInsets.all(4.r),
                            child: Row(
                              children: [
                                SvgPicture.asset("assets/svgs/discount.svg"),
                                8.horizontalSpace,
                                Text(
                                  intl.NumberFormat.currency(
                                    symbol: LocalStorage.instance
                                        .getSelectedCurrency()
                                        .symbol,
                                  ).format((price ?? 0)),
                                  style: Style.interNoSemi(
                                      size: 12, color: Style.red),
                                )
                              ],
                            ),
                          ),
                  ],
                ),
                isBonus
                    ? AnimationButtonEffect(
                        child: InkWell(
                          onTap: () {
                            AppHelpers.showCustomModalBottomSheet(
                              paddingTop: MediaQuery.of(context).padding.top,
                              context: context,
                              modal: BonusScreen(
                                bonus: bonus,
                              ),
                              isDarkMode: false,
                              isDrag: true,
                              radius: 12,
                            );
                          },
                          child: Container(
                            width: 22.w,
                            height: 22.h,
                            margin: EdgeInsets.only(
                                top: 8.r, left: 8.r, right: 4.r, bottom: 4.r),
                            decoration: const BoxDecoration(
                                shape: BoxShape.circle, color: Style.blueBonus),
                            child: Icon(
                              FlutterRemix.gift_2_fill,
                              size: 16.r,
                              color: Style.white,
                            ),
                          ),
                        ),
                      )
                    : const SizedBox.shrink()
              ],
            ),
          ],
        ),
      ),
    );
  }
}
