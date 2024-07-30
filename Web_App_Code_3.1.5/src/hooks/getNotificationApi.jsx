import Api from 'src/api/AxiosInterceptors'
import { GET_NOTIFICATIONS, GET_USER_NOTIFICATION } from 'src/utils/api'

export const getNotificationsApi = {
  getNotifications: requestData => {
    const { access_key, offset, limit, language_id } = requestData
    return Api.get(GET_NOTIFICATIONS, {
      params: {
        access_key,
        offset,
        limit,
        language_id
      }
    })
  },
  getUserNotification: requestData => {
    const { access_key, offset, limit, user_id } = requestData
    return Api.get(GET_USER_NOTIFICATION, {
      params: {
        access_key,
        offset,
        limit,
        user_id
      }
    })
  }
}
