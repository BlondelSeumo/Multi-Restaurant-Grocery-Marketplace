

import 'package:riverpodtemp/infrastructure/services/local_storage.dart';

class SearchProductModel {
  final String text;
  final int page;
  SearchProductModel({
    required this.text,
    required this.page,
  });

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{};
    map["search"] = text;
    map["perPage"] = 10;
    map["page"] = page;
    map["lang"] = LocalStorage.instance.getLanguage()?.locale ?? "en";
    return map;
  }
}
