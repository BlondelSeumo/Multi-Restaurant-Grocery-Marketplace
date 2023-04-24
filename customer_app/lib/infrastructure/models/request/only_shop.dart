

import 'package:riverpodtemp/infrastructure/services/local_storage.dart';

class OnlyShopRequest {
  final String? lan;
  OnlyShopRequest({
    this.lan,
  });

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{};
    map["lang"] = LocalStorage.instance.getLanguage()?.locale ?? "en";
    map["currency_id"] = LocalStorage.instance.getSelectedCurrency().id;
    return map;
  }
}