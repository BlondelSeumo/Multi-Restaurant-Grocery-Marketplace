import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpodtemp/domain/di/dependency_manager.dart';

import 'login_notifier.dart';
import 'login_state.dart';

final loginProvider =
    StateNotifierProvider.autoDispose<LoginNotifier, LoginState>(
  (ref) => LoginNotifier(
    authRepository,
    settingsRepository,
    userRepository
  ),
);
