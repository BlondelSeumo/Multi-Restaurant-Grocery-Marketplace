import 'package:auto_route/auto_route.dart';
import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:osm_nominatim/osm_nominatim.dart';

import '../../components/text_fields/search_text_field.dart';
import '../../theme/app_style.dart';


class MapSearchPage extends StatefulWidget {
  const MapSearchPage({ Key? key}) : super(key: key);

  @override
  State<MapSearchPage> createState() => _MapSearchPageState();
}

class _MapSearchPageState extends State<MapSearchPage> {
  List<Place> searchResult = [];


  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: EdgeInsets.symmetric(horizontal: 16.w),
          child: Column(
            children: [
              16.verticalSpace,
              SearchTextField(
                autofocus: true,
                isBorder: true,
                onChanged: (title) async {
                  searchResult = await Nominatim.searchByName(
                    query: title,
                    limit: 15,
                    addressDetails: true,
                    extraTags: true,
                    nameDetails: true,
                  );
                  setState(() {});
                },
              ),
              Expanded(
                child: ListView.builder(
                    itemCount: searchResult.length,
                    padding: EdgeInsets.only(bottom: 22.h),
                    itemBuilder: (context, index) {
                  return InkWell(
                    onTap: (){
                      context.popRoute(searchResult[index]);
                    },
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        22.verticalSpace,
                        Text(searchResult[index].address?["country"] ?? "",style: Style.interNormal(size: 14),),
                        Text(searchResult[index].displayName ,style: Style.interNormal(size: 14),
                        maxLines: 2,overflow: TextOverflow.ellipsis,),
                        const Divider(color: Style.borderColor,),
                      ],
                    ),
                  );
                }),
              )
            ],
          ),
        ),
      ),
    );
  }
}
