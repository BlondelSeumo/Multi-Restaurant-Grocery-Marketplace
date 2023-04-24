import 'package:flutter/material.dart';
import 'package:flutter_displaymode/flutter_displaymode.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:pull_to_refresh/pull_to_refresh.dart';
import 'package:riverpodtemp/application/app_widget/app_provider.dart';
import 'package:riverpodtemp/presentation/theme/app_style.dart';

import '../domain/di/dependency_manager.dart';
import '../infrastructure/services/local_storage.dart';
import 'components/custom_range_slider.dart';
import 'routes/app_router.gr.dart';

class AppWidget extends ConsumerWidget {
  AppWidget({Key? key}) : super(key: key);

  final appRouter = AppRouter();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final state = ref.refresh(appProvider);
    return FutureBuilder(
        future: Future.wait([
          setUpDependencies(),
          LocalStorage.getInstance(),
          FlutterDisplayMode.setHighRefreshRate()
        ]),
        builder: (context, AsyncSnapshot<List<dynamic>> snapshot) {
          return ScreenUtilInit(
            designSize: const Size(375, 812),
            builder: (context, child) {
              return RefreshConfiguration(
                footerBuilder: () => const ClassicFooter(
                  idleIcon: SizedBox(),
                  idleText: "",
                ),
                headerBuilder: () => const WaterDropMaterialHeader(
                  backgroundColor: Style.white,
                  color: Style.textGrey,
                ),
                child: MaterialApp.router(
                  debugShowCheckedModeBanner: false,
                  routerDelegate: appRouter.delegate(),
                  routeInformationParser: appRouter.defaultRouteParser(),
                  locale: Locale(state.activeLanguage?.locale ?? 'en'),
                  theme: ThemeData(
                    sliderTheme: SliderThemeData(
                      overlayShape: SliderComponentShape.noOverlay,
                      rangeThumbShape: CustomRoundRangeSliderThumbShape(
                        enabledThumbRadius: 12.r,
                      ),
                    ),
                  ),
                  themeMode:
                      state.isDarkMode ? ThemeMode.dark : ThemeMode.light,
                ),
              );
            },
          );
        });
  }
}
