import '../../domain/handlers/handlers.dart';
import '../../infrastructure/models/models.dart';

abstract class ProductsRepositoryFacade {
  Future<ApiResult<ProductsPaginateResponse>> searchProducts({required String text,int page});

  Future<ApiResult<SingleProductResponse>> getProductDetails(String uuid);

  Future<ApiResult<ProductsPaginateResponse>> getProductsPaginate({
    String? shopId,
    required int page,
  });

  Future<ApiResult<ProductsPaginateResponse>> getProductsPopularPaginate({
    String? shopId,
    required int page,
  });

  Future<ApiResult<ProductsPaginateResponse>> getProductsByCategoryPaginate({
    String? shopId,
    required int page,
    required int categoryId
  });

  Future<ApiResult<ProductsPaginateResponse>> getMostSoldProducts({
    int? shopId,
    int? categoryId,
    int? brandId,
  });

  Future<ApiResult<ProductsPaginateResponse>> getRelatedProducts(
    int? brandId,
    int? shopId,
    int? categoryId,
  );

  Future<ApiResult<ProductCalculateResponse>> getProductCalculations(
    int stockId,
    int quantity,
  );

  Future<ApiResult<ProductCalculateResponse>> getAllCalculations(
    List<CartProductData> cartProducts,
  );

  Future<ApiResult<ProductsPaginateResponse>> getProductsByIds(
    List<int> ids,
  );

  Future<ApiResult<void>> addReview(
    String productUuid,
    String comment,
    double rating,
    String? imageUrl,
  );

  Future<ApiResult<ProductsPaginateResponse>> getNewProducts({
    int? shopId,
    int? brandId,
    int? categoryId,
    int? page,
  });

  Future<ApiResult<ProductsPaginateResponse>> getDiscountProducts({
    int? shopId,
    int? brandId,
    int? categoryId,
    int? page,
  });

  Future<ApiResult<ProductsPaginateResponse>> getProfitableProducts({
    int? brandId,
    int? categoryId,
    int? page,
  });
}
