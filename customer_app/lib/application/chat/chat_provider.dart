import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'chat_notifier.dart';
import 'chat_state.dart';

final chatProvider = StateNotifierProvider<ChatNotifier, ChatState>(
      (ref) => ChatNotifier(),
);
