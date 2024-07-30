import Api from 'src/api/AxiosInterceptors'
import { GET_AD_SPACE_NEWS_DETAILS } from 'src/utils/api'

export const getAdsSpaceNewsDetailsApi = {
  getAdsSpaceNewsDetails: requestData => {
    const { access_key, language_id } = requestData
    return Api.get(GET_AD_SPACE_NEWS_DETAILS, {
      params: {
        access_key,
        language_id
      }
    })
  }
}
