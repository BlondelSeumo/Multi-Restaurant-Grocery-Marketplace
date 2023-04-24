// coverage:ignore-file
// GENERATED CODE - DO NOT MODIFY BY HAND
// ignore_for_file: type=lint
// ignore_for_file: unused_element, deprecated_member_use, deprecated_member_use_from_same_package, use_function_type_syntax_for_parameters, unnecessary_const, avoid_init_to_null, invalid_override_different_default_values_named, prefer_expression_function_bodies, annotate_overrides, invalid_annotation_target, unnecessary_question_mark

part of 'chat_state.dart';

// **************************************************************************
// FreezedGenerator
// **************************************************************************

T _$identity<T>(T value) => value;

final _privateConstructorUsedError = UnsupportedError(
    'It seems like you constructed your class using `MyClass._()`. This constructor is only meant to be used by freezed and you are not supposed to need it nor use it.\nPlease check the documentation here for more information: https://github.com/rrousselGit/freezed#custom-getters-and-methods');

/// @nodoc
mixin _$ChatState {
  bool get isLoading => throw _privateConstructorUsedError;
  bool get isMoreLoading => throw _privateConstructorUsedError;
  List<ChatMessageData> get chats => throw _privateConstructorUsedError;
  String get chatId => throw _privateConstructorUsedError;
  TextEditingController? get textController =>
      throw _privateConstructorUsedError;

  @JsonKey(ignore: true)
  $ChatStateCopyWith<ChatState> get copyWith =>
      throw _privateConstructorUsedError;
}

/// @nodoc
abstract class $ChatStateCopyWith<$Res> {
  factory $ChatStateCopyWith(ChatState value, $Res Function(ChatState) then) =
      _$ChatStateCopyWithImpl<$Res, ChatState>;
  @useResult
  $Res call(
      {bool isLoading,
      bool isMoreLoading,
      List<ChatMessageData> chats,
      String chatId,
      TextEditingController? textController});
}

/// @nodoc
class _$ChatStateCopyWithImpl<$Res, $Val extends ChatState>
    implements $ChatStateCopyWith<$Res> {
  _$ChatStateCopyWithImpl(this._value, this._then);

  // ignore: unused_field
  final $Val _value;
  // ignore: unused_field
  final $Res Function($Val) _then;

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? isLoading = null,
    Object? isMoreLoading = null,
    Object? chats = null,
    Object? chatId = null,
    Object? textController = freezed,
  }) {
    return _then(_value.copyWith(
      isLoading: null == isLoading
          ? _value.isLoading
          : isLoading // ignore: cast_nullable_to_non_nullable
              as bool,
      isMoreLoading: null == isMoreLoading
          ? _value.isMoreLoading
          : isMoreLoading // ignore: cast_nullable_to_non_nullable
              as bool,
      chats: null == chats
          ? _value.chats
          : chats // ignore: cast_nullable_to_non_nullable
              as List<ChatMessageData>,
      chatId: null == chatId
          ? _value.chatId
          : chatId // ignore: cast_nullable_to_non_nullable
              as String,
      textController: freezed == textController
          ? _value.textController
          : textController // ignore: cast_nullable_to_non_nullable
              as TextEditingController?,
    ) as $Val);
  }
}

/// @nodoc
abstract class _$$_ChatStateCopyWith<$Res> implements $ChatStateCopyWith<$Res> {
  factory _$$_ChatStateCopyWith(
          _$_ChatState value, $Res Function(_$_ChatState) then) =
      __$$_ChatStateCopyWithImpl<$Res>;
  @override
  @useResult
  $Res call(
      {bool isLoading,
      bool isMoreLoading,
      List<ChatMessageData> chats,
      String chatId,
      TextEditingController? textController});
}

/// @nodoc
class __$$_ChatStateCopyWithImpl<$Res>
    extends _$ChatStateCopyWithImpl<$Res, _$_ChatState>
    implements _$$_ChatStateCopyWith<$Res> {
  __$$_ChatStateCopyWithImpl(
      _$_ChatState _value, $Res Function(_$_ChatState) _then)
      : super(_value, _then);

  @pragma('vm:prefer-inline')
  @override
  $Res call({
    Object? isLoading = null,
    Object? isMoreLoading = null,
    Object? chats = null,
    Object? chatId = null,
    Object? textController = freezed,
  }) {
    return _then(_$_ChatState(
      isLoading: null == isLoading
          ? _value.isLoading
          : isLoading // ignore: cast_nullable_to_non_nullable
              as bool,
      isMoreLoading: null == isMoreLoading
          ? _value.isMoreLoading
          : isMoreLoading // ignore: cast_nullable_to_non_nullable
              as bool,
      chats: null == chats
          ? _value._chats
          : chats // ignore: cast_nullable_to_non_nullable
              as List<ChatMessageData>,
      chatId: null == chatId
          ? _value.chatId
          : chatId // ignore: cast_nullable_to_non_nullable
              as String,
      textController: freezed == textController
          ? _value.textController
          : textController // ignore: cast_nullable_to_non_nullable
              as TextEditingController?,
    ));
  }
}

/// @nodoc

class _$_ChatState extends _ChatState {
  const _$_ChatState(
      {this.isLoading = false,
      this.isMoreLoading = false,
      final List<ChatMessageData> chats = const [],
      this.chatId = '',
      this.textController})
      : _chats = chats,
        super._();

  @override
  @JsonKey()
  final bool isLoading;
  @override
  @JsonKey()
  final bool isMoreLoading;
  final List<ChatMessageData> _chats;
  @override
  @JsonKey()
  List<ChatMessageData> get chats {
    if (_chats is EqualUnmodifiableListView) return _chats;
    // ignore: implicit_dynamic_type
    return EqualUnmodifiableListView(_chats);
  }

  @override
  @JsonKey()
  final String chatId;
  @override
  final TextEditingController? textController;

  @override
  String toString() {
    return 'ChatState(isLoading: $isLoading, isMoreLoading: $isMoreLoading, chats: $chats, chatId: $chatId, textController: $textController)';
  }

  @override
  bool operator ==(dynamic other) {
    return identical(this, other) ||
        (other.runtimeType == runtimeType &&
            other is _$_ChatState &&
            (identical(other.isLoading, isLoading) ||
                other.isLoading == isLoading) &&
            (identical(other.isMoreLoading, isMoreLoading) ||
                other.isMoreLoading == isMoreLoading) &&
            const DeepCollectionEquality().equals(other._chats, _chats) &&
            (identical(other.chatId, chatId) || other.chatId == chatId) &&
            (identical(other.textController, textController) ||
                other.textController == textController));
  }

  @override
  int get hashCode => Object.hash(runtimeType, isLoading, isMoreLoading,
      const DeepCollectionEquality().hash(_chats), chatId, textController);

  @JsonKey(ignore: true)
  @override
  @pragma('vm:prefer-inline')
  _$$_ChatStateCopyWith<_$_ChatState> get copyWith =>
      __$$_ChatStateCopyWithImpl<_$_ChatState>(this, _$identity);
}

abstract class _ChatState extends ChatState {
  const factory _ChatState(
      {final bool isLoading,
      final bool isMoreLoading,
      final List<ChatMessageData> chats,
      final String chatId,
      final TextEditingController? textController}) = _$_ChatState;
  const _ChatState._() : super._();

  @override
  bool get isLoading;
  @override
  bool get isMoreLoading;
  @override
  List<ChatMessageData> get chats;
  @override
  String get chatId;
  @override
  TextEditingController? get textController;
  @override
  @JsonKey(ignore: true)
  _$$_ChatStateCopyWith<_$_ChatState> get copyWith =>
      throw _privateConstructorUsedError;
}
