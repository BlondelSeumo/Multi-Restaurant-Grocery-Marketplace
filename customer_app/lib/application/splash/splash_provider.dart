import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpodtemp/domain/di/dependency_manager.dart';

import 'splash_notifier.dart';
import 'splash_state.dart';

final splashProvider = StateNotifierProvider<SplashNotifier, SplashState>(
  (ref) => SplashNotifier(settingsRepository,),
);
