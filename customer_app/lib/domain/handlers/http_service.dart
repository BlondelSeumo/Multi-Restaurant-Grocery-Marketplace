import 'package:dio/dio.dart';
import 'package:riverpodtemp/infrastructure/services/app_constants.dart';

import 'token_interceptor.dart';

class HttpService {

  Dio client({bool requireAuth = false,bool routing = false,bool chatGpt = false}) => Dio(
    BaseOptions(
      baseUrl: chatGpt ? "https://api.openai.com" : routing ? AppConstants.drawingBaseUrl : AppConstants.baseUrl,
      connectTimeout: 60 * 1000,
      receiveTimeout: 60 * 1000,
      sendTimeout: 60 * 1000,
      headers: {
        'Accept':
        'application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8',
        'Content-type': 'application/json'
      },
    ),
  )
    ..interceptors.add(TokenInterceptor(requireAuth: requireAuth,chatGPT: chatGpt))
    ..interceptors.add(LogInterceptor(
      responseHeader: false,
      requestHeader: true,
      responseBody: true,
      requestBody: true
    ));
}
