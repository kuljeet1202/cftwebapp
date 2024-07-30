import { createSelector, createSlice } from '@reduxjs/toolkit'
import moment from 'moment'
import { getNews } from 'src/utils/api'
import { apiCallBegan } from '../actions/apiActions'
import { store } from '../store'

const initialState = {
    data: {},
    loading: false,
    lastFetch: null,
    Lang: null,
}

export const newsSlice = createSlice({
    name: 'NewsData',
    initialState,
    reducers: {
        newsRequested: news => {
            news.loading = true
        },
        newsReceived: (news, action) => {
            news.data = action.payload.data
            news.loading = false
            news.lastFetch = Date.now()
            news.Lang = action.request
        },
        newsRequestFailed: news => {
            news.loading = true
        },
        newsUpdateLanguage: (news, action) => {
            // console.log(action.payload, 'langId-newsReducer')
            if (news.Lang) {
                news.Lang.language_id = action.payload
            }

        }
    }
})

export const { newsRequested, newsReceived, newsRequestFailed, newsUpdateLanguage } = newsSlice.actions
export default newsSlice.reducer

// API Calls
export const loadNews = ({
    access_key = "",
    offset = "",
    limit = "",
    id = "",
    get_user_news = "",
    search = "", // {optional}
    category_id = "",
    category_slug = "",
    subcategory_id = "",
    subcategory_slug = "",
    slug = "",
    tag_id = "",
    latitude = "",
    longitude = "", 
    onSuccess = () => { }, 
    onError = () => { }, 
    onStart = () => { } }) => {
    const state = store.getState()
    const { currentLanguage } = store?.getState().languages
    const { lastFetch, Lang } = state.NewsData
    // console.log(Lang?.language_id, 'news-langId')
    // console.log(lastFetch, 'lastfetch')

    // console.log('currentLanguage = ', currentLanguage?.id, 'Lang =', Lang?.language_id)
    const diffInMinutes = moment().diff(moment(lastFetch), 'minutes')
    // if ((currentLanguage?.id != Lang?.language_id) || diffInMinutes > 10) {
        store.dispatch(
            apiCallBegan({
                ...getNews(access_key,
                    offset,
                    limit,
                    id,
                    get_user_news,
                    search, // {optional}
                    category_id,
                    category_slug,
                    subcategory_id,
                    subcategory_slug,
                    slug,
                    tag_id,
                    latitude,
                    longitude),
                displayToast: false,
                onStartDispatch: newsRequested.type,
                onSuccessDispatch: newsReceived.type,
                onErrorDispatch: newsRequestFailed.type,
                onStart,
                onSuccess,
                onError
            })
        )
    }
// }

// Selector Functions
export const selectNewsData = createSelector(
    state => state.NewsData,
    NewsData => NewsData
)
