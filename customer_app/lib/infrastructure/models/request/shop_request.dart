import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/local_storage.dart';

import '../../services/app_constants.dart';

class ShopRequest {
  final int page;
  final String? type;
  final int? take;
  final bool? freeDelivery;
  final bool? onlyOpen;
  final bool? deals;
  final String? rating;
  final List<double>? price;
  final String? orderBy;

  ShopRequest(
      {this.orderBy,
      this.price,
      this.take,
      this.freeDelivery,
      this.onlyOpen,
      this.deals,
      this.rating,
      required this.type,
      required this.page});

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{};
    map["currency_id"] = LocalStorage.instance.getSelectedCurrency().id;
    if (price != null) {
      for (int i = 0; i < price!.length; i++) {
        map["prices[$i]"] = price?[i];
      }
    }
    if (type != null) {
      map["type"] = type;
    }
    map["page"] = page;
    if (take != null) {
      map["take"] = take;
    }
    if (freeDelivery != null && (freeDelivery ?? false)) {
      map["free_delivery"] = freeDelivery;
    }
    if (deals != null && (deals ?? false)) {
      map["deals"] = deals;
    }
    if (onlyOpen != null && (onlyOpen ?? false)) {
      map["open"] = 1;
    }
    if (rating != null && (rating?.isNotEmpty ?? false)) {
      if (rating!.contains("-")) {
        map["rating[0]"] = rating!.substring(0, rating!.indexOf("-"));
        map["rating[1]"] = rating!.substring(rating!.indexOf("-") + 1);
      } else {
        map["rating[0]"] = rating;
      }
    }
    if (orderBy != null && (orderBy?.isNotEmpty ?? false)) {
      map["order_by"] = AppHelpers.getOrderByString(orderBy!);
    }
    map["perPage"] = 5;
    map["lang"] = LocalStorage.instance.getLanguage()?.locale ?? "en";
    map["address"] = {
      "latitude":
          LocalStorage.instance.getAddressSelected()?.location?.latitude ??
              AppConstants.demoLatitude,
      "longitude":
          LocalStorage.instance.getAddressSelected()?.location?.longitude ??
              AppConstants.demoLongitude
    };
    return map;
  }
}
