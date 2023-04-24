import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:riverpodtemp/application/home/home_notifier.dart';
import 'package:riverpodtemp/application/home/home_state.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/tr_keys.dart';
import 'package:riverpodtemp/presentation/components/app_bars/common_app_bar.dart';
import 'package:riverpodtemp/presentation/routes/app_router.gr.dart';
import 'package:riverpodtemp/presentation/theme/app_style.dart';

class AppBarHome extends StatelessWidget {
  final HomeState state;
  final HomeNotifier event;

  const AppBarHome({Key? key, required this.state, required this.event})
      : super(key: key);

  @override
  Widget build(BuildContext context) {
    return CommonAppBar(
        child: GestureDetector(
      onTap: () {
        context.pushRoute( ViewMapRoute());
        event.setAddress();
      },
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.end,
        mainAxisAlignment: MainAxisAlignment.start,
        children: [
          Container(
            decoration: const BoxDecoration(
                shape: BoxShape.circle, color: Style.bgGrey),
            padding: EdgeInsets.all(12.r),
            child: SvgPicture.asset("assets/svgs/adress.svg"),
          ),
          10.horizontalSpace,
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              Text(
                AppHelpers.getTranslation(TrKeys.deliveryAddress),
                style: Style.interNormal(
                  size: 12,
                  color: Style.textGrey,
                ),
              ),
              Row(
                children: [
                  SizedBox(
                    width: MediaQuery.of(context).size.width - 120.w,
                    child: Text(
                      "${state.addressData?.title ?? ""}, ${state.addressData?.address ?? ""}",
                      style: Style.interBold(
                        size: 14,
                        color: Style.black,
                      ),
                      maxLines: 1,
                    ),
                  ),
                  const Icon(Icons.keyboard_arrow_down_sharp)
                ],
              ),
            ],
          )
        ],
      ),
    ));
  }
}
