// coverage:ignore-file
// GENERATED CODE - DO NOT MODIFY BY HAND
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'generate_image_state.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

T _$identity<T>(T value) => value;

final _privateConstructorUsedError = UnsupportedError(
    'It seems like you constructed your class using `MyClass._()`. This constructor is only meant to be used by freezed and you are not supposed to need it nor use it.\nPlease check the documentation here for more information: https://github.com/rrousselGit/freezed#custom-getters-and-methods');

/// @nodoc
mixin _$GenerateImageState {
  bool get isLoading => throw _privateConstructorUsedError;
  GenerateImageModel? get data => throw _privateConstructorUsedError;

  @JsonKey(ignore: true)
  $GenerateImageStateCopyWith<GenerateImageState> get copyWith =>
      throw _privateConstructorUsedError;
}

/// @nodoc
abstract class $GenerateImageStateCopyWith<$Res> {
  factory $GenerateImageStateCopyWith(
          GenerateImageState value, $Res Function(GenerateImageState) then) =
      _$GenerateImageStateCopyWithImpl<$Res, GenerateImageState>;
  @useResult
  $Res call({bool isLoading, GenerateImageModel? data});
}

/// @nodoc
class _$GenerateImageStateCopyWithImpl<$Res, $Val extends GenerateImageState>
    implements $GenerateImageStateCopyWith<$Res> {
  _$GenerateImageStateCopyWithImpl(this._value, this._then);

  // ignore: unused_field
  final $Val _value;
  // ignore: unused_field
  final $Res Function($Val) _then;

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? isLoading = null,
    Object? data = freezed,
  }) {
    return _then(_value.copyWith(
      isLoading: null == isLoading
          ? _value.isLoading
          : isLoading // ignore: cast_nullable_to_non_nullable
              as bool,
      data: freezed == data
          ? _value.data
          : data // ignore: cast_nullable_to_non_nullable
              as GenerateImageModel?,
    ) as $Val);
  }
}

/// @nodoc
abstract class _$$_GenerateImageStateCopyWith<$Res>
    implements $GenerateImageStateCopyWith<$Res> {
  factory _$$_GenerateImageStateCopyWith(_$_GenerateImageState value,
          $Res Function(_$_GenerateImageState) then) =
      __$$_GenerateImageStateCopyWithImpl<$Res>;
  @override
  @useResult
  $Res call({bool isLoading, GenerateImageModel? data});
}

/// @nodoc
class __$$_GenerateImageStateCopyWithImpl<$Res>
    extends _$GenerateImageStateCopyWithImpl<$Res, _$_GenerateImageState>
    implements _$$_GenerateImageStateCopyWith<$Res> {
  __$$_GenerateImageStateCopyWithImpl(
      _$_GenerateImageState _value, $Res Function(_$_GenerateImageState) _then)
      : super(_value, _then);

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? isLoading = null,
    Object? data = freezed,
  }) {
    return _then(_$_GenerateImageState(
      isLoading: null == isLoading
          ? _value.isLoading
          : isLoading // ignore: cast_nullable_to_non_nullable
              as bool,
      data: freezed == data
          ? _value.data
          : data // ignore: cast_nullable_to_non_nullable
              as GenerateImageModel?,
    ));
  }
}

/// @nodoc

class _$_GenerateImageState extends _GenerateImageState {
  const _$_GenerateImageState({this.isLoading = false, this.data = null})
      : super._();

  @override
  @JsonKey()
  final bool isLoading;
  @override
  @JsonKey()
  final GenerateImageModel? data;

  @override
  String toString() {
    return 'GenerateImageState(isLoading: $isLoading, data: $data)';
  }

  @override
  bool operator ==(dynamic other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _$_GenerateImageState &&
            (identical(other.isLoading, isLoading) ||
                other.isLoading == isLoading) &&
            (identical(other.data, data) || other.data == data));
  }

  @override
  int get hashCode => Object.hash(runtimeType, isLoading, data);

  @JsonKey(ignore: true)
  @override
  @pragma('vm:prefer-inline')
  _$$_GenerateImageStateCopyWith<_$_GenerateImageState> get copyWith =>
      __$$_GenerateImageStateCopyWithImpl<_$_GenerateImageState>(
          this, _$identity);
}

abstract class _GenerateImageState extends GenerateImageState {
  const factory _GenerateImageState(
      {final bool isLoading,
      final GenerateImageModel? data}) = _$_GenerateImageState;
  const _GenerateImageState._() : super._();

  @override
  bool get isLoading;
  @override
  GenerateImageModel? get data;
  @override
  @JsonKey(ignore: true)
  _$$_GenerateImageStateCopyWith<_$_GenerateImageState> get copyWith =>
      throw _privateConstructorUsedError;
}
