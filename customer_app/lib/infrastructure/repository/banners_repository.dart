import 'package:flutter/material.dart';
import 'package:riverpodtemp/domain/di/injection.dart';
import 'package:riverpodtemp/domain/handlers/http_service.dart';
import 'package:riverpodtemp/domain/iterface/banners.dart';
import 'package:riverpodtemp/infrastructure/models/models.dart';
import 'package:riverpodtemp/infrastructure/models/request/banners_request.dart';
import 'package:riverpodtemp/infrastructure/services/local_storage.dart';
import '../../../domain/handlers/handlers.dart';


class BannersRepository implements BannersRepositoryFacade {
  @override
  Future<ApiResult<BannersPaginateResponse>> getBannersPaginate(
      {required int page}) async {
    final data = BannersRequest(page: page);
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response = await client.get(
        '/api/v1/rest/banners/paginate',
        queryParameters: data.toJson(),
      );
      return ApiResult.success(
        data: BannersPaginateResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> get banner products failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),
          statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<BannerData>> getBannerById(int? bannerId,) async {
    final data = {
    'lang': LocalStorage.instance.getLanguage()?.locale,
    "perPage" : 100
  };
    try {
    final client = inject<HttpService>().client(requireAuth: false);
    final response = await client.get(
    '/api/v1/rest/banners/$bannerId',
    queryParameters: data,
    );
    return ApiResult.success(
    data: BannerData.fromJson(response.data["data"]),
    );
    } catch (e) {
    debugPrint('==> get banner products failure: $e');
    return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<void>> likeBanner(int? bannerId) async {
    try {
      final client = inject<HttpService>().client(requireAuth: true);
      await client.post('/api/v1/rest/banners/$bannerId/liked');
      return const ApiResult.success(data: null);
    } catch (e) {
      debugPrint('==> like banner failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),
          statusCode: NetworkExceptions.getDioStatus(e));
    }
  }
}
