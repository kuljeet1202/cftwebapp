import Api from 'src/api/AxiosInterceptors'
import { GET_LIVE_STREAMING } from 'src/utils/api'

export const getLiveStreamingApi = {
  getLiveStreaming: requestData => {
    const { access_key, language_id } = requestData
    return Api.get(GET_LIVE_STREAMING, {
      params: {
        access_key,
        language_id
      }
    })
  }
}
