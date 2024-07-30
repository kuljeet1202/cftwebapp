// Import necessary modules
import { createSelector, createSlice } from '@reduxjs/toolkit'
import { store } from '../store'
import moment from 'moment'
import { apiCallBegan } from '../actions/apiActions'
import { getPages } from 'src/utils/api'

// Initial state with some default data
const initialState = {
    data: [],
    loading: false,
    Lang: null,
    lastFetch: null
}

// Create a Redux slice
export const morePagesSlice = createSlice({
    name: 'morePages',
    initialState,
    reducers: {
        morePagesRequested: (state, action) => {
            state.loading = true
        },
        morePagesSuccess: (state, action) => {
            state.loading = false
            state.data = action.payload.data
            state.lastFetch = Date.now()
            state.Lang = action.request
        },
        morePagesFailed: (state, action) => {
            state.loading = false
        }
    }
})

export const { morePagesRequested, morePagesSuccess, morePagesFailed } = morePagesSlice.actions
export default morePagesSlice.reducer

// API CALLS
export const loadMorePages = ({
    onSuccess = () => { },
    onError = () => { },
    onStart = () => { } }) => {

    store.dispatch(
        apiCallBegan({
            ...getPages(),
            displayToast: false,
            onStartDispatch: morePagesRequested.type,
            onSuccessDispatch: morePagesSuccess.type,
            onErrorDispatch: morePagesFailed.type,
            onStart,
            onSuccess,
            onError
        })
    )
}

export const getMorePagesData = createSelector(
    state => state.morePages,
    morePages => morePages.data
)
