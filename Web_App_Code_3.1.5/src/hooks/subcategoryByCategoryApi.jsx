import Api from 'src/api/AxiosInterceptors'
import { GET_SUBCATEGORY_BY_CATEGORY } from 'src/utils/api'

export const getsubcategorybycategoryApi = {
  getsubcategorybycategory: requestData => {
    const { access_key, category_id, language_id } = requestData
    return Api.get(GET_SUBCATEGORY_BY_CATEGORY, {
      params: {
        access_key,
        category_id,
        language_id
      }
    })
  }
}
