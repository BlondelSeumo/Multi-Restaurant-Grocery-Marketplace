import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpodtemp/domain/di/dependency_manager.dart';
import 'generate_image_notifier.dart';
import 'generate_image_state.dart';


final generateImageProvider = StateNotifierProvider<GenerateImageNotifier, GenerateImageState>(
  (ref) => GenerateImageNotifier(settingsRepository),
);
