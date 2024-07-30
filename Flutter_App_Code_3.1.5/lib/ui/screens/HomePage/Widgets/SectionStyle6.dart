// ignore_for_file: file_names

import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:news/app/routes.dart';
import 'package:news/cubits/appLocalizationCubit.dart';
import 'package:news/cubits/sectionByIdCubit.dart';
import 'package:news/data/models/BreakingNewsModel.dart';
import 'package:news/data/models/FeatureSectionModel.dart';
import 'package:news/data/models/NewsModel.dart';
import 'package:news/ui/widgets/customTextLabel.dart';
import 'package:news/utils/uiUtils.dart';
import 'package:news/ui/styles/colors.dart';
import 'package:news/ui/widgets/networkImage.dart';

class Style6Section extends StatefulWidget {
  final FeatureSectionModel model;

  const Style6Section({super.key, required this.model});

  @override
  Style6SectionState createState() => Style6SectionState();
}

class Style6SectionState extends State<Style6Section> {
  int? style6Sel;
  bool isNews = true;
  late final ScrollController style6ScrollController = ScrollController()..addListener(hasMoreBookmarkScrollListener);
  late BreakingNewsModel breakingNewsData;
  late NewsModel newsData;

  @override
  void initState() {
    super.initState();
  }

  @override
  void dispose() {
    style6ScrollController.removeListener(() {});
    super.dispose();
  }

  void hasMoreBookmarkScrollListener() {
    if (style6ScrollController.position.maxScrollExtent == style6ScrollController.offset) {
      if (context.read<SectionByIdCubit>().hasMoreSections()) {
        context.read<SectionByIdCubit>().getMoreSectionById(langId: context.read<AppLocalizationCubit>().state.id, sectionId: widget.model.id!);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return style6Data(widget.model);
  }

  Widget style6Data(FeatureSectionModel model) {
    return (model.breakVideos!.isNotEmpty || model.breakNews!.isNotEmpty || model.videos!.isNotEmpty || model.news!.isNotEmpty)
        ? Padding(padding: const EdgeInsets.symmetric(vertical: 5), child: style6NewsDetails(model))
        : const SizedBox.shrink();
  }

  Widget style6NewsDetails(FeatureSectionModel model) {
    if (model.newsType == 'news' || model.videosType == "news" || model.newsType == "user_choice") {
      if ((model.newsType == 'news' || model.newsType == "user_choice") ? model.news!.isNotEmpty : model.videos!.isNotEmpty) {
        isNews = true;
      }
    }
    if (model.newsType == 'breaking_news' || model.videosType == "breaking_news") {
      if (model.newsType == 'breaking_news' ? model.breakNews!.isNotEmpty : model.breakVideos!.isNotEmpty) {
        isNews = false;
      }
    }
    return SizedBox(
        height: MediaQuery.of(context).size.height / 2.8,
        child: SingleChildScrollView(
            controller: style6ScrollController,
            scrollDirection: Axis.horizontal,
            child: Row(
                children: List.generate(
                    (isNews)
                        ? ((model.newsType == 'news' || model.newsType == "user_choice") ? model.news!.length : model.videos!.length)
                        : ((model.newsType == 'breaking_news') ? model.breakNews!.length : model.breakVideos!.length), (index) {
              if (!(isNews)) breakingNewsData = (model.newsType == 'breaking_news') ? model.breakNews![index] : model.breakVideos![index];
              if ((isNews)) newsData = (model.newsType == 'news' || model.newsType == "user_choice") ? model.news![index] : model.videos![index];

              return SizedBox(
                  width: 180,
                  child: Padding(
                      padding: const EdgeInsets.all(5),
                      child: (isNews)
                          ? setImageCard(index: index, model: model, newsModel: newsData, allNewsList: model.news)
                          : setImageCard(index: index, model: model, breakingNewsModel: breakingNewsData, breakingNewsList: model.breakNews)));
            }))));
  }

  Widget setImageCard(
      {required int index, required FeatureSectionModel model, NewsModel? newsModel, BreakingNewsModel? breakingNewsModel, List<NewsModel>? allNewsList, List<BreakingNewsModel>? breakingNewsList}) {
    return InkWell(
      child: Stack(children: [
        ClipRRect(
            borderRadius: BorderRadius.circular(5),
            child: ShaderMask(
                shaderCallback: (bounds) {
                  return LinearGradient(begin: Alignment.topCenter, end: Alignment.bottomCenter, colors: [Colors.transparent, darkSecondaryColor.withOpacity(0.9)]).createShader(bounds);
                },
                blendMode: BlendMode.darken,
                child: CustomNetworkImage(
                    networkImageUrl: (newsModel != null) ? newsModel.image! : breakingNewsModel!.image!,
                    height: MediaQuery.of(context).size.height / 2.5,
                    width: MediaQuery.of(context).size.width / 1.9,
                    fit: BoxFit.cover,
                    isVideo: model.newsType == 'videos' ? true : false))),
        (newsModel != null && newsModel.categoryName != null)
            ? Align(
                alignment: Alignment.topLeft,
                child: Container(
                    margin: const EdgeInsetsDirectional.only(start: 7.0, top: 7.0),
                    height: 20.0,
                    padding: const EdgeInsetsDirectional.only(start: 6.0, end: 6.0, top: 2.5),
                    decoration: BoxDecoration(borderRadius: BorderRadius.circular(3), color: Theme.of(context).primaryColor),
                    child: CustomTextLabel(
                        text: newsModel.categoryName!,
                        textAlign: TextAlign.center,
                        textStyle: Theme.of(context).textTheme.bodySmall?.copyWith(color: secondaryColor),
                        overflow: TextOverflow.ellipsis,
                        softWrap: true)))
            : const SizedBox.shrink(),
        if (model.newsType == 'videos')
          Positioned.directional(
              textDirection: Directionality.of(context),
              top: MediaQuery.of(context).size.height * 0.13,
              start: MediaQuery.of(context).size.width / 6,
              end: MediaQuery.of(context).size.width / 6,
              child: UiUtils.setPlayButton(context: context)),
        Positioned.directional(
          textDirection: Directionality.of(context),
          bottom: 5,
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            (newsModel != null)
                ? Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 15),
                    child: CustomTextLabel(text: UiUtils.convertToAgo(context, DateTime.parse(newsModel.date!), 3)!, textStyle: Theme.of(context).textTheme.labelSmall!.copyWith(color: Colors.white)))
                : const SizedBox.shrink(),
            Container(
                padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 5),
                width: MediaQuery.of(context).size.width * 0.50,
                child: CustomTextLabel(
                    text: (newsModel != null) ? newsModel.title! : breakingNewsModel!.title!,
                    textStyle: Theme.of(context).textTheme.titleSmall!.copyWith(color: secondaryColor),
                    softWrap: true,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis))
          ]),
        ),
      ]),
      onTap: () {
        if (model.newsType == 'videos') {
          Navigator.of(context)
              .pushNamed(Routes.newsVideo, arguments: {"from": (newsModel != null) ? 1 : 3, if (newsModel != null) "model": newsModel, if (breakingNewsModel != null) "breakModel": breakingNewsModel});
        } else if (model.newsType == 'news' || model.newsType == "user_choice") {
          if (allNewsList != null && allNewsList.isNotEmpty) {
            List<NewsModel> newsList = List.from(allNewsList);
            newsList.removeAt(index);
            Navigator.of(context).pushNamed(Routes.newsDetails, arguments: {"model": newsModel, "newsList": newsList, "isFromBreak": false, "fromShowMore": false});
          }
        } else {
          if (breakingNewsList != null && breakingNewsList.isNotEmpty) {
            List<BreakingNewsModel> breakList = List.from(breakingNewsList);
            breakList.removeAt(index);
            Navigator.of(context).pushNamed(Routes.newsDetails, arguments: {"breakModel": breakingNewsModel, "breakNewsList": breakList, "isFromBreak": true, "fromShowMore": false});
          }
        }
      },
    );
  }
}
