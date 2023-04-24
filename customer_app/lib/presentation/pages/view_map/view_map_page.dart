import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter_remix/flutter_remix.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:geolocator/geolocator.dart';
import 'package:google_maps_flutter/google_maps_flutter.dart';
import 'package:osm_nominatim/osm_nominatim.dart';
import 'package:riverpodtemp/application/app_widget/app_provider.dart';
import 'package:riverpodtemp/application/home/home_provider.dart';
import 'package:riverpodtemp/infrastructure/models/data/address_information.dart';
import 'package:riverpodtemp/infrastructure/models/models.dart';
import 'package:riverpodtemp/infrastructure/services/app_constants.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/local_storage.dart';
import 'package:riverpodtemp/infrastructure/services/tpying_delay.dart';
import 'package:riverpodtemp/infrastructure/services/tr_keys.dart';
import 'package:riverpodtemp/presentation/components/buttons/custom_button.dart';
import 'package:riverpodtemp/presentation/components/buttons/pop_button.dart';
import 'package:riverpodtemp/presentation/components/keyboard_dismisser.dart';
import 'package:riverpodtemp/presentation/components/text_fields/outline_bordered_text_field.dart';
import 'package:riverpodtemp/presentation/components/text_fields/search_text_field.dart';
import 'package:riverpodtemp/presentation/components/title_icon.dart';
import 'package:riverpodtemp/presentation/routes/app_router.gr.dart';
import 'package:riverpodtemp/presentation/theme/theme.dart';
import 'package:sliding_up_panel/sliding_up_panel.dart';
import '../../../../application/map/view_map_notifier.dart';
import '../../../../application/map/view_map_provider.dart';

class ViewMapPage extends ConsumerStatefulWidget {
  final bool isShopLocation;
  final int? shopId;

  const ViewMapPage({
    Key? key,
    this.isShopLocation = false,
    this.shopId,
  }) : super(key: key);

  @override
  ConsumerState<ViewMapPage> createState() => _ViewMapPageState();
}

class _ViewMapPageState extends ConsumerState<ViewMapPage> {
  late ViewMapNotifier event;
  late TextEditingController controller;
  late TextEditingController office;
  late TextEditingController house;
  late TextEditingController floor;
  final GeolocatorPlatform _geolocatorPlatform = GeolocatorPlatform.instance;
  GoogleMapController? googleMapController;
  CameraPosition? cameraPosition;
  dynamic check;
  late LatLng latLng;

  @override
  void didChangeDependencies() {
    event = ref.read(viewMapProvider.notifier);
    super.didChangeDependencies();
  }

  @override
  void dispose() {
    controller.dispose();
    office.dispose();
    house.dispose();
    floor.dispose();
    super.dispose();
  }

  checkPermission() async {
    check = await _geolocatorPlatform.checkPermission();
  }

  Future<void> getMyLocation() async {
    if (check == LocationPermission.denied ||
        check == LocationPermission.deniedForever) {
      check = await Geolocator.requestPermission();
      if (check != LocationPermission.denied &&
          check != LocationPermission.deniedForever) {
        var loc = await Geolocator.getCurrentPosition();
        latLng = LatLng(loc.latitude, loc.longitude);
        googleMapController!
            .animateCamera(CameraUpdate.newLatLngZoom(latLng, 15));
      }
    } else {
      if (check != LocationPermission.deniedForever) {
        var loc = await Geolocator.getCurrentPosition();
        latLng = LatLng(loc.latitude, loc.longitude);
        googleMapController!
            .animateCamera(CameraUpdate.newLatLngZoom(latLng, 15));
      }
    }
  }

  @override
  void initState() {
    controller = TextEditingController();
    office = TextEditingController();
    house = TextEditingController();
    floor = TextEditingController();
    latLng = LatLng(
      LocalStorage.instance.getAddressSelected()?.location?.latitude ??
          (AppHelpers.getInitialLatitude() ?? AppConstants.demoLatitude),
      LocalStorage.instance.getAddressSelected()?.location?.longitude ??
          (AppHelpers.getInitialLongitude() ?? AppConstants.demoLongitude),
    );
    checkPermission();
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(viewMapProvider);
    final bool isLtr = LocalStorage.instance.getLangLtr();
    final bool isDarkMode = ref.watch(appProvider).isDarkMode;
    return KeyboardDismisser(
      child: Directionality(
        textDirection: isLtr ? TextDirection.ltr : TextDirection.rtl,
        child: Scaffold(
          backgroundColor: isDarkMode ? Style.mainBackDark : Style.mainBack,
          body: Column(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: SlidingUpPanel(
                  borderRadius: BorderRadius.only(
                    topLeft: Radius.circular(15.r),
                    topRight: Radius.circular(15.r),
                  ),
                  minHeight: 240.h,
                  maxHeight: 240.h,
                  color: isDarkMode ? Style.dontHaveAnAccBackDark : Style.white,
                  body: Padding(
                    padding: REdgeInsets.only(bottom: 0),
                    child: Stack(
                      children: [
                        GoogleMap(
                          padding: REdgeInsets.only(bottom: 240.h),
                          myLocationButtonEnabled: false,
                          initialCameraPosition: CameraPosition(
                            bearing: 0,
                            target: latLng,
                            tilt: 0,
                            zoom: 17,
                          ),
                          mapToolbarEnabled: false,
                          zoomControlsEnabled: true,
                          onTap: (position) {
                            event.updateActive();
                            Delayed(milliseconds: 700).run(() async {
                              Place placemarks = await Nominatim.reverseSearch(
                                lat: cameraPosition?.target.latitude ??
                                    latLng.latitude,
                                lon: cameraPosition?.target.longitude ??
                                    latLng.longitude,
                                addressDetails: true,
                                extraTags: true,
                                nameDetails: true,
                              );
                              event
                                // ignore: use_build_context_synchronously
                                ..checkDriverZone(
                                    context: context,
                                    location: LatLng(
                                      cameraPosition?.target.latitude ??
                                          latLng.latitude,
                                      cameraPosition?.target.longitude ??
                                          latLng.longitude,
                                    ),
                                    shopId: widget.shopId)
                                ..changePlace(
                                  AddressData(
                                    title: placemarks.address?["country"] ?? "",
                                    address:
                                        "${placemarks.address?["city"] ?? ""}, ${placemarks.address?["state"] ?? placemarks.address?["county"] ?? ""}",
                                    location: LocationModel(
                                      latitude:
                                          cameraPosition?.target.latitude ??
                                              latLng.latitude,
                                      longitude:
                                          cameraPosition?.target.longitude ??
                                              latLng.longitude,
                                    ),
                                  ),
                                );
                              controller.text =
                                  "${placemarks.address?["country"] ?? ""}, ${placemarks.address?["city"] ?? ""} ${placemarks.address?["state"] ?? placemarks.address?["county"] ?? ""} ";
                            });
                            googleMapController!.animateCamera(
                                CameraUpdate.newLatLngZoom(position, 15));
                          },
                          onCameraIdle: () {
                            event.updateActive();
                            Delayed(milliseconds: 700).run(() async {
                              Place placemarks = await Nominatim.reverseSearch(
                                lat: cameraPosition?.target.latitude ??
                                    latLng.latitude,
                                lon: cameraPosition?.target.longitude ??
                                    latLng.longitude,
                                addressDetails: true,
                                extraTags: true,
                                nameDetails: true,
                              );
                              if (!widget.isShopLocation) {
                                event
                                  // ignore: use_build_context_synchronously
                                  ..checkDriverZone(
                                      context: context,
                                      location: LatLng(
                                        cameraPosition?.target.latitude ??
                                            latLng.latitude,
                                        cameraPosition?.target.longitude ??
                                            latLng.longitude,
                                      ),
                                      shopId: widget.shopId)
                                  ..changePlace(
                                    AddressData(
                                      title:
                                          placemarks.address?["country"] ?? "",
                                      address:
                                          "${placemarks.address?["city"] ?? ""}, ${placemarks.address?["state"] ?? placemarks.address?["county"] ?? ""}",
                                      location: LocationModel(
                                        latitude:
                                            cameraPosition?.target.latitude ??
                                                latLng.latitude,
                                        longitude:
                                            cameraPosition?.target.longitude ??
                                                latLng.longitude,
                                      ),
                                    ),
                                  );
                              } else {
                                event.changePlace(
                                  AddressData(
                                    title: placemarks.address?["country"] ?? "",
                                    address:
                                        "${placemarks.address?["city"] ?? ""}, ${placemarks.address?["state"] ?? placemarks.address?["county"] ?? ""}",
                                    location: LocationModel(
                                      latitude:
                                          cameraPosition?.target.latitude ??
                                              latLng.latitude,
                                      longitude:
                                          cameraPosition?.target.longitude ??
                                              latLng.longitude,
                                    ),
                                  ),
                                );
                              }
                              controller.text =
                                  "${placemarks.address?["country"] ?? ""}, ${placemarks.address?["city"] ?? ""} ${placemarks.address?["state"] ?? placemarks.address?["county"] ?? ""} ";
                            });
                          },
                          onCameraMove: (position) {
                            cameraPosition = position;
                          },
                          onMapCreated: (controller) {
                            googleMapController = controller;
                          },
                        ),
                        Positioned(
                          bottom: MediaQuery.of(context).padding.bottom +
                              85.h +
                              MediaQuery.of(context).size.height / 2,
                          left: MediaQuery.of(context).size.width / 2 - 23.w,
                          child: Image.asset(
                            "assets/images/marker.png",
                            width: 46.w,
                            height: 46.h,
                          ),
                        ),
                        Positioned(
                          bottom: 260.h,
                          right: 16.w,
                          child: InkWell(
                            onTap: () async {
                              await getMyLocation();
                            },
                            child: Container(
                              width: 50.r,
                              height: 50.r,
                              decoration: BoxDecoration(
                                  color: Style.white,
                                  borderRadius:
                                      BorderRadius.all(Radius.circular(10.r)),
                                  boxShadow: [
                                    BoxShadow(
                                        color: Style.shimmerBase,
                                        blurRadius: 2,
                                        offset: const Offset(0, 2))
                                  ]),
                              child: const Center(
                                  child: Icon(FlutterRemix.navigation_line)),
                            ),
                          ),
                        )
                      ],
                    ),
                  ),
                  padding: REdgeInsets.symmetric(horizontal: 15),
                  panel: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      8.verticalSpace,
                      Container(
                        width: 49.w,
                        height: 3.h,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(40.r),
                          color: Style.dragElement,
                        ),
                      ),
                      16.verticalSpace,
                      TitleAndIcon(
                          title: AppHelpers.getTranslation(
                              TrKeys.enterADeliveryAddress)),
                      24.verticalSpace,
                      SearchTextField(
                        isRead: true,
                        isBorder: true,
                        textEditingController: controller,
                        onTap: () async {
                          Place? data = await context
                              .pushRoute(const MapSearchRoute()) as Place?;
                          if (data != null) {
                            controller.text =
                                "${data.address?["country"] ?? ""}, ${data.address?["city"] ?? ""}, ${data.address?["state"] ?? data.address?["county"] ?? ""}";
                            googleMapController!.animateCamera(
                                CameraUpdate.newLatLngZoom(
                                    LatLng(data.lat, data.lon), 15));
                            event.changePlace(
                              AddressData(
                                title: data.address?["country"] ?? "",
                                address:
                                    "${data.address?["city"] ?? ""}, ${data.address?["state"] ?? data.address?["county"] ?? ""}",
                                location: LocationModel(
                                  latitude: data.lat,
                                  longitude: data.lon,
                                ),
                              ),
                            );
                          }
                        },
                      ),
                      24.verticalSpace,
                      Padding(
                        padding: EdgeInsets.only(
                            bottom: MediaQuery.of(context).padding.bottom),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const PopButton(),
                            24.horizontalSpace,
                            Expanded(
                              child: CustomButton(
                                isLoading: !widget.isShopLocation
                                    ? state.isLoading
                                    : false,
                                background: !widget.isShopLocation
                                    ? (state.isActive
                                        ? Style.brandGreen
                                        : Style.bgGrey)
                                    : Style.brandGreen,
                                textColor: !widget.isShopLocation
                                    ? (state.isActive
                                        ? Style.black
                                        : Style.textGrey)
                                    : Style.black,
                                title: !widget.isShopLocation
                                    ? (state.isActive
                                    ? AppHelpers.getTranslation(TrKeys.apply)
                                    : AppHelpers.getTranslation(TrKeys.noDriverZone))
                                    : AppHelpers.getTranslation(TrKeys.apply) ,
                                onPressed: () {
                                  if (widget.isShopLocation) {
                                    Navigator.pop(context, state.place);
                                  } else {
                                    if (state.isActive) {
                                      ref.read(homeProvider.notifier)
                                        ..fetchBanner(context)
                                        ..fetchRestaurant(context)
                                        ..fetchShopRecommend(context)
                                        ..fetchShop(context)
                                        ..fetchStore(context)
                                        ..fetchRestaurantNew(context)
                                        ..fetchCategories(context);
                                      LocalStorage.instance.setAddressSelected(
                                          state.place ?? AddressData());
                                      ref
                                          .read(homeProvider.notifier)
                                          .setAddress();
                                      AppHelpers.showAlertDialog(
                                          context: context,
                                          child: SingleChildScrollView(
                                            child: Column(
                                              mainAxisSize: MainAxisSize.min,
                                              children: [
                                                TitleAndIcon(
                                                  title: AppHelpers
                                                      .getTranslation(TrKeys
                                                          .addAddressInformation),
                                                  paddingHorizontalSize: 0,
                                                ),
                                                24.verticalSpace,
                                                OutlinedBorderTextField(
                                                  textController: office,
                                                  label:
                                                      AppHelpers.getTranslation(
                                                              TrKeys.office)
                                                          .toUpperCase(),
                                                ),
                                                24.verticalSpace,
                                                OutlinedBorderTextField(
                                                  textController: house,
                                                  label:
                                                      AppHelpers.getTranslation(
                                                              TrKeys.house)
                                                          .toUpperCase(),
                                                ),
                                                24.verticalSpace,
                                                OutlinedBorderTextField(
                                                  textController: floor,
                                                  label:
                                                      AppHelpers.getTranslation(
                                                              TrKeys.floor)
                                                          .toUpperCase(),
                                                ),
                                                32.verticalSpace,
                                                CustomButton(
                                                    title: AppHelpers
                                                        .getTranslation(
                                                            TrKeys.save),
                                                    onPressed: () {
                                                      AddressInformation data =
                                                          AddressInformation(
                                                              house: house.text,
                                                              office:
                                                                  office.text,
                                                              floor:
                                                                  floor.text);
                                                      LocalStorage.instance
                                                          .setAddressInformation(
                                                              data);
                                                      Navigator.pop(context);
                                                      Navigator.pop(context);
                                                    }),
                                                16.verticalSpace,
                                                CustomButton(
                                                    borderColor: Style.black,
                                                    textColor: Style.black,
                                                    background:
                                                        Style.transparent,
                                                    title: AppHelpers
                                                        .getTranslation(
                                                            TrKeys.skip),
                                                    onPressed: () {
                                                      Navigator.pop(context);
                                                      Navigator.pop(context);
                                                    }),
                                              ],
                                            ),
                                          ));
                                    } else {
                                      AppHelpers.showCheckTopSnackBarInfo(
                                        context,
                                        AppHelpers.getTranslation(
                                            TrKeys.noDriverZone),
                                      );
                                    }
                                  }
                                },
                              ),
                            ),
                          ],
                        ),
                      )
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
