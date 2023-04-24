import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpodtemp/domain/di/dependency_manager.dart';
import 'view_map_notifier.dart';
import 'view_map_state.dart';

final viewMapProvider = StateNotifierProvider<ViewMapNotifier, ViewMapState>(
  (ref) => ViewMapNotifier(shopsRepository),
);
