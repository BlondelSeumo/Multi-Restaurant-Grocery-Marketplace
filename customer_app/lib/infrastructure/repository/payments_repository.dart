

import 'package:flutter/material.dart';
import 'package:riverpodtemp/domain/di/injection.dart';
import 'package:riverpodtemp/domain/handlers/http_service.dart';
import 'package:riverpodtemp/domain/iterface/payments.dart';
import 'package:riverpodtemp/infrastructure/models/models.dart';
import 'package:riverpodtemp/infrastructure/services/local_storage.dart';
import '../../../domain/handlers/handlers.dart';

class PaymentsRepository implements PaymentsRepositoryFacade {
  @override
  Future<ApiResult<PaymentsResponse>> getPayments() async {
    final data = {'lang': LocalStorage.instance.getLanguage()?.locale};
    try {
      final client = inject<HttpService>().client(requireAuth: false);
      final response =
          await client.get('/api/v1/rest/payments', queryParameters: data);
      return ApiResult.success(
        data: PaymentsResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> get payments failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }

  @override
  Future<ApiResult<TransactionsResponse>> createTransaction({
    required int orderId,
    required int paymentId,
  }) async {
    final data = {'payment_sys_id': paymentId};
    try {
      final client = inject<HttpService>().client(requireAuth: true);
      final response = await client.post(
        '/api/v1/payments/order/$orderId/transactions',
        data: data,
      );
      return ApiResult.success(
        data: TransactionsResponse.fromJson(response.data),
      );
    } catch (e) {
      debugPrint('==> create transaction failure: $e');
      return ApiResult.failure(error: NetworkExceptions.getDioException(e),statusCode: NetworkExceptions.getDioStatus(e));
    }
  }
}
