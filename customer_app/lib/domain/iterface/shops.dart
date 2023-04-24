import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:riverpodtemp/infrastructure/models/data/filter_model.dart';
import 'package:riverpodtemp/infrastructure/models/response/branches_response.dart';

import '../../domain/handlers/handlers.dart';
import '../../infrastructure/models/data/story_data.dart';
import '../../infrastructure/models/models.dart';
import '../../infrastructure/models/response/tag_response.dart';

abstract class ShopsRepositoryFacade {
  Future<ApiResult<ShopsPaginateResponse>> getShopFilter(
      {int? categoryId, required int page, required String type});

  Future<ApiResult<ShopsPaginateResponse>> getPickupShops();

  Future<ApiResult<ShopsPaginateResponse>> searchShops(
      {required String text, int? categoryId});

  Future<ApiResult<ShopsPaginateResponse>> getNearbyShops(
    double latitude,
    double longitude,
  );

  Future<ApiResult<ShopsPaginateResponse>> getAllShops(int page,
      {String? type, FilterModel? filterModel, required bool isOpen});

  Future<ApiResult<TagResponse>> getTags();

  Future<ApiResult<bool>> checkDriverZone(LatLng location, {int? shopId});

  Future<ApiResult<PriceModel>> getSuggestPrice();

  Future<ApiResult<ShopsPaginateResponse>> getShopsRecommend(int page);

  Future<ApiResult<List<List<StoryModel?>?>?>> getStory(int page);

  Future<ApiResult<SingleShopResponse>> getSingleShop({required String uuid});

  Future<ApiResult<dynamic>> joinOrder({
    required String shopId,
    required String name,
    required String cartId,
  });

  Future<ApiResult<BranchResponse>> getShopBranch({required String uuid});

  Future<ApiResult<ShopsPaginateResponse>> getShopsByIds(
    List<int> shopIds,
  );

  Future<ApiResult<void>> createShop({
    required double tax,
    required double deliveryTo,
    required double deliveryFrom,
    required String deliveryType,
    required String phone,
    required String name,
    required String type,
    required num category,
    required String description,
    required double startPrice,
    required double perKm,
    required AddressData address,
    String? logoImage,
    String? backgroundImage,
  });
}
