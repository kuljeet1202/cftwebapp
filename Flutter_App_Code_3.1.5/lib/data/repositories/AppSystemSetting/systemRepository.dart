// ignore_for_file: file_names

import 'package:news/utils/api.dart';
import 'package:news/utils/strings.dart';

class SystemRepository {
  Future<dynamic> fetchSettings() async {
    try {
      final result = await Api.sendApiRequest(url: Api.getSettingApi, body: {}, isGet: true);
      return result[DATA];
    } catch (e) {
      throw ApiException(e.toString());
    }
  }
}
