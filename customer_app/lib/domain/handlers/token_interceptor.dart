import 'package:dio/dio.dart';
import 'package:riverpodtemp/infrastructure/services/app_constants.dart';
import 'package:riverpodtemp/infrastructure/services/local_storage.dart';

class TokenInterceptor extends Interceptor {
  final bool requireAuth;
  final bool chatGPT;

  TokenInterceptor({
    required this.requireAuth,
    this.chatGPT = false,
  });

  @override
  void onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final String token = LocalStorage.instance.getToken();
    if (token.isNotEmpty && requireAuth) {
      options.headers.addAll({
        'Authorization': 'Bearer  ${chatGPT ? AppConstants.chatGpt : token}'
      });
    }
    handler.next(options);
  }
}
