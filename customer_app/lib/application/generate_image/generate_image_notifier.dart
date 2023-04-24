
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';

import '../../domain/iterface/settings.dart';
import '../../infrastructure/services/app_connectivity.dart';
import 'generate_image_state.dart';




class GenerateImageNotifier extends StateNotifier<GenerateImageState> {
  final SettingsRepositoryFacade _settingsRepository;
  GenerateImageNotifier(this._settingsRepository) : super(const GenerateImageState());

  Future<void> generateImage(BuildContext context,String name) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      state = state.copyWith(isLoading: true);
      final response = await _settingsRepository.getGenerateImage(name);
      response.when(
        success: (data) async {
          state = state.copyWith(
            isLoading: false,
            data: data,
          );
        },
        failure: (activeFailure,status) {
          state = state.copyWith(isLoading: false);
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

}
