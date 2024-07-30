import Api from 'src/api/AxiosInterceptors'
import { GET_BREAKING_NEWS, SET_BREAKING_NEWS_VIEW } from 'src/utils/api'

export const AllBreakingNewsApi = {
  getBreakingNews: requestData => {
    const { language_id, access_key,slug } = requestData
    return Api.get(GET_BREAKING_NEWS, {
      params: {
        language_id,
        access_key,
        slug
      }
    })
  },
  setBreakingNewsView: requestData => {
    const { access_key, user_id, breaking_news_id } = requestData
    return Api.post(SET_BREAKING_NEWS_VIEW, {
      access_key,
      user_id,
      breaking_news_id
    })
  }
}
