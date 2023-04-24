import 'dart:io';
import 'package:auto_route/auto_route.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter_remix/flutter_remix.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:intl/intl.dart' as intl;
import 'package:riverpodtemp/application/edit_profile/edit_profile_provider.dart';
import 'package:riverpodtemp/application/generate_image/generate_image_provider.dart';
import 'package:riverpodtemp/application/profile/profile_provider.dart';
import 'package:riverpodtemp/infrastructure/models/models.dart';
import 'package:riverpodtemp/infrastructure/services/app_helpers.dart';
import 'package:riverpodtemp/infrastructure/services/local_storage.dart';
import 'package:riverpodtemp/infrastructure/services/tr_keys.dart';
import 'package:riverpodtemp/presentation/components/buttons/custom_button.dart';
import 'package:riverpodtemp/presentation/components/custom_network_image.dart';
import 'package:riverpodtemp/presentation/components/keyboard_dismisser.dart';
import 'package:riverpodtemp/presentation/components/loading.dart';
import 'package:riverpodtemp/presentation/components/text_fields/outline_bordered_text_field.dart';
import 'package:riverpodtemp/presentation/components/title_icon.dart';
import 'package:riverpodtemp/presentation/routes/app_router.gr.dart';
import 'package:riverpodtemp/presentation/theme/theme.dart';

class EditProfileScreen extends ConsumerStatefulWidget {
  const EditProfileScreen({Key? key}) : super(key: key);

  @override
  ConsumerState<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends ConsumerState<EditProfileScreen> {
  final formKey = GlobalKey<FormState>();
  late TextEditingController birthDay;

  @override
  void initState() {
    birthDay = TextEditingController(
        text: intl.DateFormat("yyyy-MM-dd").format(DateTime.tryParse(
                ref.read(profileProvider).userData?.birthday ?? "") ??
            DateTime.now()));
    WidgetsBinding.instance.addPostFrameCallback((timeStamp) {
      ref
          .read(editProfileProvider.notifier)
          .setPhone(ref.read(profileProvider).userData?.phone ?? "");
      ref.read(editProfileProvider.notifier).setBirth(
          intl.DateFormat("yyyy-MM-dd").format(DateTime.tryParse(
                  ref.read(profileProvider).userData?.birthday ?? "") ??
              DateTime.now()));
    });
    super.initState();
  }

  @override
  void dispose() {
    birthDay.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final bool isLtr = LocalStorage.instance.getLangLtr();
    final event = ref.read(editProfileProvider.notifier);
    final user = ref.watch(profileProvider).userData;
    final state = ref.watch(editProfileProvider);
    ref.listen(editProfileProvider, (previous, next) {
      if (next.isSuccess && (previous?.isSuccess ?? false) != next.isSuccess) {
        ref
            .read(profileProvider.notifier)
            .setUser(next.userData ?? ProfileData());
      }
    });
    return Directionality(
      textDirection: isLtr ? TextDirection.ltr : TextDirection.rtl,
      child: KeyboardDismisser(
        child: Container(
          margin: MediaQuery.of(context).viewInsets,
          decoration: BoxDecoration(
              color: Style.bgGrey.withOpacity(0.96),
              borderRadius: BorderRadius.only(
                topLeft: Radius.circular(16.r),
                topRight: Radius.circular(16.r),
              )),
          width: double.infinity,
          child: state.isLoading
              ? const Loading()
              : Padding(
                  padding: EdgeInsets.symmetric(horizontal: 16.w),
                  child: SingleChildScrollView(
                    child: Form(
                      key: formKey,
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            mainAxisSize: MainAxisSize.max,
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              8.verticalSpace,
                              Center(
                                child: Container(
                                  height: 4.h,
                                  width: 48.w,
                                  decoration: BoxDecoration(
                                      color: Style.dragElement,
                                      borderRadius: BorderRadius.all(
                                          Radius.circular(40.r))),
                                ),
                              ),
                              24.verticalSpace,
                              TitleAndIcon(
                                title: AppHelpers.getTranslation(
                                    TrKeys.profileSettings),
                                paddingHorizontalSize: 0,
                                titleSize: 18,
                              ),
                              24.verticalSpace,
                              Stack(
                                children: [
                                  Container(
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(42.r),
                                      color: Style.shimmerBase,
                                    ),
                                    child: ClipRRect(
                                      borderRadius: BorderRadius.circular(42.r),
                                      child:
                                          ((user?.img?.isNotEmpty ?? false) &&
                                                  state.imagePath.isEmpty)
                                              ? CustomNetworkImage(
                                                  url: user!.img ?? "",
                                                  height: 84.r,
                                                  width: 84.r,
                                                  radius: 42.r)
                                              : state.imagePath.isNotEmpty
                                                  ? Image.file(
                                                      File(state.imagePath),
                                                      width: 84.r,
                                                      height: 84.r,
                                                    )
                                                  : CustomNetworkImage(
                                                      url: state.url,
                                                      height: 84.r,
                                                      width: 84.r,
                                                      radius: 42.r),
                                    ),
                                  ),
                                  Padding(
                                    padding:
                                        EdgeInsets.only(top: 56.h, left: 50.w),
                                    child: GestureDetector(
                                      onTap: () {
                                        AppHelpers.showAlertDialog(
                                            context: context,
                                            child: Container(
                                              decoration: BoxDecoration(
                                                color: Style.white,
                                                borderRadius:
                                                    BorderRadius.circular(16.r),
                                              ),
                                              padding: EdgeInsets.symmetric(
                                                  horizontal: 6.r,
                                                  vertical: 8.r),
                                              child: Column(
                                                mainAxisSize: MainAxisSize.min,
                                                crossAxisAlignment:
                                                    CrossAxisAlignment.start,
                                                children: [
                                                  TitleAndIcon(
                                                      title: AppHelpers
                                                          .getTranslation(
                                                              TrKeys.select)),
                                                  24.verticalSpace,
                                                  CustomButton(
                                                      title: AppHelpers
                                                          .getTranslation(TrKeys
                                                              .generateImageWithChatGPT),
                                                      onPressed: () async {
                                                        context.popRoute();
                                                        ref.refresh(
                                                            generateImageProvider);
                                                        final photoUrl =
                                                            await context.pushRoute(
                                                                const GenerateImageRoute());

                                                        event.getPhotoWithUrl(
                                                            photoUrl as String);
                                                      }),
                                                  16.verticalSpace,
                                                  CustomButton(
                                                      title: AppHelpers
                                                          .getTranslation(
                                                              TrKeys.gallery),
                                                      onPressed: () {
                                                        context.popRoute();
                                                        event.getPhoto();
                                                      })
                                                ],
                                              ),
                                            ));
                                      },
                                      child: Container(
                                        width: 38.w,
                                        height: 38.h,
                                        decoration: BoxDecoration(
                                            color: Style.white,
                                            shape: BoxShape.circle,
                                            border: Border.all(
                                                color: Style.borderColor)),
                                        child: const Icon(
                                            FlutterRemix.pencil_line),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              24.verticalSpace,
                              OutlinedBorderTextField(
                                readOnly: user?.email != null,
                                label: AppHelpers.getTranslation(TrKeys.email)
                                    .toUpperCase(),
                                initialText: user?.email ?? "",
                                validation: (s) {
                                  if (s?.isNotEmpty ?? false) {
                                    return null;
                                  }
                                  return AppHelpers.getTranslation(
                                      TrKeys.canNotBeEmpty);
                                },
                                onChanged: (s) {
                                  event.setEmail(s);
                                },
                              ),
                              34.verticalSpace,
                              Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  SizedBox(
                                    width: (MediaQuery.of(context).size.width -
                                            40) /
                                        2,
                                    child: OutlinedBorderTextField(
                                      label: AppHelpers.getTranslation(
                                              TrKeys.firstname)
                                          .toUpperCase(),
                                      initialText: user?.firstname ?? "",
                                      validation: (s) {
                                        if (s?.isNotEmpty ?? false) {
                                          return null;
                                        }
                                        return AppHelpers.getTranslation(
                                            TrKeys.canNotBeEmpty);
                                      },
                                      onChanged: (s) {
                                        event.setFirstName(s);
                                      },
                                    ),
                                  ),
                                  SizedBox(
                                    width: (MediaQuery.of(context).size.width -
                                            40) /
                                        2,
                                    child: OutlinedBorderTextField(
                                      label: AppHelpers.getTranslation(
                                              TrKeys.surname)
                                          .toUpperCase(),
                                      initialText: user?.lastname ?? "",
                                      validation: (s) {
                                        if (s?.isNotEmpty ?? false) {
                                          return null;
                                        }
                                        return AppHelpers.getTranslation(
                                            TrKeys.canNotBeEmpty);
                                      },
                                      onChanged: (s) {
                                        event.setLastName(s);
                                      },
                                    ),
                                  ),
                                ],
                              ),
                              34.verticalSpace,
                              Row(
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  SizedBox(
                                    width: (MediaQuery.of(context).size.width -
                                            40) /
                                        2,
                                    child: OutlinedBorderTextField(
                                      readOnly: user?.phone != null,
                                      label: AppHelpers.getTranslation(
                                              TrKeys.alternativeNumber)
                                          .toUpperCase(),
                                      hint: "+1 990 000 00 00",
                                      initialText: user?.phone ?? "",
                                      validation: (s) {
                                        if (s?.isNotEmpty ?? false) {
                                          return null;
                                        }
                                        return AppHelpers.getTranslation(
                                            TrKeys.canNotBeEmpty);
                                      },
                                      onChanged: (s) {
                                        event.setPhone(s);
                                      },
                                    ),
                                  ),
                                  SizedBox(
                                    width: (MediaQuery.of(context).size.width -
                                            40) /
                                        2,
                                    child: OutlinedBorderTextField(
                                      label: AppHelpers.getTranslation(
                                              TrKeys.mobileNumber)
                                          .toUpperCase(),
                                      hint: "+1 990 000 00 00",
                                      initialText: user?.secondPhone ?? "",
                                      onChanged: (s) {
                                        event.setSecondPhone(s);
                                      },
                                    ),
                                  ),
                                ],
                              ),
                              34.verticalSpace,
                              OutlinedBorderTextField(
                                onTap: () {
                                  AppHelpers.showCustomModalBottomSheet(
                                      context: context,
                                      modal: Container(
                                        height: 250.h,
                                        padding:
                                            const EdgeInsets.only(top: 6.0),
                                        margin: EdgeInsets.only(
                                          bottom: MediaQuery.of(context)
                                              .viewInsets
                                              .bottom,
                                        ),
                                        color: CupertinoColors.systemBackground
                                            .resolveFrom(context),
                                        child: SafeArea(
                                          top: false,
                                          child: CupertinoDatePicker(
                                            initialDateTime: DateTime.tryParse(
                                                    birthDay.text) ??
                                                DateTime.now(),
                                            maximumDate: DateTime.now(),
                                            mode: CupertinoDatePickerMode.date,
                                            use24hFormat: true,
                                            onDateTimeChanged:
                                                (DateTime newDate) {
                                              birthDay.text =
                                                  intl.DateFormat("yyyy-MM-dd")
                                                      .format(newDate);
                                              event
                                                  .setBirth(newDate.toString());
                                            },
                                          ),
                                        ),
                                      ),
                                      isDarkMode: false);
                                },
                                readOnly: true,
                                label: AppHelpers.getTranslation(
                                        TrKeys.dateOfBirth)
                                    .toUpperCase(),
                                hint: "YYYY-MM-DD",
                                validation: (s) {
                                  if (s?.isNotEmpty ?? false) {
                                    return null;
                                  }
                                  return AppHelpers.getTranslation(
                                      TrKeys.canNotBeEmpty);
                                },
                                textController: birthDay,
                              ),
                              34.verticalSpace,
                              OutlinedBorderTextField(
                                label: AppHelpers.getTranslation(TrKeys.gender)
                                    .toUpperCase(),
                                hint:
                                    AppHelpers.getTranslation(TrKeys.typeHere),
                                initialText: user?.gender ?? "",
                                validation: (s) {
                                  if (s?.isNotEmpty ?? false) {
                                    return null;
                                  }
                                  return AppHelpers.getTranslation(
                                      TrKeys.canNotBeEmpty);
                                },
                                onChanged: (s) {
                                  event.setGender(s);
                                },
                              ),
                            ],
                          ),
                          Padding(
                            padding: EdgeInsets.only(
                                bottom: MediaQuery.of(context).padding.bottom +
                                    24.h,
                                top: 24.h),
                            child: CustomButton(
                              title: AppHelpers.getTranslation(TrKeys.save),
                              onPressed: () {
                                if (formKey.currentState?.validate() ?? false) {
                                  event.editProfile(context, user!);
                                }
                              },
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
        ),
      ),
    );
  }
}
