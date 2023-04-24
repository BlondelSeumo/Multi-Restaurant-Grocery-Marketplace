import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:riverpodtemp/domain/di/dependency_manager.dart';


import 'payment_notifier.dart';
import 'payment_state.dart';



final paymentProvider = StateNotifierProvider<PaymentNotifier, PaymentState>(
  (ref) => PaymentNotifier(paymentsRepository),
);
