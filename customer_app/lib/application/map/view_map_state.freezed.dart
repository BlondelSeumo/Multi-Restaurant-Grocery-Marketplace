// coverage:ignore-file
// GENERATED CODE - DO NOT MODIFY BY HAND
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'view_map_state.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

T _$identity<T>(T value) => value;

final _privateConstructorUsedError = UnsupportedError(
    'It seems like you constructed your class using `MyClass._()`. This constructor is only meant to be used by freezed and you are not supposed to need it nor use it.\nPlease check the documentation here for more information: https://github.com/rrousselGit/freezed#custom-getters-and-methods');

/// @nodoc
mixin _$ViewMapState {
  bool get isLoading => throw _privateConstructorUsedError;
  bool get isActive => throw _privateConstructorUsedError;
  AddressData? get place => throw _privateConstructorUsedError;
  bool get isSetAddress => throw _privateConstructorUsedError;

  @JsonKey(ignore: true)
  $ViewMapStateCopyWith<ViewMapState> get copyWith =>
      throw _privateConstructorUsedError;
}

/// @nodoc
abstract class $ViewMapStateCopyWith<$Res> {
  factory $ViewMapStateCopyWith(
          ViewMapState value, $Res Function(ViewMapState) then) =
      _$ViewMapStateCopyWithImpl<$Res, ViewMapState>;
  @useResult
  $Res call(
      {bool isLoading, bool isActive, AddressData? place, bool isSetAddress});
}

/// @nodoc
class _$ViewMapStateCopyWithImpl<$Res, $Val extends ViewMapState>
    implements $ViewMapStateCopyWith<$Res> {
  _$ViewMapStateCopyWithImpl(this._value, this._then);

  // ignore: unused_field
  final $Val _value;
  // ignore: unused_field
  final $Res Function($Val) _then;

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? isLoading = null,
    Object? isActive = null,
    Object? place = freezed,
    Object? isSetAddress = null,
  }) {
    return _then(_value.copyWith(
      isLoading: null == isLoading
          ? _value.isLoading
          : isLoading // ignore: cast_nullable_to_non_nullable
              as bool,
      isActive: null == isActive
          ? _value.isActive
          : isActive // ignore: cast_nullable_to_non_nullable
              as bool,
      place: freezed == place
          ? _value.place
          : place // ignore: cast_nullable_to_non_nullable
              as AddressData?,
      isSetAddress: null == isSetAddress
          ? _value.isSetAddress
          : isSetAddress // ignore: cast_nullable_to_non_nullable
              as bool,
    ) as $Val);
  }
}

/// @nodoc
abstract class _$$_ViewMapStateCopyWith<$Res>
    implements $ViewMapStateCopyWith<$Res> {
  factory _$$_ViewMapStateCopyWith(
          _$_ViewMapState value, $Res Function(_$_ViewMapState) then) =
      __$$_ViewMapStateCopyWithImpl<$Res>;
  @override
  @useResult
  $Res call(
      {bool isLoading, bool isActive, AddressData? place, bool isSetAddress});
}

/// @nodoc
class __$$_ViewMapStateCopyWithImpl<$Res>
    extends _$ViewMapStateCopyWithImpl<$Res, _$_ViewMapState>
    implements _$$_ViewMapStateCopyWith<$Res> {
  __$$_ViewMapStateCopyWithImpl(
      _$_ViewMapState _value, $Res Function(_$_ViewMapState) _then)
      : super(_value, _then);

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? isLoading = null,
    Object? isActive = null,
    Object? place = freezed,
    Object? isSetAddress = null,
  }) {
    return _then(_$_ViewMapState(
      isLoading: null == isLoading
          ? _value.isLoading
          : isLoading // ignore: cast_nullable_to_non_nullable
              as bool,
      isActive: null == isActive
          ? _value.isActive
          : isActive // ignore: cast_nullable_to_non_nullable
              as bool,
      place: freezed == place
          ? _value.place
          : place // ignore: cast_nullable_to_non_nullable
              as AddressData?,
      isSetAddress: null == isSetAddress
          ? _value.isSetAddress
          : isSetAddress // ignore: cast_nullable_to_non_nullable
              as bool,
    ));
  }
}

/// @nodoc

class _$_ViewMapState extends _ViewMapState {
  const _$_ViewMapState(
      {this.isLoading = false,
      this.isActive = false,
      this.place = null,
      this.isSetAddress = false})
      : super._();

  @override
  @JsonKey()
  final bool isLoading;
  @override
  @JsonKey()
  final bool isActive;
  @override
  @JsonKey()
  final AddressData? place;
  @override
  @JsonKey()
  final bool isSetAddress;

  @override
  String toString() {
    return 'ViewMapState(isLoading: $isLoading, isActive: $isActive, place: $place, isSetAddress: $isSetAddress)';
  }

  @override
  bool operator ==(dynamic other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _$_ViewMapState &&
            (identical(other.isLoading, isLoading) ||
                other.isLoading == isLoading) &&
            (identical(other.isActive, isActive) ||
                other.isActive == isActive) &&
            (identical(other.place, place) || other.place == place) &&
            (identical(other.isSetAddress, isSetAddress) ||
                other.isSetAddress == isSetAddress));
  }

  @override
  int get hashCode =>
      Object.hash(runtimeType, isLoading, isActive, place, isSetAddress);

  @JsonKey(ignore: true)
  @override
  @pragma('vm:prefer-inline')
  _$$_ViewMapStateCopyWith<_$_ViewMapState> get copyWith =>
      __$$_ViewMapStateCopyWithImpl<_$_ViewMapState>(this, _$identity);
}

abstract class _ViewMapState extends ViewMapState {
  const factory _ViewMapState(
      {final bool isLoading,
      final bool isActive,
      final AddressData? place,
      final bool isSetAddress}) = _$_ViewMapState;
  const _ViewMapState._() : super._();

  @override
  bool get isLoading;
  @override
  bool get isActive;
  @override
  AddressData? get place;
  @override
  bool get isSetAddress;
  @override
  @JsonKey(ignore: true)
  _$$_ViewMapStateCopyWith<_$_ViewMapState> get copyWith =>
      throw _privateConstructorUsedError;
}
