import 'package:flutter/material.dart';
import 'package:riverpodtemp/domain/di/injection.dart';
import 'package:riverpodtemp/domain/handlers/http_service.dart';
import 'package:riverpodtemp/domain/iterface/products.dart';
import 'package:riverpodtemp/infrastructure/models/models.dart';
import 'package:riverpodtemp/infrastructure/models/request/product_request.dart';
import 'package:riverpodtemp/infrastructure/models/request/search_product.dart';
import 'package:riverpodtemp/infrastructure/services/local_storage.dart';
import '../../../domain/handlers/handlers.dart';

class ProductsRepository implements ProductsRepositoryFacade {
  @override
  Future<ApiResult<ProductsPaginateResponse>> searchProducts({required String text,int? page}) async {
    final data = SearchProductModel(text: text, page: page ?? 1);
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/products/paginate',
        queryParameters: data.toJson(),
      );
      return ApiResult.success(
        data: ProductsPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<SingleProductResponse>> getProductDetails(
      String uuid,
  ) async {
    final data = {
      'currency_id': LocalStorage.instance.getSelectedCurrency().id,
      'lang': LocalStorage.instance.getLanguage()?.locale,
    };
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/products/$uuid',
        queryParameters: data,
      );
      return ApiResult.success(
        data: SingleProductResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> get product details failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<ProductsPaginateResponse>> getProductsByCategoryPaginate({
    String? shopId,
    required int page,
    required int categoryId
  }) async {
    final data = ProductRequest(shopId: shopId!, page: page,categoryId: categoryId);
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/products/paginate',
        queryParameters: data.toJsonByCategory(),
      );
      return ApiResult.success(
        data: ProductsPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> getProductsByCategoryPaginate id failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<ProductsPaginateResponse>> getProductsPaginate({
    String? shopId,
    required int page,
  }) async {
    final data = ProductRequest(shopId: shopId!, page: page);
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/products/paginate',
        queryParameters: data.toJson(),
      );
      return ApiResult.success(
        data: ProductsPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> getProductsPaginate failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<ProductsPaginateResponse>> getProductsPopularPaginate({
    String? shopId,
    required int page,
  }) async {
    final data = ProductRequest(shopId: shopId!, page: page);
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/shops/$shopId/products/recommended/paginate',
        queryParameters: data.toJsonPopular(),
      );
      return ApiResult.success(
        data: ProductsPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> getProductsPopularPaginate failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<ProductsPaginateResponse>> getMostSoldProducts({
    int? shopId,
    int? categoryId,
    int? brandId,
  }) async {
    final data = {
      if (shopId != null) 'shop_id': shopId,
      if (categoryId != null) 'category_id': categoryId,
      if (brandId != null) 'brand_id': brandId,
      'currency_id': LocalStorage.instance.getSelectedCurrency().id,
      'lang': LocalStorage.instance.getLanguage()?.locale,
    };
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/products/most-sold',
        queryParameters: data,
      );
      return ApiResult.success(
        data: ProductsPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> get most sold products failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }



  @override
  Future<ApiResult<ProductsPaginateResponse>> getRelatedProducts(
    int? brandId,
    int? shopId,
    int? categoryId,
  ) async {
    final data = {
      'brand_id': brandId,
      'shop_id': shopId,
      'category_id': categoryId,
      'lang': LocalStorage.instance.getLanguage()?.locale,
    };
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/products/paginate',
        queryParameters: data,
      );
      return ApiResult.success(
        data: ProductsPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> getRelatedProduct failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<ProductCalculateResponse>> getProductCalculations(
    int stockId,
    int quantity,
  ) async {
    final data = {
      'currency_id': LocalStorage.instance.getSelectedCurrency().id,
      'products[0][id]': stockId,
      'products[0][quantity]': quantity,
    };
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/products/calculate',
        queryParameters: data,
      );
      return ApiResult.success(
        data: ProductCalculateResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> get product calculations failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<ProductCalculateResponse>> getAllCalculations(
    List<CartProductData> cartProducts,
  ) async {
    final data = {
      'currency_id': LocalStorage.instance.getSelectedCurrency().id,
    };
    for (int i = 0; i < cartProducts.length; i++) {
      data['products[$i][id]'] = cartProducts[i].selectedStock?.id;
      data['products[$i][quantity]'] = cartProducts[i].quantity;
    }
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/products/calculate',
        queryParameters: data,
      );
      return ApiResult.success(
        data: ProductCalculateResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> get all calculations failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<ProductsPaginateResponse>> getProductsByIds(
    List<int> ids,
  ) async {
    final data = {
      'currency_id': LocalStorage.instance.getSelectedCurrency().id,
      'lang': LocalStorage.instance.getLanguage()?.locale,
    };
    for (int i = 0; i < ids.length; i++) {
      data['products[$i]'] = ids[i];
    }
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/products/ids',
        queryParameters: data,
      );
      return ApiResult.success(
        data: ProductsPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> get products by ids failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<void>> addReview(
    String productUuid,
    String comment,
    double rating,
    String? imageUrl,
  ) async {
    final data = {
      'rating': rating,
     if(comment != "") 'comment': comment,
      if (imageUrl != null) 'images': [imageUrl],
    };
    debugPrint('===> add review data: $data');
    try {
      final client = inject<HttpService>().client(requireAuth: true);
      await client.post(
        '/api/v1/rest/products/review/$productUuid',
        data: data,
      );
      return const ApiResult.success(data: null);
    } catch (e) {
      debugPrint('==> add review failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<ProductsPaginateResponse>> getNewProducts({
    int? shopId,
    int? brandId,
    int? categoryId,
    int? page,
  }) async {
    final data = {
      if (shopId != null) 'shop_id': shopId,
      if (brandId != null) 'brand_id': brandId,
      if (categoryId != null) 'category_id': categoryId,
      if (page != null) 'page': page,
      'sort': 'desc',
      'column': 'created_at',
      'currency_id': LocalStorage.instance.getSelectedCurrency().id,
      'perPage': 14,
      'lang': LocalStorage.instance.getLanguage()?.locale,
    };
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/products/paginate',
        queryParameters: data,
      );
      return ApiResult.success(
        data: ProductsPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> get new products failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<ProductsPaginateResponse>> getDiscountProducts({
    int? shopId,
    int? brandId,
    int? categoryId,
    int? page,
  }) async {
    final data = {
      if (shopId != null) 'shop_id': shopId,
      if (brandId != null) 'brand_id': brandId,
      if (categoryId != null) 'category_id': categoryId,
      if (page != null) 'page': page,
      'currency_id': LocalStorage.instance.getSelectedCurrency().id,
      'perPage': 14,
      'lang': LocalStorage.instance.getLanguage()?.locale,
    };
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/products/discount',
        queryParameters: data,
      );
      return ApiResult.success(
        data: ProductsPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> get discount products failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<ProductsPaginateResponse>> getProfitableProducts({
    int? brandId,
    int? categoryId,
    int? page,
  }) async {
    final data = {
      if (brandId != null) 'brand_id': brandId,
      if (categoryId != null) 'category_id': categoryId,
      if (page != null) 'page': page,
      'profitable': true,
      'currency_id': LocalStorage.instance.getSelectedCurrency().id,
      'perPage': 14,
      'lang': LocalStorage.instance.getLanguage()?.locale,
    };
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/products/discount',
        queryParameters: data,
      );
      return ApiResult.success(
        data: ProductsPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> get profitable products failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }
}
