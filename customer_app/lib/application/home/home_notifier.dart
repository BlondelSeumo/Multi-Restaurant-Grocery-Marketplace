import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:pull_to_refresh/pull_to_refresh.dart';
import 'package:riverpodtemp/domain/iterface/banners.dart';
import 'package:riverpodtemp/domain/iterface/categories.dart';
import 'package:riverpodtemp/domain/iterface/shops.dart';
import 'package:riverpodtemp/infrastructure/models/data/filter_model.dart';
import 'package:riverpodtemp/infrastructure/models/data/story_data.dart';
import 'package:riverpodtemp/infrastructure/models/models.dart';
import 'package:riverpodtemp/infrastructure/services/app_connectivity.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/local_storage.dart';

import 'home_state.dart';

class HomeNotifier extends StateNotifier<HomeState> {
  final CategoriesRepositoryFacade _categoriesRepository;
  final ShopsRepositoryFacade _shopsRepository;
  final BannersRepositoryFacade _bannersRepository;

  HomeNotifier(this._categoriesRepository, this._bannersRepository,
      this._shopsRepository)
      : super(
          const HomeState(),
        );
  int categoryIndex = 1;
  int shopIndex = 1;
  int newShopIndex = 1;
  int marketIndex = 1;
  int storyIndex = 1;
  int bannerIndex = 1;
  int shopRefreshIndex = 1;
  int marketRefreshIndex = 1;

  void setAddress([AddressData? data]) async {
    AddressData? addressData = LocalStorage.instance.getAddressSelected();
    state = state.copyWith(addressData: data ?? addressData);
  }

  void setSelectCategory(int index, BuildContext context) {
    if (state.selectIndexCategory == index) {
      state =
          state.copyWith(selectIndexCategory: -1, isSelectCategoryLoading: 0);
    } else {
      state = state.copyWith(
        selectIndexCategory: index,
      );
      fetchFilterRestaurant(context);
      fetchFilterShop(context);
    }
  }

  Future<void> fetchCategories(BuildContext context) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      state = state.copyWith(isCategoryLoading: true);
      final response = await _categoriesRepository.getAllCategories(page: 1);
      response.when(
        success: (data) async {
          state = state.copyWith(
            isCategoryLoading: false,
            categories: data.data ?? [],
          );
        },
        failure: (activeFailure, status) {
          state = state.copyWith(isCategoryLoading: false);
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchBannerById(BuildContext context,int bannerId) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      state = state.copyWith(isBannerLoading: true);
      final response = await _bannersRepository.getBannerById(bannerId);
      response.when(
        success: (data) async {
          state = state.copyWith(
            isBannerLoading: false,
            banner: data,
          );
        },
        failure: (activeFailure, status) {
          state = state.copyWith(isBannerLoading: false);
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchCategoriesPage(
      BuildContext context, RefreshController controller,
      {bool isRefresh = false}) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      if (isRefresh) {
        categoryIndex = 1;
      }
      final response = await _categoriesRepository.getAllCategories(
          page: isRefresh ? 1 : ++categoryIndex);
      response.when(
        success: (data) async {
          if (isRefresh) {
            state = state.copyWith(
              categories: data.data ?? [],
            );
            controller.refreshCompleted();
          } else {
            if (data.data?.isNotEmpty ?? false) {
              List<CategoryData> list = List.from(state.categories);
              list.addAll(data.data!);
              state = state.copyWith(
                categories: list,
              );
              controller.loadComplete();
            } else {
              categoryIndex--;
              controller.loadNoData();
            }
          }
        },
        failure: (activeFailure, status) {
          if (!isRefresh) {
            categoryIndex--;
            controller.loadNoData();
          } else {
            controller.refreshFailed();
          }
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchShop(BuildContext context) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      state = state.copyWith(isShopLoading: true);
      final response = await _shopsRepository.getAllShops(1, type: "shop", isOpen: true);
      response.when(
        success: (data) async {
          state = state.copyWith(isShopLoading: false, shops: data.data ?? []);
        },
        failure: (activeFailure, status) {
          state = state.copyWith(isShopLoading: false);
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchShopPage(
      BuildContext context, RefreshController shopController,
      {bool isRefresh = false}) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      if (isRefresh) {
        marketIndex = 1;
      }
      final response = await _shopsRepository
          .getAllShops(isRefresh ? 1 : ++marketIndex, type: "shop", isOpen: true);
      response.when(
        success: (data) async {
          if (isRefresh) {
            state = state.copyWith(
              shops: data.data ?? [],
            );
            shopController.refreshCompleted();
          } else {
            if (data.data?.isNotEmpty ?? false) {
              List<ShopData> list = List.from(state.shops);
              list.addAll(data.data!);
              state = state.copyWith(
                shops: list,
              );
              shopController.loadComplete();
            } else {
              marketIndex--;

              shopController.loadNoData();
            }
          }
        },
        failure: (activeFailure, status) {
          if (!isRefresh) {
            marketIndex--;
            shopController.loadFailed();
          } else {
            shopController.refreshFailed();
          }
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchRestaurant(BuildContext context) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      state = state.copyWith(isRestaurantLoading: true);
      final response =
          await _shopsRepository.getAllShops(1, type: "restaurant", isOpen: true);
      response.when(
        success: (data) async {
          state = state.copyWith(
              isRestaurantLoading: false, restaurant: data.data ?? []);
        },
        failure: (activeFailure, status) {
          state = state.copyWith(isRestaurantLoading: false);
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchRestaurantPage(
      BuildContext context, RefreshController shopController,
      {bool isRefresh = false}) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      if (isRefresh) {
        shopIndex = 1;
      }
      final response = await _shopsRepository
          .getAllShops(isRefresh ? 1 : ++shopIndex, type: "restaurant", isOpen: true);
      response.when(
        success: (data) async {
          if (isRefresh) {
            state = state.copyWith(
              restaurant: data.data ?? [],
            );
            shopController.refreshCompleted();
          } else {
            if (data.data?.isNotEmpty ?? false) {
              List<ShopData> list = List.from(state.restaurant);
              list.addAll(data.data!);
              state = state.copyWith(
                restaurant: list,
              );
              shopController.loadComplete();
            } else {
              shopIndex--;

              shopController.loadNoData();
            }
          }
        },
        failure: (activeFailure, status) {
          if (!isRefresh) {
            shopIndex--;
            shopController.loadFailed();
          } else {
            shopController.refreshFailed();
          }
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }


  Future<void> fetchRestaurantNew(BuildContext context) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      state = state.copyWith(isRestaurantNewLoading: true);
      final response =
      await _shopsRepository.getAllShops(1, type: "restaurant",filterModel: FilterModel(sort: "new"), isOpen: true);
      response.when(
        success: (data) async {
          state = state.copyWith(
              isRestaurantNewLoading: false, newRestaurant: data.data ?? []);
        },
        failure: (activeFailure, status) {
          state = state.copyWith(isRestaurantNewLoading: false);
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchRestaurantPageNew(
      BuildContext context, RefreshController shopController,
      {bool isRefresh = false}) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      if (isRefresh) {
        newShopIndex = 1;
      }
      final response = await _shopsRepository
          .getAllShops(isRefresh ? 1 : ++newShopIndex, type: "restaurant",filterModel: FilterModel(sort: "new"), isOpen: true);
      response.when(
        success: (data) async {
          if (isRefresh) {
            state = state.copyWith(
              newRestaurant: data.data ?? [],
            );
            shopController.refreshCompleted();
          } else {
            if (data.data?.isNotEmpty ?? false) {
              List<ShopData> list = List.from(state.newRestaurant);
              list.addAll(data.data!);
              state = state.copyWith(
                newRestaurant: list,
              );
              shopController.loadComplete();
            } else {
              newShopIndex--;
              shopController.loadNoData();
            }
          }
        },
        failure: (activeFailure, status) {
          if (!isRefresh) {
            newShopIndex--;
            shopController.loadFailed();
          } else {
            shopController.refreshFailed();
          }
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchShopRecommend(BuildContext context) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      state = state.copyWith(isShopRecommendLoading: true);
      final response = await _shopsRepository.getShopsRecommend(1);
      response.when(
        success: (data) async {
          state = state.copyWith(
              isShopRecommendLoading: false, shopsRecommend: data.data ?? []);
        },
        failure: (activeFailure, status) {
          state = state.copyWith(isShopRecommendLoading: false);
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchStorePage(
      BuildContext context, RefreshController shopController,
      {bool isRefresh = false}) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      if (isRefresh) {
        storyIndex = 1;
      }
      final response =
          await _shopsRepository.getStory(isRefresh ? 1 : ++storyIndex);
      response.when(
        success: (data) async {
          if (isRefresh) {
            state = state.copyWith(
              story: data ?? [],
            );
            shopController.refreshCompleted();
          } else {
            if (data?.isNotEmpty ?? false) {
              List<List<StoryModel?>?>? list = state.story;
              list!.addAll(data!);
              state = state.copyWith(
                story: list,
              );
              shopController.loadComplete();
            } else {
              storyIndex--;

              shopController.loadNoData();
            }
          }
        },
        failure: (activeFailure, status) {
          if (!isRefresh) {
            storyIndex--;
            shopController.loadFailed();
          } else {
            shopController.refreshFailed();
          }
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchStore(BuildContext context) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      state = state.copyWith(isStoryLoading: true);
      final response = await _shopsRepository.getStory(1);
      response.when(
        success: (data) async {
          state = state.copyWith(isStoryLoading: false, story: data ?? []);
        },
        failure: (activeFailure, status) {
          state = state.copyWith(isStoryLoading: false);
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchShopPageRecommend(
      BuildContext context, RefreshController shopController,
      {bool isRefresh = false}) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      if (isRefresh) {
        shopIndex = 1;
      }
      final response =
          await _shopsRepository.getShopsRecommend(isRefresh ? 1 : ++shopIndex);
      response.when(
        success: (data) async {
          if (isRefresh) {
            state = state.copyWith(
              shopsRecommend: data.data ?? [],
            );
            shopController.refreshCompleted();
          } else {
            if (data.data?.isNotEmpty ?? false) {
              List<ShopData> list = List.from(state.shopsRecommend);
              list.addAll(data.data!);
              state = state.copyWith(
                shopsRecommend: list,
              );
              shopController.loadComplete();
            } else {
              shopIndex--;

              shopController.loadNoData();
            }
          }
        },
        failure: (activeFailure, status) {
          if (!isRefresh) {
            shopIndex--;
            shopController.loadFailed();
          } else {
            shopController.refreshFailed();
          }
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchBanner(BuildContext context) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      state = state.copyWith(isBannerLoading: true);
      final response = await _bannersRepository.getBannersPaginate(page: 1);
      response.when(
        success: (data) async {
          state =
              state.copyWith(isBannerLoading: false, banners: data.data ?? []);
        },
        failure: (activeFailure, status) {
          state = state.copyWith(isBannerLoading: false);
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchBannerPage(
      BuildContext context, RefreshController controller,
      {bool isRefresh = false}) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      if (isRefresh) {
        bannerIndex = 1;
      }
      final response = await _bannersRepository.getBannersPaginate(
          page: isRefresh ? 1 : ++bannerIndex);
      response.when(
        success: (data) async {
          if (isRefresh) {
            state = state.copyWith(
              banners: data.data ?? [],
            );
            controller.refreshCompleted();
          } else {
            if (data.data?.isNotEmpty ?? false) {
              List<BannerData> list = List.from(state.banners);
              list.addAll(data.data!);
              state = state.copyWith(
                banners: list,
              );
              controller.loadComplete();
            } else {
              bannerIndex--;
              controller.loadNoData();
            }
          }
        },
        failure: (activeFailure, status) {
          if (!isRefresh) {
            bannerIndex--;
            controller.loadFailed();
          } else {
            controller.refreshFailed();
          }

          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchFilterRestaurant(BuildContext context) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      state = state.copyWith(isSelectCategoryLoading: -1);
      final response = await _shopsRepository.getShopFilter(
          categoryId: state.categories[state.selectIndexCategory].id,
          page: 1,
          type: "restaurant");
      response.when(
        success: (data) async {
          state = state.copyWith(
              isSelectCategoryLoading: 1, filterShops: data.data ?? []);
        },
        failure: (activeFailure, status) {
          state = state.copyWith(isSelectCategoryLoading: 1);
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchFilterRestaurantPage(
      BuildContext context, RefreshController shopController,
      {bool isRefresh = false}) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      if (isRefresh) {
        shopRefreshIndex = 1;
      }
      final response = await _shopsRepository.getShopFilter(
          categoryId: state.categories[state.selectIndexCategory].id,
          page: isRefresh ? 1 : ++shopRefreshIndex,
          type: "restaurant");
      response.when(
        success: (data) async {
          if (isRefresh) {
            state = state.copyWith(
              filterShops: data.data ?? [],
            );
            shopController.refreshCompleted();
          } else {
            if (data.data?.isNotEmpty ?? false) {
              List<ShopData> list = List.from(state.filterShops);
              list.addAll(data.data!);
              state = state.copyWith(
                filterShops: list,
              );
              shopController.loadComplete();
            } else {
              shopRefreshIndex--;
              shopController.loadNoData();
            }
          }
        },
        failure: (activeFailure, status) {
          if (!isRefresh) {
            shopRefreshIndex--;
            shopController.loadFailed();
          } else {
            shopController.refreshFailed();
          }
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchFilterShop(BuildContext context) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      state = state.copyWith(isShopLoading: true);
      final response = await _shopsRepository.getShopFilter(
          categoryId: state.categories[state.selectIndexCategory].id,
          page: 1,
          type: "shop");
      response.when(
        success: (data) async {
          state = state.copyWith(
              isShopLoading: false, filterMarket: data.data ?? []);
        },
        failure: (activeFailure, status) {
          state = state.copyWith(isShopLoading: false);
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> fetchFilterShopPage(
      BuildContext context, RefreshController shopController,
      {bool isRefresh = false}) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      if (isRefresh) {
        marketRefreshIndex = 1;
      }
      final response = await _shopsRepository.getShopFilter(
          categoryId: state.categories[state.selectIndexCategory].id,
          page: isRefresh ? 1 : ++marketRefreshIndex,
          type: "shop");
      response.when(
        success: (data) async {
          if (isRefresh) {
            state = state.copyWith(
              filterMarket: data.data ?? [],
            );
            shopController.refreshCompleted();
          } else {
            if (data.data?.isNotEmpty ?? false) {
              List<ShopData> list = List.from(state.filterMarket);
              list.addAll(data.data!);
              state = state.copyWith(
                filterMarket: list,
              );
              shopController.loadComplete();
            } else {
              marketRefreshIndex--;
              shopController.loadNoData();
            }
          }
        },
        failure: (activeFailure, status) {
          if (!isRefresh) {
            marketRefreshIndex--;
            shopController.loadFailed();
          } else {
            shopController.refreshFailed();
          }
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }
}
