// coverage:ignore-file
// GENERATED CODE - DO NOT MODIFY BY HAND
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'promo_code_state.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

T _$identity<T>(T value) => value;

final _privateConstructorUsedError = UnsupportedError(
    'It seems like you constructed your class using `MyClass._()`. This constructor is only meant to be used by freezed and you are not supposed to need it nor use it.\nPlease check the documentation here for more information: https://github.com/rrousselGit/freezed#custom-getters-and-methods');

/// @nodoc
mixin _$PromoCodeState {
  bool get isActive => throw _privateConstructorUsedError;
  bool get isLoading => throw _privateConstructorUsedError;

  @JsonKey(ignore: true)
  $PromoCodeStateCopyWith<PromoCodeState> get copyWith =>
      throw _privateConstructorUsedError;
}

/// @nodoc
abstract class $PromoCodeStateCopyWith<$Res> {
  factory $PromoCodeStateCopyWith(
          PromoCodeState value, $Res Function(PromoCodeState) then) =
      _$PromoCodeStateCopyWithImpl<$Res, PromoCodeState>;
  @useResult
  $Res call({bool isActive, bool isLoading});
}

/// @nodoc
class _$PromoCodeStateCopyWithImpl<$Res, $Val extends PromoCodeState>
    implements $PromoCodeStateCopyWith<$Res> {
  _$PromoCodeStateCopyWithImpl(this._value, this._then);

  // ignore: unused_field
  final $Val _value;
  // ignore: unused_field
  final $Res Function($Val) _then;

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? isActive = null,
    Object? isLoading = null,
  }) {
    return _then(_value.copyWith(
      isActive: null == isActive
          ? _value.isActive
          : isActive // ignore: cast_nullable_to_non_nullable
              as bool,
      isLoading: null == isLoading
          ? _value.isLoading
          : isLoading // ignore: cast_nullable_to_non_nullable
              as bool,
    ) as $Val);
  }
}

/// @nodoc
abstract class _$$_PromoCodeStateCopyWith<$Res>
    implements $PromoCodeStateCopyWith<$Res> {
  factory _$$_PromoCodeStateCopyWith(
          _$_PromoCodeState value, $Res Function(_$_PromoCodeState) then) =
      __$$_PromoCodeStateCopyWithImpl<$Res>;
  @override
  @useResult
  $Res call({bool isActive, bool isLoading});
}

/// @nodoc
class __$$_PromoCodeStateCopyWithImpl<$Res>
    extends _$PromoCodeStateCopyWithImpl<$Res, _$_PromoCodeState>
    implements _$$_PromoCodeStateCopyWith<$Res> {
  __$$_PromoCodeStateCopyWithImpl(
      _$_PromoCodeState _value, $Res Function(_$_PromoCodeState) _then)
      : super(_value, _then);

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? isActive = null,
    Object? isLoading = null,
  }) {
    return _then(_$_PromoCodeState(
      isActive: null == isActive
          ? _value.isActive
          : isActive // ignore: cast_nullable_to_non_nullable
              as bool,
      isLoading: null == isLoading
          ? _value.isLoading
          : isLoading // ignore: cast_nullable_to_non_nullable
              as bool,
    ));
  }
}

/// @nodoc

class _$_PromoCodeState extends _PromoCodeState {
  const _$_PromoCodeState({this.isActive = false, this.isLoading = false})
      : super._();

  @override
  @JsonKey()
  final bool isActive;
  @override
  @JsonKey()
  final bool isLoading;

  @override
  String toString() {
    return 'PromoCodeState(isActive: $isActive, isLoading: $isLoading)';
  }

  @override
  bool operator ==(dynamic other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _$_PromoCodeState &&
            (identical(other.isActive, isActive) ||
                other.isActive == isActive) &&
            (identical(other.isLoading, isLoading) ||
                other.isLoading == isLoading));
  }

  @override
  int get hashCode => Object.hash(runtimeType, isActive, isLoading);

  @JsonKey(ignore: true)
  @override
  @pragma('vm:prefer-inline')
  _$$_PromoCodeStateCopyWith<_$_PromoCodeState> get copyWith =>
      __$$_PromoCodeStateCopyWithImpl<_$_PromoCodeState>(this, _$identity);
}

abstract class _PromoCodeState extends PromoCodeState {
  const factory _PromoCodeState({final bool isActive, final bool isLoading}) =
      _$_PromoCodeState;
  const _PromoCodeState._() : super._();

  @override
  bool get isActive;
  @override
  bool get isLoading;
  @override
  @JsonKey(ignore: true)
  _$$_PromoCodeStateCopyWith<_$_PromoCodeState> get copyWith =>
      throw _privateConstructorUsedError;
}
