import Api from 'src/api/AxiosInterceptors'
import { GET_QUESTION } from 'src/utils/api'
import { GET_QUESTION_RESULT } from 'src/utils/api'

export const getQuestionApi = {
    getQuestion: requestData => {
        const { access_key, language_id, user_id } = requestData
        return Api.get(GET_QUESTION, {
            params: {
                access_key,
                language_id,
                user_id,
            }
        })
    },
    getQuestionResult: requestData => {
        const { access_key, language_id,question_id } = requestData
        return Api.get(GET_QUESTION_RESULT, {
            params: {
                access_key,
                language_id,
                question_id,
            }
        })
    }
}