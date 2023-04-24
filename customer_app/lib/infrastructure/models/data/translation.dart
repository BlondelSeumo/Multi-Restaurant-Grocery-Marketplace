class Translation {
  Translation({
    int? id,
    String? locale,
    String? title,
    String? description,
    String? shortDesc,
    String? address,
  }) {
    _id = id;
    _locale = locale;
    _title = title;
    _description = description;
    _shortDesc = shortDesc;
    _address = address;
  }

  Translation.fromJson(dynamic json) {
    _id = json?['id'];
    _locale = json?['locale'];
    _title = json?['title'];
    _description = json?['description'];
    _shortDesc = json?['short_desc'] ?? json?['faq'];
    _address = json?['address'];
  }

  int? _id;
  String? _locale;
  String? _title;
  String? _description;
  String? _shortDesc;
  String? _address;

  Translation copyWith({
    int? id,
    String? locale,
    String? title,
    String? description,
    String? shortDesc,
    String? address,
  }) =>
      Translation(
        id: id ?? _id,
        locale: locale ?? _locale,
        title: title ?? _title,
        description: description ?? _description,
        shortDesc: shortDesc ?? _shortDesc,
        address: address ?? _address,
      );

  int? get id => _id;

  String? get locale => _locale;

  String? get title => _title;

  String? get description => _description;

  String? get shortDesc => _shortDesc;

  String? get address => _address;

  Map<String, dynamic> toJson() {
    final map = <String, dynamic>{};
    map['id'] = _id;
    map['locale'] = _locale;
    map['title'] = _title;
    map['description'] = _description;
    map['short_desc'] = _shortDesc;
    map['address'] = _address;
    return map;
  }
}
