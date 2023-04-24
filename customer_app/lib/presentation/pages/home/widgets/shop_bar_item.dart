
import 'dart:ui';

import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:pull_to_refresh/pull_to_refresh.dart';
import 'package:riverpodtemp/infrastructure/models/data/story_data.dart';
import 'package:riverpodtemp/presentation/components/custom_network_image.dart';
import 'package:riverpodtemp/presentation/components/shop_avarat.dart';
import 'package:riverpodtemp/presentation/routes/app_router.gr.dart';
import 'package:riverpodtemp/presentation/theme/theme.dart';

class ShopBarItem extends StatelessWidget {
  final RefreshController controller;
  final StoryModel? story;
  final int index;

  const ShopBarItem({
    Key? key,
    required this.story,
    required this.controller,
    required this.index,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () {
        context.pushRoute(StoryList(controller: controller, index: index));
      },
      child:

      Container(
        margin: EdgeInsets.only(right: 9.r),
        width: 110.w,
        color: Style.transparent,
        child: Stack(
          children: [
            SizedBox(
              height: 164.h,
              width: 110.w,
              child: CustomNetworkImage(
                url: story?.url ?? "",
                height: 164.h,
                width: 110.w,
                radius: 8.r,
              ),
            ),
            Align(
              alignment: Alignment.topCenter,
              child: Padding(
                padding: EdgeInsets.only(top: 12.h,right: 8.w,left: 8.w),
                child: ClipRRect(
                  borderRadius: BorderRadius.all(Radius.circular(100.r)),
                  child: BackdropFilter(
                    filter: ImageFilter.blur(sigmaX: 10.0, sigmaY: 10.0),
                    child: Container(
                      padding:
                          EdgeInsets.symmetric(vertical: 4.h, horizontal: 12.w),
                      decoration: BoxDecoration(
                          color: Style.black.withOpacity(0.4),
                          borderRadius:
                              BorderRadius.all(Radius.circular(100.r))),
                      child: Text(
                        story?.productTitle ?? "",
                        style: Style.interNormal(
                          size: 12,
                          color: Style.white,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  ),
                ),
              ),
            ),
            Align(
              alignment: Alignment.bottomCenter,
              child: Padding(
                padding: EdgeInsets.only(top: 70.h),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    ShopAvatar(
                      shopImage: story?.logoImg ?? "",
                      size: 46,
                      padding: 6,
                      bgColor: Style.white.withOpacity(0.65),
                    ),
                    6.verticalSpace,
                    Text(
                      story?.title ?? "",
                      style: Style.interNormal(
                        size: 12,
                        color: Style.black,
                      ),
                      maxLines: 1,
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
