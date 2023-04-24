import 'package:flutter/material.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:riverpodtemp/infrastructure/models/data/chat_message_data.dart';
import 'package:riverpodtemp/infrastructure/services/app_constants.dart';
import 'package:riverpodtemp/presentation/theme/app_style.dart';


class ChatItem extends StatelessWidget {
  final ChatMessageData chatData;

  const ChatItem({Key? key, required this.chatData}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      crossAxisAlignment: chatData.messageOwner == MessageOwner.partner
          ? CrossAxisAlignment.start
          : CrossAxisAlignment.end,
      children: [
        Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.only(
              topLeft: Radius.circular(25.r),
              topRight: Radius.circular(25.r),
              bottomLeft: Radius.circular(
                chatData.messageOwner == MessageOwner.partner ? 0 : 25.r,
              ),
              bottomRight: Radius.circular(
                chatData.messageOwner == MessageOwner.you ? 0 : 25.r,
              ),
            ),
            color: chatData.messageOwner == MessageOwner.you
                ? Style.brandGreen
                : Style.bgGrey,
          ),
          constraints: BoxConstraints(
            maxWidth: 256.r,
          ),
          padding: REdgeInsets.symmetric(
            horizontal: 20,
            vertical: 15,
          ),
          child: Text(
            chatData.message,
            style: GoogleFonts.k2d(
              fontWeight: FontWeight.w500,
              fontSize: 16.sp,
              color: Style.black,
              letterSpacing: -0.5,
            ),
          ),
        ),
        4.verticalSpace,
        Text(
          chatData.time,
          style: GoogleFonts.k2d(
            fontWeight: FontWeight.w500,
            fontSize: 10.sp,
            color: Style.black,
            letterSpacing: -0.5,
          ),
        ),
        35.verticalSpace,
      ],
    );
  }
}
