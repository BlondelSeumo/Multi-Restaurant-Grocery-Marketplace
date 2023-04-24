import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter_remix/flutter_remix.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/tr_keys.dart';
import 'package:riverpodtemp/presentation/routes/app_router.gr.dart';
import '../../../theme/theme.dart';

class NoConnectionPage extends ConsumerWidget {
  const NoConnectionPage({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Scaffold(
      backgroundColor: Style.white,
      body: Column(
        children: [
          const SizedBox(height: 200, width: double.infinity),
          const Icon(
            FlutterRemix.wifi_off_fill,
            size: 120,
            color: Style.black,
          ),
          const SizedBox(height: 20),
          Text(
            AppHelpers.getTranslation(TrKeys.noInternetConnection),
            style: Style.interNoSemi(
              size: 18,
              color: Style.black,
            ),
          ),
          const SizedBox(height: 40),
          ElevatedButton(
            onPressed: () => context.replaceRoute(const SplashRoute()),
            child: const Icon(
              FlutterRemix.restart_fill,
              color: Style.black,
              size: 40,
            ),
          ),
        ],
      ),
    );
  }
}
