// coverage:ignore-file
// GENERATED CODE - DO NOT MODIFY BY HAND
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'app_state.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

T _$identity<T>(T value) => value;

final _privateConstructorUsedError = UnsupportedError(
    'It seems like you constructed your class using `MyClass._()`. This constructor is only meant to be used by freezed and you are not supposed to need it nor use it.\nPlease check the documentation here for more information: https://github.com/rrousselGit/freezed#custom-getters-and-methods');

/// @nodoc
mixin _$AppState {
  bool get isDarkMode => throw _privateConstructorUsedError;
  bool get isLtr => throw _privateConstructorUsedError;
  LanguageData? get activeLanguage => throw _privateConstructorUsedError;

  @JsonKey(ignore: true)
  $AppStateCopyWith<AppState> get copyWith =>
      throw _privateConstructorUsedError;
}

/// @nodoc
abstract class $AppStateCopyWith<$Res> {
  factory $AppStateCopyWith(AppState value, $Res Function(AppState) then) =
      _$AppStateCopyWithImpl<$Res, AppState>;
  @useResult
  $Res call({bool isDarkMode, bool isLtr, LanguageData? activeLanguage});
}

/// @nodoc
class _$AppStateCopyWithImpl<$Res, $Val extends AppState>
    implements $AppStateCopyWith<$Res> {
  _$AppStateCopyWithImpl(this._value, this._then);

  // ignore: unused_field
  final $Val _value;
  // ignore: unused_field
  final $Res Function($Val) _then;

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? isDarkMode = null,
    Object? isLtr = null,
    Object? activeLanguage = freezed,
  }) {
    return _then(_value.copyWith(
      isDarkMode: null == isDarkMode
          ? _value.isDarkMode
          : isDarkMode // ignore: cast_nullable_to_non_nullable
              as bool,
      isLtr: null == isLtr
          ? _value.isLtr
          : isLtr // ignore: cast_nullable_to_non_nullable
              as bool,
      activeLanguage: freezed == activeLanguage
          ? _value.activeLanguage
          : activeLanguage // ignore: cast_nullable_to_non_nullable
              as LanguageData?,
    ) as $Val);
  }
}

/// @nodoc
abstract class _$$_AppStateCopyWith<$Res> implements $AppStateCopyWith<$Res> {
  factory _$$_AppStateCopyWith(
          _$_AppState value, $Res Function(_$_AppState) then) =
      __$$_AppStateCopyWithImpl<$Res>;
  @override
  @useResult
  $Res call({bool isDarkMode, bool isLtr, LanguageData? activeLanguage});
}

/// @nodoc
class __$$_AppStateCopyWithImpl<$Res>
    extends _$AppStateCopyWithImpl<$Res, _$_AppState>
    implements _$$_AppStateCopyWith<$Res> {
  __$$_AppStateCopyWithImpl(
      _$_AppState _value, $Res Function(_$_AppState) _then)
      : super(_value, _then);

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? isDarkMode = null,
    Object? isLtr = null,
    Object? activeLanguage = freezed,
  }) {
    return _then(_$_AppState(
      isDarkMode: null == isDarkMode
          ? _value.isDarkMode
          : isDarkMode // ignore: cast_nullable_to_non_nullable
              as bool,
      isLtr: null == isLtr
          ? _value.isLtr
          : isLtr // ignore: cast_nullable_to_non_nullable
              as bool,
      activeLanguage: freezed == activeLanguage
          ? _value.activeLanguage
          : activeLanguage // ignore: cast_nullable_to_non_nullable
              as LanguageData?,
    ));
  }
}

/// @nodoc

class _$_AppState extends _AppState {
  const _$_AppState(
      {this.isDarkMode = false, this.isLtr = true, this.activeLanguage})
      : super._();

  @override
  @JsonKey()
  final bool isDarkMode;
  @override
  @JsonKey()
  final bool isLtr;
  @override
  final LanguageData? activeLanguage;

  @override
  String toString() {
    return 'AppState(isDarkMode: $isDarkMode, isLtr: $isLtr, activeLanguage: $activeLanguage)';
  }

  @override
  bool operator ==(dynamic other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _$_AppState &&
            (identical(other.isDarkMode, isDarkMode) ||
                other.isDarkMode == isDarkMode) &&
            (identical(other.isLtr, isLtr) || other.isLtr == isLtr) &&
            (identical(other.activeLanguage, activeLanguage) ||
                other.activeLanguage == activeLanguage));
  }

  @override
  int get hashCode =>
      Object.hash(runtimeType, isDarkMode, isLtr, activeLanguage);

  @JsonKey(ignore: true)
  @override
  @pragma('vm:prefer-inline')
  _$$_AppStateCopyWith<_$_AppState> get copyWith =>
      __$$_AppStateCopyWithImpl<_$_AppState>(this, _$identity);
}

abstract class _AppState extends AppState {
  const factory _AppState(
      {final bool isDarkMode,
      final bool isLtr,
      final LanguageData? activeLanguage}) = _$_AppState;
  const _AppState._() : super._();

  @override
  bool get isDarkMode;
  @override
  bool get isLtr;
  @override
  LanguageData? get activeLanguage;
  @override
  @JsonKey(ignore: true)
  _$$_AppStateCopyWith<_$_AppState> get copyWith =>
      throw _privateConstructorUsedError;
}
