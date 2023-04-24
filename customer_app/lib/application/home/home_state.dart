import 'package:freezed_annotation/freezed_annotation.dart';
import 'package:riverpodtemp/infrastructure/models/models.dart';

import '../../infrastructure/models/data/story_data.dart';

part 'home_state.freezed.dart';

@freezed
class HomeState with _$HomeState {
  const factory HomeState({
    @Default(true) bool isCategoryLoading,
    @Default(true) bool isBannerLoading,
    @Default(true) bool isShopLoading,
    @Default(true) bool isRestaurantLoading,
    @Default(true) bool isRestaurantNewLoading,
    @Default(true) bool isStoryLoading,
    @Default(true) bool isShopRecommendLoading,
    @Default(-1) int selectIndexCategory,
    @Default(0) int isSelectCategoryLoading,
    @Default(null) AddressData? addressData,
    @Default([]) List<CategoryData> categories,
    @Default([]) List<BannerData> banners,
    @Default(null) BannerData? banner,
    @Default([]) List<ShopData> shops,
    @Default([]) List<ShopData> restaurant,
    @Default([]) List<ShopData> newRestaurant,
    @Default([]) List<List<StoryModel?>?>? story,
    @Default([]) List<ShopData> shopsRecommend,
    @Default([]) List<ShopData> filterShops,
    @Default([]) List<ShopData> filterMarket,
  }) = _HomeState;

  const HomeState._();


}