// coverage:ignore-file
// GENERATED CODE - DO NOT MODIFY BY HAND
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'currency_state.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

T _$identity<T>(T value) => value;

final _privateConstructorUsedError = UnsupportedError(
    'It seems like you constructed your class using `MyClass._()`. This constructor is only meant to be used by freezed and you are not supposed to need it nor use it.\nPlease check the documentation here for more information: https://github.com/rrousselGit/freezed#custom-getters-and-methods');

/// @nodoc
mixin _$CurrencyState {
  List<CurrencyData> get list => throw _privateConstructorUsedError;
  bool get isLoading => throw _privateConstructorUsedError;
  int get index => throw _privateConstructorUsedError;

  @JsonKey(ignore: true)
  $CurrencyStateCopyWith<CurrencyState> get copyWith =>
      throw _privateConstructorUsedError;
}

/// @nodoc
abstract class $CurrencyStateCopyWith<$Res> {
  factory $CurrencyStateCopyWith(
          CurrencyState value, $Res Function(CurrencyState) then) =
      _$CurrencyStateCopyWithImpl<$Res, CurrencyState>;
  @useResult
  $Res call({List<CurrencyData> list, bool isLoading, int index});
}

/// @nodoc
class _$CurrencyStateCopyWithImpl<$Res, $Val extends CurrencyState>
    implements $CurrencyStateCopyWith<$Res> {
  _$CurrencyStateCopyWithImpl(this._value, this._then);

  // ignore: unused_field
  final $Val _value;
  // ignore: unused_field
  final $Res Function($Val) _then;

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? list = null,
    Object? isLoading = null,
    Object? index = null,
  }) {
    return _then(_value.copyWith(
      list: null == list
          ? _value.list
          : list // ignore: cast_nullable_to_non_nullable
              as List<CurrencyData>,
      isLoading: null == isLoading
          ? _value.isLoading
          : isLoading // ignore: cast_nullable_to_non_nullable
              as bool,
      index: null == index
          ? _value.index
          : index // ignore: cast_nullable_to_non_nullable
              as int,
    ) as $Val);
  }
}

/// @nodoc
abstract class _$$_CurrencyStateCopyWith<$Res>
    implements $CurrencyStateCopyWith<$Res> {
  factory _$$_CurrencyStateCopyWith(
          _$_CurrencyState value, $Res Function(_$_CurrencyState) then) =
      __$$_CurrencyStateCopyWithImpl<$Res>;
  @override
  @useResult
  $Res call({List<CurrencyData> list, bool isLoading, int index});
}

/// @nodoc
class __$$_CurrencyStateCopyWithImpl<$Res>
    extends _$CurrencyStateCopyWithImpl<$Res, _$_CurrencyState>
    implements _$$_CurrencyStateCopyWith<$Res> {
  __$$_CurrencyStateCopyWithImpl(
      _$_CurrencyState _value, $Res Function(_$_CurrencyState) _then)
      : super(_value, _then);

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? list = null,
    Object? isLoading = null,
    Object? index = null,
  }) {
    return _then(_$_CurrencyState(
      list: null == list
          ? _value._list
          : list // ignore: cast_nullable_to_non_nullable
              as List<CurrencyData>,
      isLoading: null == isLoading
          ? _value.isLoading
          : isLoading // ignore: cast_nullable_to_non_nullable
              as bool,
      index: null == index
          ? _value.index
          : index // ignore: cast_nullable_to_non_nullable
              as int,
    ));
  }
}

/// @nodoc

class _$_CurrencyState extends _CurrencyState {
  const _$_CurrencyState(
      {final List<CurrencyData> list = const [],
      this.isLoading = true,
      this.index = 0})
      : _list = list,
        super._();

  final List<CurrencyData> _list;
  @override
  @JsonKey()
  List<CurrencyData> get list {
    if (_list is EqualUnmodifiableListView) return _list;
    // ignore: implicit_dynamic_type
    return EqualUnmodifiableListView(_list);
  }

  @override
  @JsonKey()
  final bool isLoading;
  @override
  @JsonKey()
  final int index;

  @override
  String toString() {
    return 'CurrencyState(list: $list, isLoading: $isLoading, index: $index)';
  }

  @override
  bool operator ==(dynamic other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _$_CurrencyState &&
            const DeepCollectionEquality().equals(other._list, _list) &&
            (identical(other.isLoading, isLoading) ||
                other.isLoading == isLoading) &&
            (identical(other.index, index) || other.index == index));
  }

  @override
  int get hashCode => Object.hash(runtimeType,
      const DeepCollectionEquality().hash(_list), isLoading, index);

  @JsonKey(ignore: true)
  @override
  @pragma('vm:prefer-inline')
  _$$_CurrencyStateCopyWith<_$_CurrencyState> get copyWith =>
      __$$_CurrencyStateCopyWithImpl<_$_CurrencyState>(this, _$identity);
}

abstract class _CurrencyState extends CurrencyState {
  const factory _CurrencyState(
      {final List<CurrencyData> list,
      final bool isLoading,
      final int index}) = _$_CurrencyState;
  const _CurrencyState._() : super._();

  @override
  List<CurrencyData> get list;
  @override
  bool get isLoading;
  @override
  int get index;
  @override
  @JsonKey(ignore: true)
  _$$_CurrencyStateCopyWith<_$_CurrencyState> get copyWith =>
      throw _privateConstructorUsedError;
}
