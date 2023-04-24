
import 'package:freezed_annotation/freezed_annotation.dart';
import 'package:riverpodtemp/infrastructure/models/models.dart';
part 'like_state.freezed.dart';

@freezed
class LikeState with _$LikeState {

  const factory LikeState({
    @Default(true) bool isShopLoading,
    @Default([]) List<ShopData> shops,

  }) = _LikeState;

  const LikeState._();
}