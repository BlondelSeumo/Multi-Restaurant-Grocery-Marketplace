import 'package:riverpodtemp/infrastructure/services/local_storage.dart';

class ProductRequest {
  final String shopId;
  final int page;
  final int? categoryId;

  ProductRequest( {
    required this.shopId,
    required this.page,
    this.categoryId,
  });

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{};
    map["shop_id"] = shopId;
    map["lang"] = LocalStorage.instance.getLanguage()?.locale ?? "en";
    map["currency_id"] = LocalStorage.instance.getSelectedCurrency().id;
    map["page"] = page;
    map["perPage"] = 6;
    return map;
  }

  Map<String, dynamic> toJsonPopular() {
    final map = <String, dynamic>{};
    map["lang"] = LocalStorage.instance.getLanguage()?.locale ?? "en";
    map["currency_id"] = LocalStorage.instance.getSelectedCurrency().id;
    map["page"] = page;
    map["perPage"] = 6;
    return map;
  }

  Map<String, dynamic> toJsonByCategory() {
    final map = <String, dynamic>{};
    map["shop_id"] = shopId;
    map["lang"] = LocalStorage.instance.getLanguage()?.locale ?? "en";
    map["currency_id"] = LocalStorage.instance.getSelectedCurrency().id;
    map["page"] = page;
    map["category_id"] = categoryId;
    map["perPage"] = 6;
    return map;
  }
}
