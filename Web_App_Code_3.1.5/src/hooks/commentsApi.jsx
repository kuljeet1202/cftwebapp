import Api from 'src/api/AxiosInterceptors'
import { GET_COMMENT_BY_NEWS } from 'src/utils/api'

export const getCommentByNewsApi = {
  getCommentByNews: requestData => {
    const { access_key, offset, limit, news_id } = requestData
    return Api.get(GET_COMMENT_BY_NEWS, {
      params: {
        access_key,
        news_id,
        offset,
        limit
      }
    })
  }
}
