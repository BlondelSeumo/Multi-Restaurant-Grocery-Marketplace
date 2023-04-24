
import 'package:riverpodtemp/infrastructure/models/models.dart';
import 'package:riverpodtemp/infrastructure/services/app_constants.dart';
import '../../domain/handlers/handlers.dart';

abstract class GalleryRepositoryFacade {
  Future<ApiResult<GalleryUploadResponse>> uploadImage(
      String file,
      UploadType uploadType,
      );
}
