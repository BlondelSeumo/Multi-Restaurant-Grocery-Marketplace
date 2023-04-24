

import '../../infrastructure/models/data/cart_data.dart';
import '../../infrastructure/models/request/cart_request.dart';
import '../handlers/api_result.dart';

abstract class CartRepositoryFacade{

  Future<ApiResult<CartModel>> getCart();

  Future<ApiResult<CartModel>> getCartInGroup(String? cartId,String? shopId,String? cartUuid);

  Future<ApiResult<dynamic>> startGroupOrder({required int cartId});

  Future<ApiResult<dynamic>> changeStatus({required String? userUuid,required String? cartId});

  Future<ApiResult<CartModel>> deleteCart({required int cartId});

  Future<ApiResult<dynamic>> deleteUser({required int cartId,required String userId});

  Future<ApiResult<CartModel>> removeProductCart({required int cartDetailId,List<int> listOfId});

  Future<ApiResult<CartModel>> createAndCart({required CartRequest cart});

  Future<ApiResult<CartModel>> insertCart({required CartRequest cart});

  Future<ApiResult<CartModel>> insertCartWithGroup({required CartRequest cart});

  Future<ApiResult<CartModel>> createCart({required CartRequest cart});

}