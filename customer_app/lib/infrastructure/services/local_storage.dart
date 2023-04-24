import 'dart:convert';

import 'package:riverpodtemp/infrastructure/models/data/address_information.dart';
import 'package:riverpodtemp/infrastructure/models/models.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'app_constants.dart';


class LocalStorage {
  static SharedPreferences? _preferences;

  LocalStorage._();

  static LocalStorage? _localStorage;

  static LocalStorage instance = LocalStorage._();

  static Future<LocalStorage> getInstance() async {
    if (_localStorage == null) {
      _localStorage = LocalStorage._();
      await _localStorage!._init();
    }
    return _localStorage!;
  }

  static Future<SharedPreferences> getSharedPreferences() async {
    if (_preferences == null) {
      _localStorage = LocalStorage._();
      await _localStorage!._init();
    }
    return _preferences!;
  }

  Future<void> _init() async {
    _preferences = await SharedPreferences.getInstance();
  }
  Future<void> setToken(String? token) async {
    if (_preferences != null) {
      await _preferences!.setString(AppConstants.keyToken, token ?? '');
    }
  }

  String getToken() => _preferences?.getString(AppConstants.keyToken) ?? '';

  void deleteToken() => _preferences?.remove(AppConstants.keyToken);

  Future<void> setLastName(String? image) async {
    if (_preferences != null) {
      await _preferences!.setString(AppConstants.keyLastName, image ?? "");
    }
  }

  String getLastName() => _preferences?.getString(AppConstants.keyLastName) ?? "";

  void deleteLastName() => _preferences?.remove(AppConstants.keyLastName);

  Future<void> setFirstName(String? image) async {
    if (_preferences != null) {
      await _preferences!.setString(AppConstants.keyFirstName, image ?? "");
    }
  }

  String getFirstName() => _preferences?.getString(AppConstants.keyFirstName) ?? "";

  void deleteFirstName() => _preferences?.remove(AppConstants.keyFirstName);


  Future<void> setUserId(int? image) async {
    if (_preferences != null) {
      await _preferences!.setInt(AppConstants.keyUserId, image ?? 0);
    }
  }

  int getUserId() => _preferences?.getInt(AppConstants.keyUserId) ?? 0;

  void deleteUserId() => _preferences?.remove(AppConstants.keyUserId);


  Future<void> setProfileImage(String? image) async {
    if (_preferences != null) {
      await _preferences!.setString(AppConstants.keyUserImage, image ?? '');
    }
  }

  String getProfileImage() => _preferences?.getString(AppConstants.keyUserImage) ?? '';

  void deleteProfileImage() => _preferences?.remove(AppConstants.keyUserImage);

  Future<void> setSearchHistory(List<String> list) async {
    if (_preferences != null) {
      final List<String> idsStrings = list.map((e) => e.toString()).toList();
      await _preferences!
          .setStringList(AppConstants.keySearchStores, idsStrings);
    }
  }

  List<String> getSearchList() {
    final List<String> strings =
        _preferences?.getStringList(AppConstants.keySearchStores) ?? [];
    return strings;
  }

  void deleteSearchList() =>
      _preferences?.remove(AppConstants.keySearchStores);


  Future<void> setSavedShopsList(List<int> ids) async {
    if (_preferences != null) {
      final List<String> idsStrings = ids.map((e) => e.toString()).toList();
      await _preferences!
          .setStringList(AppConstants.keySavedStores, idsStrings);
    }
  }

  List<int> getSavedShopsList() {
    final List<String> strings =
        _preferences?.getStringList(AppConstants.keySavedStores) ?? [];
    if(strings.isNotEmpty){
      final List<int> ids = strings.map((e) => int.parse(e)).toList();
      return ids;
    }else{
      return [];
    }

  }

  void deleteSavedShopsList() =>
      _preferences?.remove(AppConstants.keySavedStores);


  Future<void> setAddressSelected(AddressData data) async {
    if (_preferences != null) {
      await _preferences!.setString(AppConstants.keyAddressSelected, jsonEncode(data.toJson()));
    }
  }

  AddressData? getAddressSelected() {
    String dataString =  _preferences?.getString(AppConstants.keyAddressSelected) ?? "";
    if(dataString.isNotEmpty){
      AddressData data = AddressData.fromJson(jsonDecode(dataString));
      return data;
    }else{
      return null;
    }

  }

  void deleteAddressSelected() =>
      _preferences?.remove(AppConstants.keyAddressSelected);

  Future<void> setAddressInformation(AddressInformation data) async {
    if (_preferences != null) {
      await _preferences!.setString(AppConstants.keyAddressInformation, jsonEncode(data.toJson()));
    }
  }

  AddressInformation? getAddressInformation() {
    String dataString =  _preferences?.getString(AppConstants.keyAddressInformation) ?? "";
    if(dataString.isNotEmpty){
      AddressInformation data = AddressInformation.fromJson(jsonDecode(dataString));
      return data;
    }else{
      return null;
    }
  }

  void deleteAddressInformation() =>
      _preferences?.remove(AppConstants.keyAddressInformation);

  Future<void> setLanguageSelected(bool selected) async {
    if (_preferences != null) {
      await _preferences!.setBool(AppConstants.keyLangSelected, selected);
    }
  }

  bool getLanguageSelected() =>
      _preferences?.getBool(AppConstants.keyLangSelected) ?? false;

  void deleteLangSelected() =>
      _preferences?.remove(AppConstants.keyLangSelected);

  Future<void> setSelectedCurrency(CurrencyData currency) async {
    if (_preferences != null) {
      final String currencyString = jsonEncode(currency.toJson());
      await _preferences!
          .setString(AppConstants.keySelectedCurrency, currencyString);
    }
  }

  CurrencyData getSelectedCurrency() {
    String json =  _preferences?.getString(AppConstants.keySelectedCurrency) ?? '';
    if(json.isEmpty){
      return CurrencyData();
    }else{
      final map = jsonDecode(json);
      return CurrencyData.fromJson(map);
    }

  }

  void deleteSelectedCurrency() =>
      _preferences?.remove(AppConstants.keySelectedCurrency);

  Future<void> setWalletData(Wallet? wallet) async {
    if (_preferences != null) {
      final String walletString = jsonEncode(wallet?.toJson());
      await _preferences!.setString(AppConstants.keyWalletData, walletString);
    }
  }

  Wallet? getWalletData() {
    final wallet = _preferences?.getString(AppConstants.keyWalletData);
    if (wallet == null) {
      return null;
    }
    final map = jsonDecode(wallet);
    if (map == null) {
      return null;
    }
    return Wallet.fromJson(map);
  }

  void deleteWalletData() => _preferences?.remove(AppConstants.keyWalletData);


  Future<void> setSettingsList(List<SettingsData> settings) async {
    if (_preferences != null) {
      final List<String> strings =
          settings.map((setting) => jsonEncode(setting.toJson())).toList();
      await _preferences!
          .setStringList(AppConstants.keyGlobalSettings, strings);
    }
  }

  List<SettingsData> getSettingsList() {
    final List<String> settings =
        _preferences?.getStringList(AppConstants.keyGlobalSettings) ?? [];
    final List<SettingsData> settingsList = settings
        .map(
          (setting) => SettingsData.fromJson(jsonDecode(setting)),
        )
        .toList();
    return settingsList;
  }

  void deleteSettingsList() =>
      _preferences?.remove(AppConstants.keyGlobalSettings);

  Future<void> setTranslations(Map<String, dynamic>? translations) async {
    if (_preferences != null) {
      final String encoded = jsonEncode(translations);
      await _preferences!.setString(AppConstants.keyTranslations, encoded);
    }
  }

  Map<String, dynamic> getTranslations() {
    final String encoded =
        _preferences?.getString(AppConstants.keyTranslations) ?? '';
    if (encoded.isEmpty) {
      return {};
    }
    final Map<String, dynamic> decoded = jsonDecode(encoded);
    return decoded;
  }

  void deleteTranslations() =>
      _preferences?.remove(AppConstants.keyTranslations);

  Future<void> setAppThemeMode(bool isDarkMode) async {
    if (_preferences != null) {
      await _preferences!.setBool(AppConstants.keyAppThemeMode, isDarkMode);
    }
  }

  bool getAppThemeMode() =>
      _preferences?.getBool(AppConstants.keyAppThemeMode) ?? false;

  void deleteAppThemeMode() =>
      _preferences?.remove(AppConstants.keyAppThemeMode);

  Future<void> setSettingsFetched(bool fetched) async {
    if (_preferences != null) {
      await _preferences!.setBool(AppConstants.keySettingsFetched, fetched);
    }
  }

  bool getSettingsFetched() =>
      _preferences?.getBool(AppConstants.keySettingsFetched) ?? false;

  void deleteSettingsFetched() =>
      _preferences?.remove(AppConstants.keySettingsFetched);

  Future<void> setLanguageData(LanguageData? langData) async {
    if (_preferences != null) {
      final String lang = jsonEncode(langData?.toJson());
      await _preferences!.setString(AppConstants.keyLanguageData, lang);
    }
  }

  LanguageData? getLanguage() {
    final lang = _preferences?.getString(AppConstants.keyLanguageData);
    if (lang == null) {
      return null;
    }
    final map = jsonDecode(lang);
    if (map == null) {
      return null;
    }
    return LanguageData.fromJson(map);
  }

  void deleteLanguage() => _preferences?.remove(AppConstants.keyLanguageData);


  Future<void> setLangLtr(int? backward) async {
    if (_preferences != null) {
      await _preferences!.setBool(AppConstants.keyLangLtr, backward == 0);
    }
  }

  bool getLangLtr() => _preferences?.getBool(AppConstants.keyLangLtr) ?? true;

  void deleteLangLtr() => _preferences?.remove(AppConstants.keyLangLtr);

  void logout() {
    deleteWalletData();
    deleteSavedShopsList();
    deleteSearchList();
    deleteProfileImage();
    deleteUserId();
    deleteFirstName();
    deleteLastName();
    deleteToken();
  }
}
