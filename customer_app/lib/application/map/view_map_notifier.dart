import 'package:flutter/cupertino.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:riverpodtemp/infrastructure/models/models.dart';
import 'package:riverpodtemp/infrastructure/services/app_connectivity.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/local_storage.dart';

import '../../domain/iterface/shops.dart';
import '../../infrastructure/services/tr_keys.dart';
import 'view_map_state.dart';

class ViewMapNotifier extends StateNotifier<ViewMapState> {
  final ShopsRepositoryFacade _shopsRepository;

  ViewMapNotifier(this._shopsRepository) : super(const ViewMapState());

  void changePlace(AddressData place) {
    state = state.copyWith(place: place, isSetAddress: true);
  }

  void checkAddress() {
    AddressData? data = LocalStorage.instance.getAddressSelected();
    if (data == null) {
      state = state.copyWith(isSetAddress: false);
    } else {
      state = state.copyWith(isSetAddress: true);
    }
  }

  updateActive() {
    state = state.copyWith(isLoading: true);
  }

  Future<void> checkDriverZone({required BuildContext context,required LatLng location,int? shopId}) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      state = state.copyWith(isLoading: true, isActive: false);
      final response = await _shopsRepository.checkDriverZone(location,shopId: shopId);
      response.when(
        success: (data) async {
          state = state.copyWith(isLoading: false, isActive: data);
          if (!data) {
            AppHelpers.showCheckTopSnackBarInfo(
              context,
              AppHelpers.getTranslation(TrKeys.noDriverZone),
            );
          }
        },
        failure: (activeFailure, status) {
          state = state.copyWith(isLoading: false);
          if (status != 400) {
            AppHelpers.showCheckTopSnackBar(
              context,
              AppHelpers.getTranslation(status.toString()),
            );
          }
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }
}
