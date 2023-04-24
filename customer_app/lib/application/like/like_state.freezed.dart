// coverage:ignore-file
// GENERATED CODE - DO NOT MODIFY BY HAND
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'like_state.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

T _$identity<T>(T value) => value;

final _privateConstructorUsedError = UnsupportedError(
    'It seems like you constructed your class using `MyClass._()`. This constructor is only meant to be used by freezed and you are not supposed to need it nor use it.\nPlease check the documentation here for more information: https://github.com/rrousselGit/freezed#custom-getters-and-methods');

/// @nodoc
mixin _$LikeState {
  bool get isShopLoading => throw _privateConstructorUsedError;
  List<ShopData> get shops => throw _privateConstructorUsedError;

  @JsonKey(ignore: true)
  $LikeStateCopyWith<LikeState> get copyWith =>
      throw _privateConstructorUsedError;
}

/// @nodoc
abstract class $LikeStateCopyWith<$Res> {
  factory $LikeStateCopyWith(LikeState value, $Res Function(LikeState) then) =
      _$LikeStateCopyWithImpl<$Res, LikeState>;
  @useResult
  $Res call({bool isShopLoading, List<ShopData> shops});
}

/// @nodoc
class _$LikeStateCopyWithImpl<$Res, $Val extends LikeState>
    implements $LikeStateCopyWith<$Res> {
  _$LikeStateCopyWithImpl(this._value, this._then);

  // ignore: unused_field
  final $Val _value;
  // ignore: unused_field
  final $Res Function($Val) _then;

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? isShopLoading = null,
    Object? shops = null,
  }) {
    return _then(_value.copyWith(
      isShopLoading: null == isShopLoading
          ? _value.isShopLoading
          : isShopLoading // ignore: cast_nullable_to_non_nullable
              as bool,
      shops: null == shops
          ? _value.shops
          : shops // ignore: cast_nullable_to_non_nullable
              as List<ShopData>,
    ) as $Val);
  }
}

/// @nodoc
abstract class _$$_LikeStateCopyWith<$Res> implements $LikeStateCopyWith<$Res> {
  factory _$$_LikeStateCopyWith(
          _$_LikeState value, $Res Function(_$_LikeState) then) =
      __$$_LikeStateCopyWithImpl<$Res>;
  @override
  @useResult
  $Res call({bool isShopLoading, List<ShopData> shops});
}

/// @nodoc
class __$$_LikeStateCopyWithImpl<$Res>
    extends _$LikeStateCopyWithImpl<$Res, _$_LikeState>
    implements _$$_LikeStateCopyWith<$Res> {
  __$$_LikeStateCopyWithImpl(
      _$_LikeState _value, $Res Function(_$_LikeState) _then)
      : super(_value, _then);

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? isShopLoading = null,
    Object? shops = null,
  }) {
    return _then(_$_LikeState(
      isShopLoading: null == isShopLoading
          ? _value.isShopLoading
          : isShopLoading // ignore: cast_nullable_to_non_nullable
              as bool,
      shops: null == shops
          ? _value._shops
          : shops // ignore: cast_nullable_to_non_nullable
              as List<ShopData>,
    ));
  }
}

/// @nodoc

class _$_LikeState extends _LikeState {
  const _$_LikeState(
      {this.isShopLoading = true, final List<ShopData> shops = const []})
      : _shops = shops,
        super._();

  @override
  @JsonKey()
  final bool isShopLoading;
  final List<ShopData> _shops;
  @override
  @JsonKey()
  List<ShopData> get shops {
    if (_shops is EqualUnmodifiableListView) return _shops;
    // ignore: implicit_dynamic_type
    return EqualUnmodifiableListView(_shops);
  }

  @override
  String toString() {
    return 'LikeState(isShopLoading: $isShopLoading, shops: $shops)';
  }

  @override
  bool operator ==(dynamic other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _$_LikeState &&
            (identical(other.isShopLoading, isShopLoading) ||
                other.isShopLoading == isShopLoading) &&
            const DeepCollectionEquality().equals(other._shops, _shops));
  }

  @override
  int get hashCode => Object.hash(
      runtimeType, isShopLoading, const DeepCollectionEquality().hash(_shops));

  @JsonKey(ignore: true)
  @override
  @pragma('vm:prefer-inline')
  _$$_LikeStateCopyWith<_$_LikeState> get copyWith =>
      __$$_LikeStateCopyWithImpl<_$_LikeState>(this, _$identity);
}

abstract class _LikeState extends LikeState {
  const factory _LikeState(
      {final bool isShopLoading, final List<ShopData> shops}) = _$_LikeState;
  const _LikeState._() : super._();

  @override
  bool get isShopLoading;
  @override
  List<ShopData> get shops;
  @override
  @JsonKey(ignore: true)
  _$$_LikeStateCopyWith<_$_LikeState> get copyWith =>
      throw _privateConstructorUsedError;
}
