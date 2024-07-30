// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/data/models/NotificationModel.dart';
import 'package:news/ui/screens/Notification/Widgets/shimmerNotification.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/ui/widgets/errorContainerWidget.dart';
import 'package:news/utils/ErrorMessageKeys.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/app/routes.dart';
import 'package:news/cubits/NewsByIdCubit.dart';
import 'package:news/cubits/notificationCubit.dart';
import 'package:news/ui/widgets/circularProgressIndicator.dart';
import 'package:news/ui/widgets/networkImage.dart';

class NotificationList extends StatefulWidget {
  const NotificationList({super.key});

  @override
  State<StatefulWidget> createState() {
    return _NotificationState();
  }
}

class _NotificationState extends State<NotificationList> {
  List<String> selectedList = [];
  late final ScrollController controller = ScrollController()..addListener(hasMoreNotiScrollListener);

  bool loading = false;
  final int maxLinesTomatch = 2;

  List<bool> readMore = [false];

  @override
  void initState() {
    super.initState();
  }

  @override
  void dispose() {
    controller.dispose();
    super.dispose();
  }

  void refreshNotification() {
    context.read<NotificationCubit>().getNotification(context: context);
  }

  void hasMoreNotiScrollListener() {
    if (controller.position.maxScrollExtent == controller.offset) {
      if (context.read<NotificationCubit>().hasMoreNotification()) {
        context.read<NotificationCubit>().getMoreNotification(context: context);
      } else {
        debugPrint("No more notifications");
      }
    }
  }

  Widget notificationCell({required NotificationModel model}) {
    return Row(
      children: <Widget>[
        ClipRRect(
            borderRadius: BorderRadius.circular(5.0),
            child: (model.image != null && model.image! != "")
                ? CustomNetworkImage(networkImageUrl: model.image!, fit: BoxFit.cover, width: 80, height: 80, isVideo: false)
                : SvgPicture.asset(UiUtils.getSvgImagePath("placeholder"), height: 80.0, width: 80, fit: BoxFit.cover, bundle: DefaultAssetBundle.of(context))),
        Expanded(
            child: Padding(
          padding: const EdgeInsetsDirectional.only(start: 13.0, end: 8.0),
          child: LayoutBuilder(builder: (context, size) {
            //check for Message text overflow
            var span =
                TextSpan(text: model.message, style: Theme.of(context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.4), letterSpacing: 0.1));
            var tp = TextPainter(maxLines: maxLinesTomatch, textDirection: Directionality.of(context), text: span); // trigger it to layout
            tp.layout(maxWidth: size.maxWidth);
            // whether the text overflowed or not
            var exceeded = tp.didExceedMaxLines;
            return Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: <Widget>[
                CustomTextLabel(
                    text: model.title!,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    textStyle:
                        Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.bold, color: UiUtils.getColorScheme(context).primaryContainer, fontSize: 15.0, letterSpacing: 0.1)),
                Wrap(alignment: WrapAlignment.start, children: [
                  CustomTextLabel(
                      text: model.message!,
                      overflow: (!model.isReadMore) ? TextOverflow.ellipsis : null,
                      maxLines: (!model.isReadMore) ? maxLinesTomatch : null,
                      textStyle: Theme.of(context).textTheme.titleSmall?.copyWith(color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.4), letterSpacing: 0.1)),
                  (model.message != null && exceeded)
                      ? InkWell(
                          onTap: () {
                            model.isReadMore = !model.isReadMore;
                            setState(() {});
                          },
                          child: CustomTextLabel(
                              text: (model.isReadMore) ? UiUtils.getTranslatedLabel(context, 'readLessLbl') : UiUtils.getTranslatedLabel(context, 'readMoreLbl'),
                              textStyle: Theme.of(context).textTheme.bodySmall?.copyWith(fontWeight: FontWeight.w600, color: UiUtils.getColorScheme(context).primary.withOpacity(0.7))))
                      : const SizedBox.shrink()
                ]),
                Padding(
                    padding: const EdgeInsetsDirectional.only(top: 8.0),
                    child: CustomTextLabel(
                        text: UiUtils.convertToAgo(context, DateTime.parse(model.dateSent!), 2)!,
                        textStyle:
                            Theme.of(context).textTheme.bodySmall?.copyWith(fontWeight: FontWeight.normal, color: UiUtils.getColorScheme(context).primaryContainer.withOpacity(0.7), fontSize: 11)))
              ],
            );
          }),
        )),
        (model.newsId == null || model.newsId == "0") ? const SizedBox.shrink() : const Icon(Icons.arrow_circle_right_rounded)
      ],
    );
  }

  _buildNotiContainer({required NotificationModel model, required int index, required int totalCurrentNoti, required bool hasMoreNotiFetchError, required bool hasMore}) {
    if (index == totalCurrentNoti - 1 && index != 0) {
      if (hasMore) {
        if (hasMoreNotiFetchError) {
          return Center(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 15.0, vertical: 8.0),
              child: IconButton(
                  onPressed: () {
                    context.read<NotificationCubit>().getMoreNotification(context: context);
                  },
                  icon: Icon(Icons.error, color: Theme.of(context).primaryColor)),
            ),
          );
        } else {
          return Center(child: Padding(padding: const EdgeInsets.symmetric(horizontal: 15.0, vertical: 8.0), child: showCircularProgress(true, Theme.of(context).primaryColor)));
        }
      }
    }

    return Hero(
        tag: model.id!,
        child: Padding(
            padding: const EdgeInsetsDirectional.only(top: 5.0, bottom: 10.0),
            child: InkWell(
              child: Container(
                  padding: const EdgeInsets.all(10.0),
                  decoration: BoxDecoration(color: UiUtils.getColorScheme(context).background, borderRadius: BorderRadius.circular(10)),
                  child: notificationCell(model: model)),
              onTap: () {
                if (!loading && model.newsId != "0") {
                  context.read<NewsByIdCubit>().getNewsById(newsId: model.newsId!, langId: context.read<AppLocalizationCubit>().state.id).then((value) {
                    if (value.isNotEmpty) {
                      loading = true;
                      Navigator.of(context).pushNamed(Routes.newsDetails, arguments: {"model": value[0], "isFromBreak": false, "fromShowMore": false}).then((value) => loading = false);
                    }
                  });
                }
              },
            )));
  }

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<NotificationCubit, NotificationState>(
      builder: (context, state) {
        if (state is NotificationFetchSuccess) {
          return Padding(
            padding: const EdgeInsetsDirectional.only(start: 15.0, end: 15.0, top: 10.0, bottom: 10.0),
            child: RefreshIndicator(
              onRefresh: () async {
                refreshNotification();
              },
              child: ListView.builder(
                  controller: controller,
                  physics: const AlwaysScrollableScrollPhysics(),
                  shrinkWrap: true,
                  itemCount: state.notification.length,
                  itemBuilder: (context, index) {
                    return _buildNotiContainer(
                        model: state.notification[index], hasMore: state.hasMore, hasMoreNotiFetchError: state.hasMoreFetchError, index: index, totalCurrentNoti: state.notification.length);
                  }),
            ),
          );
        }
        if (state is NotificationFetchFailure) {
          return ErrorContainerWidget(
              errorMsg: UiUtils.getTranslatedLabel(context, (state.errorMessage.contains(ErrorMessageKeys.noInternet)) ? 'internetmsg' : 'notiNotAvail'), onRetry: refreshNotification);
        }
        //state is NotificationFetchInProgress || state is NotificationInitial
        return Padding(padding: const EdgeInsets.only(bottom: 10.0, left: 10.0, right: 10.0), child: shimmerNotification(context));
      },
    );
  }
}
