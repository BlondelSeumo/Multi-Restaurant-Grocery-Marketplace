import 'package:riverpodtemp/infrastructure/services/local_storage.dart';

import '../../services/app_constants.dart';

class CategoryModel {
  final int page;

  CategoryModel({
    required this.page
  });

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{};
    map["lang"] = LocalStorage.instance.getLanguage()?.locale ?? "en";
    map["page"] = page;
    map["type"] = "shop";
    map["perPage"] = 10;
    map["address"] = {
      "latitude" : LocalStorage.instance.getAddressSelected()?.location?.latitude ?? AppConstants.demoLatitude,
      "longitude" : LocalStorage.instance.getAddressSelected()?.location?.longitude ?? AppConstants.demoLongitude
    };
    return map;
  }

  Map<String, dynamic> toJsonShop() {
    final map = <String, dynamic>{};
    map["lang"] = LocalStorage.instance.getLanguage()?.locale ?? "en";
    map["perPage"] = 100;
    return map;
  }
}
