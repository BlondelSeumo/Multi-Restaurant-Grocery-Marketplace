import '../../services/local_storage.dart';

class CartRequest {
  final int? shopId;
  final String? cartId;
  final String? userUuid;
  final int? stockId;
  final int? parentId;
  final int? quantity;
  final List<CartRequest>? carts;

  CartRequest({
    this.shopId,
    this.stockId,
    this.parentId,
    this.quantity,
    this.carts,
    this.cartId,
    this.userUuid,
  });

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{};
    if (shopId != null) map["shop_id"] = shopId;
    if (cartId != null) map["cart_id"] = cartId;
    if (userUuid != null) map["user_cart_uuid"] = userUuid;
    if (stockId != null) map["stock_id"] = stockId;
    if (parentId != null) map["parent_id"] = parentId;
    if (quantity != null) map["quantity"] = quantity;
    map["rate"] = LocalStorage.instance.getSelectedCurrency().rate ?? 1;
    map["currency_id"] = LocalStorage.instance.getSelectedCurrency().id ?? 0;
    return map;
  }

  Map<String, dynamic> toJsonInsert() {
    final map = <String, dynamic>{};
    if (shopId != null) map["shop_id"] = shopId;
    map["rate"] = LocalStorage.instance.getSelectedCurrency().rate ?? 1;
    map["currency_id"] = LocalStorage.instance.getSelectedCurrency().id ?? 0;
    if (cartId != null) map["cart_id"] = cartId;
    if (userUuid != null) map["user_cart_uuid"] = userUuid;
    if (carts != null) map["products"] = toJsonCart();
    return map;
  }

  List<Map<String, dynamic>> toJsonCart() {
    List<Map<String, dynamic>> list = [];
    carts?.forEach((element) {
      final map = <String, dynamic>{};
      map["stock_id"] = element.stockId;
      map["quantity"] = element.quantity;
      if (element.parentId != null) map["parent_id"] = element.parentId;
      if (cartId != null) map["cart_id"] = cartId;
      if (userUuid != null) map["user_cart_uuid"] = userUuid;
      list.add(map);
    });

    return list;
  }
}
