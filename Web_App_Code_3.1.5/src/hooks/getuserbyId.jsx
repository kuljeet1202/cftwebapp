import Api from 'src/api/AxiosInterceptors'
import { GET_USER_BY_ID } from 'src/utils/api'

export const getUserByIdApi = {
  getUserById: requestData => {
    const { access_key, user_id } = requestData
    return Api.get(GET_USER_BY_ID, {
      params: {
        access_key,
        user_id
      }
    })
  }
}
