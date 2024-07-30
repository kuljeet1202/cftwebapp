import Api from 'src/api/AxiosInterceptors'
import { GET_TAG } from 'src/utils/api'

export const getTagApi = {
  getTag: requestData => {
    const { access_key, language_id,slug } = requestData
    return Api.get(GET_TAG, {
      params: {
        access_key,
        language_id,
        slug
      }
    })
  },
}
