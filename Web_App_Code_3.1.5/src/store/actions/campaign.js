import {
  accountdeleteApi,
  deleteCommentApi,
  getCategoriesApi,
  setBookmarkApi,
  setCommentApi,
  setLikeDisLikeApi,
  DeleteUserNotificationApi,
  setUserCategoriesApi,
  setnewsApi,
  deleteimageApi,
  deletenewsApi,
  getsubcategorybycategoryApi,
  set_comment_like_dislike_Api,
  set_flag_Api,
  register_Fcmtoken_Api,
  getUserByIdApi,
  getComments,
  setQuestionResult,
  getQuestionResult,
  getBreakingNews,
  getPages
} from '../../utils/api'
import { store } from '../store'
import { apiCallBegan } from './apiActions'

// 1. get categories
export const categoriesApi = ({
  offset = '',
  limit = '',
  language_id = '',
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...getCategoriesApi(offset, limit, language_id),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 2. set bookmark
export const setbookmarkApi = ({
  news_id = '',
  status = '',
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...setBookmarkApi(news_id, status),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 3. set comment
export const setcommentApi = ({
  parent_id = '',
  news_id = '',
  message = '',
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...setCommentApi(parent_id, news_id, message),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 4. delete comment
export const deletecommentApi = ({ comment_id = '', onSuccess = () => { }, onError = () => { }, onStart = () => { } }) => {
  store.dispatch(
    apiCallBegan({
      ...deleteCommentApi(comment_id),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 5. set likedislike
export const setlikedislikeApi = ({
  news_id = '',
  status = '',
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...setLikeDisLikeApi(news_id, status),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 6. delete user notification
export const deleteusernotificationApi = ({
  id = '',
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...DeleteUserNotificationApi(id),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 7. set user categories
export const setusercategoriesApi = ({
  category_id = '',
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...setUserCategoriesApi(category_id),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 9. set news
export const setNewsApi = async ({
  action_type = '',
  category_id = '',
  subcategory_id = '',
  tag_id = '',
  title = '',
  meta_title = '',
  meta_description = '',
  meta_keyword = '',
  slug = '',
  content_type = '',
  content_data = '',
  description = '',
  image = '',
  ofile = '',
  show_till = '',
  language_id = '',
  location_id = '',
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...setnewsApi(
        action_type,
        category_id,
        subcategory_id,
        tag_id,
        title,
        meta_title,
        meta_description,
        meta_keyword,
        slug,
        content_type,
        content_data,
        description,
        image,
        ofile,
        show_till,
        language_id,
        location_id
      ),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 10. delete new images
export const deleteImageApi = ({ image_id = '', onSuccess = () => { }, onError = () => { }, onStart = () => { } }) => {
  store.dispatch(
    apiCallBegan({
      ...deleteimageApi(image_id),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 11. delete news
export const deleteNewsApi = ({ news_id = '', onSuccess = () => { }, onError = () => { }, onStart = () => { } }) => {
  store.dispatch(
    apiCallBegan({
      ...deletenewsApi(news_id),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 12. subcategory by category
export const getSubcategoryByCategoryApi = ({
  category_id = '',
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...getsubcategorybycategoryApi(category_id),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 13. set comment like dislike
export const setCommentLikeDislikeApi = ({
  comment_id = '',
  status = '',
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...set_comment_like_dislike_Api(comment_id, status),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 14. set flag
export const setFlagApi = ({
  comment_id = '',
  news_id = '',
  message = '',
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...set_flag_Api(comment_id, news_id, message),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 16. delete account
export const accountDeleteApi = ({ onSuccess = () => { }, onError = () => { }, onStart = () => { } }) => {
  store.dispatch(
    apiCallBegan({
      ...accountdeleteApi(),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 17. register token
export const registerFcmTokenApi = async ({
  token = '',
  latitude = '',
  longitude = '',
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...register_Fcmtoken_Api(token, latitude, longitude),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}

// 18. get user by id
export const getuserbyidApi = ({
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { } }) => {
  store.dispatch(
    apiCallBegan({
      ...getUserByIdApi(),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}


// // GET PRODUCTS
export const GetCommentsApi = ({
  access_key = '',
  news_id = '',
  offset = '',
  limit = '',
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { } }) => {
  store.dispatch(
    apiCallBegan({
      ...getComments(access_key,
        news_id,
        offset,
        limit),
      displayToast: false,
      onStart,
      onSuccess,
      onError,
    })
  );
};


// 19. 
export const setQuestionResultApi = ({
  access_key = '',
  language_id = '',
  question_id = '',
  option_id = '',
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...setQuestionResult(access_key, language_id, question_id, option_id,),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}
export const getQuestionResultApi = ({
  access_key,
  language_id,
  question_id,
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...getQuestionResult(access_key,
        language_id,
        question_id,),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}
export const getBreakingNewsApi = ({
  language_id = "",
  slug = "",
  onSuccess = () => { },
  onError = () => { },
  onStart = () => { }
}) => {
  store.dispatch(
    apiCallBegan({
      ...getBreakingNews(language_id, slug),
      displayToast: false,
      onStart,
      onSuccess,
      onError
    })
  )
}
