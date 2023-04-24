import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpodtemp/presentation/routes/app_router.gr.dart';
import '../../../../application/splash/splash_provider.dart';
import 'package:flutter_native_splash/flutter_native_splash.dart';

class SplashPage extends ConsumerStatefulWidget {
  const SplashPage({Key? key}) : super(key: key);

  @override
  ConsumerState<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends ConsumerState<SplashPage> {


  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.read(splashProvider.notifier).getToken(
        context,
        goMain: () {
          context.replaceRoute(const MainRoute());
          ref.read(splashProvider.notifier).getTranslations(context);
          FlutterNativeSplash.remove();
        },
        goLogin: () {
          context.replaceRoute(const LoginRoute());
          ref.read(splashProvider.notifier).getTranslations(context);
          FlutterNativeSplash.remove();
        },
      );
    });

  }



  @override
  Widget build(BuildContext context) {
    return Image.asset(
      "assets/images/splash.png",
      fit: BoxFit.fill,
    );
  }
}
