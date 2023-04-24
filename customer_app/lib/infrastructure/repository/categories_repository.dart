import 'package:riverpodtemp/domain/di/injection.dart';
import 'package:riverpodtemp/domain/handlers/http_service.dart';
import 'package:riverpodtemp/domain/iterface/categories.dart';
import 'package:riverpodtemp/infrastructure/models/models.dart';
import 'package:riverpodtemp/infrastructure/models/request/category_request.dart';
import 'package:riverpodtemp/infrastructure/models/request/search_shop.dart';

import '../../../domain/handlers/handlers.dart';

class CategoriesRepository implements CategoriesRepositoryFacade {
  @override
  Future<ApiResult<CategoriesPaginateResponse>> getAllCategories(
      {required int page}) async {
    final data = CategoryModel(page: page);

    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/categories/paginate',
        queryParameters: data.toJson(),
      );
      return ApiResult.success(
        data: CategoriesPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      return ApiResult.failure(
          error: NetworkExceptions.getDioException(e),
          statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<CategoriesPaginateResponse>> searchCategories(
      {required String text}) async {
    final data = SearchShopModel(text: text);

    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/categories/search',
        queryParameters: data.toJson(),
      );
      return ApiResult.success(
        data: CategoriesPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      return ApiResult.failure(
          error: NetworkExceptions.getDioException(e),
          statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<CategoriesPaginateResponse>> getCategoriesByShop(
      {required String shopId}) async {
    final data = CategoryModel(page: 1);
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/shops/$shopId/categories',
        queryParameters: data.toJsonShop()
      );
      return ApiResult.success(
        data: CategoriesPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      return ApiResult.failure(
          error: NetworkExceptions.getDioException(e),
          statusCode: NetworkExceptions.getDioStatus(e));
    }
  }
}
