

class GenerateImageModel {
  GenerateImageModel({
    this.created,
    this.data,
  });

  int? created;
  List<Datum>? data;

  GenerateImageModel copyWith({
    int? created,
    List<Datum>? data,
  }) =>
      GenerateImageModel(
        created: created ?? this.created,
        data: data ?? this.data,
      );

  factory GenerateImageModel.fromJson(Map<String, dynamic> json) => GenerateImageModel(
    created: json["created"],
    data: json["data"] == null ? [] : List<Datum>.from(json["data"]!.map((x) => Datum.fromJson(x))),
  );

  Map<String, dynamic> toJson() => {
    "created": created,
    "data": data == null ? [] : List<dynamic>.from(data!.map((x) => x.toJson())),
  };
}

class Datum {
  Datum({
    this.url,
  });

  String? url;

  Datum copyWith({
    String? url,
  }) =>
      Datum(
        url: url ?? this.url,
      );

  factory Datum.fromJson(Map<String, dynamic> json) => Datum(
    url: json["url"],
  );

  Map<String, dynamic> toJson() => {
    "url": url,
  };
}
