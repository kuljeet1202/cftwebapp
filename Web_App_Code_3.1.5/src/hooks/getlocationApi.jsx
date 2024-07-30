import Api from 'src/api/AxiosInterceptors'
import { GET_LOCATION } from 'src/utils/api'

export const getlocationapi = {
  getlocation: requestData => {
    const { access_key,limit} = requestData
    return Api.get(GET_LOCATION, {
      params: {
        access_key,
        limit: limit,
      }
    })
  }
}
