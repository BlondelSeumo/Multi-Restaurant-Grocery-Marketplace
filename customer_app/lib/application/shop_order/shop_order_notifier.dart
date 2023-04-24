import 'dart:convert';

import 'package:auto_route/auto_route.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpodtemp/domain/iterface/cart.dart';
import 'package:riverpodtemp/infrastructure/models/data/addons_data.dart';
import 'package:riverpodtemp/infrastructure/models/data/cart_data.dart';
import 'package:riverpodtemp/infrastructure/models/request/cart_request.dart';
import 'package:riverpodtemp/infrastructure/services/app_constants.dart';
import 'package:riverpodtemp/infrastructure/services/local_storage.dart';
import 'package:riverpodtemp/infrastructure/services/tr_keys.dart';
import '../../infrastructure/services/app_connectivity.dart';
import '../../infrastructure/services/app_helpers.dart';
import '../../infrastructure/services/tpying_delay.dart';
import '../../presentation/routes/app_router.gr.dart';
import 'shop_order_state.dart';
import 'package:http/http.dart' as http;

class ShopOrderNotifier extends StateNotifier<ShopOrderState> {
  final CartRepositoryFacade _cartRepository;

  ShopOrderNotifier(this._cartRepository) : super(const ShopOrderState());
  final _delayed = Delayed(milliseconds: 700);

  Future<void> addCount(BuildContext context, int index) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      CartDetail oldDetail =
          state.cart?.userCarts?.first.cartDetails?[index] ?? CartDetail();
      CartDetail newDetail =
          oldDetail.copyWith(quantity: 1 + (oldDetail.quantity ?? 1));
      List<CartDetail> newCartList =
          state.cart?.userCarts?.first.cartDetails ?? [];
      newCartList.removeAt(index);
      newCartList.insert(index, newDetail);
      UserCart newCart =
          state.cart!.userCarts!.first.copyWith(cartDetails: newCartList);
      List<UserCart> newUserCart = state.cart?.userCarts ?? [];
      newUserCart.removeAt(0);
      newUserCart.insert(0, newCart);
      Cart newDate = state.cart!.copyWith(userCarts: newUserCart);
      state = state.copyWith(cart: newDate);
      _delayed.run(() async {
        state = state.copyWith(
          isAddAndRemoveLoading: true,
        );
        List<CartRequest> list = [
          CartRequest(
              stockId:
                  state.cart?.userCarts?.first.cartDetails?[index].stock?.id ??
                      0,
              quantity:
                  state.cart?.userCarts?.first.cartDetails?[index].quantity ??
                      1)
        ];
        for (Addons element
            in state.cart?.userCarts?.first.cartDetails?[index].addons ?? []) {
          list.add(CartRequest(
            stockId: element.stocks?.id,
            quantity: element.quantity,
            parentId:
                state.cart?.userCarts?.first.cartDetails?[index].stock?.id ?? 0,
          ));
        }
        final response = await _cartRepository.insertCart(
          cart: CartRequest(
              shopId: state.cart?.shopId ?? 0,
              stockId:
                  state.cart?.userCarts?.first.cartDetails?[index].stock?.id ??
                      0,
              quantity:
                  state.cart?.userCarts?.first.cartDetails?[index].quantity ??
                      1,
              carts: list),
        );
        response.when(
          success: (data) async {
            state =
                state.copyWith(cart: data.data, isAddAndRemoveLoading: false);
          },
          failure: (activeFailure, status) {
            state = state.copyWith(isAddAndRemoveLoading: false);
            AppHelpers.showCheckTopSnackBar(
              context,
              AppHelpers.getTranslation(status.toString()),
            );
          },
        );
      });
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> removeCount(BuildContext context, int index) async {
    if ((state.cart?.userCarts?.first.cartDetails?[index].quantity ?? 1) > 1) {
      final connected = await AppConnectivity.connectivity();
      if (connected) {
        CartDetail oldDetail =
            state.cart?.userCarts?.first.cartDetails?[index] ?? CartDetail();
        CartDetail newDetail =
            oldDetail.copyWith(quantity: (oldDetail.quantity ?? 1) - 1);
        List<CartDetail> newCartList =
            state.cart?.userCarts?.first.cartDetails ?? [];
        newCartList.removeAt(index);
        newCartList.insert(index, newDetail);
        UserCart newCart =
            state.cart!.userCarts!.first.copyWith(cartDetails: newCartList);
        List<UserCart> newUserCart = state.cart?.userCarts ?? [];
        newUserCart.removeAt(0);
        newUserCart.insert(0, newCart);
        Cart newDate = state.cart!.copyWith(userCarts: newUserCart);
        state = state.copyWith(cart: newDate);
        _delayed.run(() async {
          state = state.copyWith(isAddAndRemoveLoading: true);
          List<CartRequest> list = [
            CartRequest(
                stockId: state
                        .cart?.userCarts?.first.cartDetails?[index].stock?.id ??
                    0,
                quantity:
                    state.cart?.userCarts?.first.cartDetails?[index].quantity ??
                        1)
          ];
          for (Addons element
              in state.cart?.userCarts?.first.cartDetails?[index].addons ??
                  []) {
            list.add(CartRequest(
              stockId: element.stocks?.id,
              quantity: element.quantity,
              parentId:
                  state.cart?.userCarts?.first.cartDetails?[index].stock?.id ??
                      0,
            ));
          }
          final response = await _cartRepository.insertCart(
            cart: CartRequest(
                shopId: state.cart?.shopId ?? 0,
                stockId: state
                        .cart?.userCarts?.first.cartDetails?[index].stock?.id ??
                    0,
                quantity:
                    state.cart?.userCarts?.first.cartDetails?[index].quantity ??
                        1,
                carts: list),
          );
          response.when(
            success: (data) async {
              state =
                  state.copyWith(cart: data.data, isAddAndRemoveLoading: false);
              getCart(
                context,
                () {},
                isShowLoading: false,
              );
            },
            failure: (activeFailure, status) {
              state = state.copyWith(isAddAndRemoveLoading: false);
              AppHelpers.showCheckTopSnackBar(
                context,
                AppHelpers.getTranslation(status.toString()),
              );
            },
          );
        });
      } else {
        if (mounted) {
          AppHelpers.showNoConnectionSnackBar(context);
        }
      }
    } else {
      final connected = await AppConnectivity.connectivity();
      if (connected) {
        state = state.copyWith(
          isAddAndRemoveLoading: true,
        );
        final cartId = state.cart?.id ?? 0;
        final cartDetailId =
            state.cart?.userCarts?.first.cartDetails?[index].id ?? 0;
        List<CartDetail> newCartList =
            state.cart?.userCarts?.first.cartDetails ?? [];
        newCartList.removeAt(index);
        UserCart newCart =
            state.cart!.userCarts!.first.copyWith(cartDetails: newCartList);
        List<UserCart> newUserCart = state.cart?.userCarts ?? [];
        newUserCart.removeAt(0);
        newUserCart.insert(0, newCart);
        Cart newDate = state.cart!.copyWith(userCarts: newUserCart);
        if (newDate.userCarts!.first.cartDetails!.isEmpty) {
          state = state.copyWith(cart: null);
          final responseDelete =
              await _cartRepository.deleteCart(cartId: cartId);
          responseDelete.when(
            success: (data) async {
              state = state.copyWith(isAddAndRemoveLoading: false);
              context.popRoute();
              getCart(context, () {}, isShowLoading: false);
            },
            failure: (activeFailure, status) {
              state = state.copyWith(isAddAndRemoveLoading: false);
              AppHelpers.showCheckTopSnackBar(
                context,
                AppHelpers.getTranslation(status.toString()),
              );
            },
          );
        } else {
          state = state.copyWith(cart: newDate);
          final response = await _cartRepository.removeProductCart(
              cartDetailId: cartDetailId);
          response.when(
            success: (data) async {
              state = state.copyWith(isAddAndRemoveLoading: false);
              getCart(context, () {}, isShowLoading: false);
            },
            failure: (activeFailure, status) {
              state = state.copyWith(isAddAndRemoveLoading: false);
              AppHelpers.showCheckTopSnackBar(
                context,
                AppHelpers.getTranslation(status.toString()),
              );
            },
          );
        }
      } else {
        if (mounted) {
          AppHelpers.showNoConnectionSnackBar(context);
        }
      }
    }
  }

  Future<void> addCountWithGroup(
      {required BuildContext context,
      required int productIndex,
      required int userIndex}) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      CartDetail oldDetail =
          state.cart?.userCarts?[userIndex].cartDetails?[productIndex] ??
              CartDetail();
      CartDetail newDetail =
          oldDetail.copyWith(quantity: 1 + (oldDetail.quantity ?? 1));
      List<CartDetail> newCartList =
          state.cart?.userCarts?[userIndex].cartDetails ?? [];
      newCartList.removeAt(productIndex);
      newCartList.insert(productIndex, newDetail);
      UserCart newCart =
          state.cart!.userCarts![userIndex].copyWith(cartDetails: newCartList);
      List<UserCart> newUserCart = state.cart?.userCarts ?? [];
      newUserCart.removeAt(userIndex);
      newUserCart.insert(userIndex, newCart);
      Cart newDate = state.cart!.copyWith(userCarts: newUserCart);
      state = state.copyWith(cart: newDate);
      _delayed.run(() async {
        state = state.copyWith(
          isAddAndRemoveLoading: true,
        );
        List<CartRequest> list = [
          CartRequest(
              stockId: state.cart?.userCarts?[userIndex]
                      .cartDetails?[productIndex].stock?.id ??
                  0,
              quantity: state.cart?.userCarts?[userIndex]
                      .cartDetails?[productIndex].quantity ??
                  1)
        ];
        for (Addons element in state.cart?.userCarts?[userIndex]
                .cartDetails?[productIndex].addons ??
            []) {
          list.add(CartRequest(
            stockId: element.stocks?.id,
            quantity: element.quantity,
            parentId: state.cart?.userCarts?[userIndex]
                    .cartDetails?[productIndex].stock?.id ??
                0,
          ));
        }
        final response = await _cartRepository.insertCartWithGroup(
          cart: CartRequest(
              cartId: state.cart?.id.toString(),
              userUuid: state.cart?.userCarts?[userIndex].uuid,
              shopId: state.cart?.shopId ?? 0,
              stockId: state.cart?.userCarts?[userIndex]
                      .cartDetails?[productIndex].stock?.id ??
                  0,
              quantity: state.cart?.userCarts?[userIndex]
                      .cartDetails?[productIndex].quantity ??
                  1,
              carts: list),
        );
        response.when(
          success: (data) async {
            state =
                state.copyWith(cart: data.data, isAddAndRemoveLoading: false);
          },
          failure: (activeFailure, status) {
            state = state.copyWith(isAddAndRemoveLoading: false);
            AppHelpers.showCheckTopSnackBar(
              context,
              AppHelpers.getTranslation(status.toString()),
            );
          },
        );
      });
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future<void> removeCountWithGroup({
    required BuildContext context,
    required int productIndex,
    required int userIndex,
  }) async {
    if ((state.cart?.userCarts?[userIndex].cartDetails?[productIndex]
                .quantity ??
            1) >
        1) {
      final connected = await AppConnectivity.connectivity();
      if (connected) {
        CartDetail oldDetail =
            state.cart?.userCarts?[userIndex].cartDetails?[productIndex] ??
                CartDetail();
        CartDetail newDetail =
            oldDetail.copyWith(quantity: (oldDetail.quantity ?? 1) - 1);
        List<CartDetail> newCartList =
            state.cart?.userCarts?[userIndex].cartDetails ?? [];
        newCartList.removeAt(productIndex);
        newCartList.insert(productIndex, newDetail);
        UserCart newCart = state.cart!.userCarts![userIndex]
            .copyWith(cartDetails: newCartList);
        List<UserCart> newUserCart = state.cart?.userCarts ?? [];
        newUserCart.removeAt(userIndex);
        newUserCart.insert(userIndex, newCart);
        Cart newDate = state.cart!.copyWith(userCarts: newUserCart);
        state = state.copyWith(cart: newDate);
        _delayed.run(() async {
          state = state.copyWith(isAddAndRemoveLoading: true);
          List<CartRequest> list = [
            CartRequest(
                stockId: state.cart?.userCarts?[userIndex]
                        .cartDetails?[productIndex].stock?.id ??
                    0,
                quantity: state.cart?.userCarts?[userIndex]
                        .cartDetails?[productIndex].quantity ??
                    1)
          ];
          for (Addons element in state.cart?.userCarts?[userIndex]
                  .cartDetails?[productIndex].addons ??
              []) {
            list.add(CartRequest(
              stockId: element.stocks?.id,
              quantity: element.quantity,
              parentId: state.cart?.userCarts?[userIndex]
                      .cartDetails?[productIndex].stock?.id ??
                  0,
            ));
          }
          final response = await _cartRepository.insertCartWithGroup(
            cart: CartRequest(
                cartId: state.cart?.id.toString(),
                userUuid: state.cart?.userCarts?[userIndex].uuid,
                shopId: state.cart?.shopId ?? 0,
                stockId: state.cart?.userCarts?[userIndex]
                        .cartDetails?[productIndex].stock?.id ??
                    0,
                quantity: state.cart?.userCarts?[userIndex]
                        .cartDetails?[productIndex].quantity ??
                    1,
                carts: list),
          );
          response.when(
            success: (data) async {
              state =
                  state.copyWith(cart: data.data, isAddAndRemoveLoading: false);
              getCart(context, () {}, isShowLoading: false);
            },
            failure: (activeFailure, status) {
              state = state.copyWith(isAddAndRemoveLoading: false);
              AppHelpers.showCheckTopSnackBar(
                context,
                AppHelpers.getTranslation(status.toString()),
              );
            },
          );
        });
      } else {
        if (mounted) {
          AppHelpers.showNoConnectionSnackBar(context);
        }
      }
    } else {
      final connected = await AppConnectivity.connectivity();
      if (connected) {
        state = state.copyWith(
          isAddAndRemoveLoading: true,
        );
        final cartId = state.cart?.id ?? 0;
        final cartDetailId =
            state.cart?.userCarts?[userIndex].cartDetails?[productIndex].id ??
                0;
        List<CartDetail> newCartList =
            state.cart?.userCarts?[userIndex].cartDetails ?? [];
        newCartList.removeAt(productIndex);
        UserCart newCart = state.cart!.userCarts![userIndex]
            .copyWith(cartDetails: newCartList);
        List<UserCart> newUserCart = state.cart?.userCarts ?? [];
        newUserCart.removeAt(userIndex);
        newUserCart.insert(userIndex, newCart);
        Cart newDate = state.cart!.copyWith(userCarts: newUserCart);
        if (newDate.userCarts![userIndex].cartDetails!.isEmpty) {
          state = state.copyWith(cart: null);
          final responseDelete =
              await _cartRepository.deleteCart(cartId: cartId);
          responseDelete.when(
            success: (data) async {
              state = state.copyWith(isAddAndRemoveLoading: false);
              context.popRoute();
              getCart(context, () {}, isShowLoading: false);
            },
            failure: (activeFailure, status) {
              state = state.copyWith(isAddAndRemoveLoading: false);
              AppHelpers.showCheckTopSnackBar(
                context,
                AppHelpers.getTranslation(status.toString()),
              );
            },
          );
        } else {
          state = state.copyWith(cart: newDate);
          final response = await _cartRepository.removeProductCart(
              cartDetailId: cartDetailId);
          response.when(
            success: (data) async {
              state = state.copyWith(isAddAndRemoveLoading: false);
              getCart(context, () {}, isShowLoading: false);
            },
            failure: (activeFailure, status) {
              state = state.copyWith(isAddAndRemoveLoading: false);
              AppHelpers.showCheckTopSnackBar(
                context,
                AppHelpers.getTranslation(status.toString()),
              );
            },
          );
        }
      } else {
        if (mounted) {
          AppHelpers.showNoConnectionSnackBar(context);
        }
      }
    }
  }

  Future getCart(BuildContext context, VoidCallback onSuccess,
      {bool isShowLoading = true,
      String? shopId,
      String? cartId,
      String? userUuid}) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      if (isShowLoading) {
        state = state.copyWith(isLoading: true);
      }

      final response = (userUuid == null || userUuid.isEmpty)
          ? await _cartRepository.getCart()
          : await _cartRepository.getCartInGroup(cartId, shopId, userUuid);

      response.when(
        success: (data) async {
          if (isShowLoading) {
            state = state.copyWith(cart: data.data, isLoading: false);
            onSuccess();
          } else {
            state = state.copyWith(
              cart: data.data,
            );
          }
        },
        failure: (activeFailure, status) {
          if (status == 404) {
            if (isShowLoading) {
              state = state.copyWith(isLoading: false, cart: null);
            } else {
              state = state.copyWith(cart: null);
            }
          } else if (status == 400 || status == 404) {
            AppHelpers.showCheckTopSnackBarDone(
                context, AppHelpers.getTranslation(TrKeys.thankYouForOrder));
            state = state.copyWith(cart: null, isStartGroup: false);
            Navigator.pop(context);
          } else if (status != 401) {
            if (isShowLoading) {
              state = state.copyWith(isLoading: false);
            }
            AppHelpers.showCheckTopSnackBar(
              context,
              AppHelpers.getTranslation(status.toString()),
            );
          } else {
            if (isShowLoading) {
              state = state.copyWith(isLoading: false);
            }
            LocalStorage.instance.logout();
            context.router.popUntilRoot();
            context.replaceRoute(const LoginRoute());
          }
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  Future changeStatus(BuildContext context, String? userUuid) async {
    final connected = await AppConnectivity.connectivity();
    state = state.copyWith(isEditOrder: !state.isEditOrder);
    if (connected) {
      final response = await _cartRepository.changeStatus(
          userUuid: userUuid, cartId: state.cart?.id.toString());
      response.when(
        success: (data) async {},
        failure: (activeFailure, status) {},
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
        return;
      }
    }
  }

  Future deleteCart(BuildContext context) async {
    final connected = await AppConnectivity.connectivity();
    state = state.copyWith(isDeleteLoading: true);
    if (connected) {
      final response =
          await _cartRepository.deleteCart(cartId: state.cart?.id ?? 0);
      response.when(
        success: (data) async {
          state = state.copyWith(isDeleteLoading: false, cart: null);
          Navigator.pop(context);
          return;
        },
        failure: (activeFailure, status) {
          state = state.copyWith(
            isDeleteLoading: false,
          );
          AppHelpers.showCheckTopSnackBar(
            context,
            AppHelpers.getTranslation(status.toString()),
          );
          return;
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
        return;
      }
    }
  }

  Future<void> deleteUser(BuildContext context, int index,
      {String? userId}) async {
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      if (userId == null) {
        _cartRepository.deleteUser(
            cartId: state.cart?.id ?? 0,
            userId: state.cart?.userCarts?[index].uuid ?? "");
        Cart? cart = state.cart;
        List<UserCart>? list = cart?.userCarts;
        list?.removeAt(index);
        Cart? newCart = cart?.copyWith(userCarts: list);
        state = state.copyWith(cart: newCart);
      } else {
        // ignore: use_build_context_synchronously
        context.popRoute();
        _cartRepository.deleteUser(cartId: state.cart?.id ?? 0, userId: userId);
        state = state.copyWith(
          isStartGroup: false,
          cart: null,
        );
      }
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
        return;
      }
    }
  }

  void joinGroupOrder(BuildContext context) async {
    state = state.copyWith(
      isStartGroup: false,
    );
    state = state.copyWith(
      isStartGroup: true,
    );
  }

  Future<void> startGroupOrder(
    BuildContext context,
    int cartId,
  ) async {
    final connected = await AppConnectivity.connectivity();
    state = state.copyWith(
      isStartGroup: false,
      isStartGroupLoading: true,
    );
    if (connected) {
      final response = await _cartRepository.startGroupOrder(cartId: cartId);
      response.when(
        success: (data) async {
          Cart? cart = state.cart;
          Cart? newCart = cart?.copyWith(group: true);
          state = state.copyWith(
            cart: newCart,
            isStartGroup: true,
            isStartGroupLoading: false,
          );
        },
        failure: (activeFailure, status) {
          state = state.copyWith(
            isStartGroup: false,
            isStartGroupLoading: false,
          );
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

  void createCart(BuildContext context, int shopId) async {
    state = state.copyWith(isCheckShopOrder: false, isOtherShop: false);
    final connected = await AppConnectivity.connectivity();
    if (connected) {
      state = state.copyWith(isCheckShopOrder: true);
      final response = await _cartRepository.createCart(
        cart: CartRequest(shopId: shopId),
      );
      response.when(
        success: (data) {
          state = state.copyWith(isCheckShopOrder: false, cart: data.data);
          startGroupOrder(
            context,
            data.data?.id ?? 0,
          );
        },
        failure: (activeFailure, status) {
          state = state.copyWith(isCheckShopOrder: false);
          if (status == 400) {
            state = state.copyWith(isOtherShop: true);
          } else {
            AppHelpers.showCheckTopSnackBar(
              context,
              AppHelpers.getTranslation(status.toString()),
            );
          }
        },
      );
    } else {
      if (mounted) {
        AppHelpers.showNoConnectionSnackBar(context);
      }
    }
  }

  generateShareLink(String shopName, String shopLogo, String type) async {
    final productLink =
        "${AppConstants.webUrl}/group/${state.cart?.shopId}?g=${state.cart?.id}&o=${state.cart?.ownerId}&t=$type";

    const dynamicLink =
        'https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=AIzaSyDraEPokcqncELQIoXO2Phy0YZUUIaKqMI';

    final dataShare = {
      "dynamicLinkInfo": {
        "domainUriPrefix": 'https://foodyman.page.link',
        "link": productLink,
        "androidInfo": {
          "androidPackageName": 'com.foodyman',
          "androidFallbackLink":
              "${AppConstants.webUrl}/group/${state.cart?.shopId}?g=${state.cart?.id}&o=${state.cart?.ownerId}&t=$type"
        },
        "iosInfo": {
          "iosBundleId": "com.foodyman.customer",
          "iosFallbackLink":
              "${AppConstants.webUrl}/group/${state.cart?.shopId}?g=${state.cart?.id}&o=${state.cart?.ownerId}&t=$type"
        },
        "socialMetaTagInfo": {
          "socialTitle": AppHelpers.getTranslation(TrKeys.groupOrder),
          "socialDescription": shopName,
          "socialImageLink": shopLogo,
        }
      }
    };

    final res =
        await http.post(Uri.parse(dynamicLink), body: jsonEncode(dataShare));

    state = state.copyWith(shareLink: jsonDecode(res.body)['shortLink']);
  }
}
