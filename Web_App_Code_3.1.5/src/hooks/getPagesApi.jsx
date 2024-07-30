import Api from 'src/api/AxiosInterceptors'
import { GET_PAGES } from 'src/utils/api'

export const getpagesApi = {
  getpages: requestData => {
    const { access_key, language_id,slug } = requestData
    return Api.get(GET_PAGES, {
      params: {
        access_key,
        language_id,
        slug,
      }
    })
  }
}
