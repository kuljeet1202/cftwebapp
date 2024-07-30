import Api from 'src/api/AxiosInterceptors'
import { GET_BOOKMARK, SET_BOOKMARK } from 'src/utils/api'

export const bookmarkApi = {
  getBookmark: requestData => {
    const { access_key, language_id, offset, limit } = requestData
    return Api.get(GET_BOOKMARK, {
      params: {
        access_key,
        language_id,
        offset,
        limit
      }
    })
  },
  setBookmark: requestData => {
    const { access_key, news_id, status } = requestData
    return Api.post(SET_BOOKMARK, {
      access_key,
      news_id,
      status //1-bookmark, 0-unbookmark
    })
  }
}
