import { createSelector, createSlice } from '@reduxjs/toolkit'
import moment from 'moment'
import { getFeatureSection } from 'src/utils/api'
import { apiCallBegan } from '../actions/apiActions'
import { store } from '../store'

const initialState = {
  loading: false,
  lastFetch: null,
  Lang: null
}

export const featureLayoutSlice = createSlice({
  name: 'Layouts',
  initialState,
  reducers: {
    layoutRequested: layout => {
      layout.loading = true
    },
    layoutReceived: (layout, action) => {
      layout.loading = false
      layout.lastFetch = Date.now()
      layout.Lang = action.request
    },
    layoutRequestFailed: layout => {
      layout.loading = true
    },
    layoutUpdateLanguage: (layout, action) => {
      if (layout.Lang) {
        layout.Lang.language_id = action.payload
      }
    },
    
  }
})

export const { layoutRequested, layoutReceived, layoutRequestFailed, layoutUpdateLanguage } = featureLayoutSlice.actions
export default featureLayoutSlice.reducer

// API Calls
export const loadLayout = ({
  access_key = "",
  offset = "",
  limit = "",
  slug = "",
  latitude = "",
  longitude = "",
  section_id = "",
  onSuccess = () => {},
  onError = () => {},
  onStart = () => {}
}) => {
  const state = store.getState()
  const { currentLanguage } = store.getState().languages
  const { lastFetch, Lang } = state.Layouts
  const diffInMinutes = moment().diff(moment(lastFetch), "minutes");
  // if (currentLanguage?.id != Lang?.language_id || diffInMinutes > process.env.NEXT_PUBLIC_LOAD_MIN) {
    store.dispatch(
      apiCallBegan({
        ...getFeatureSection(access_key, offset, limit, slug, latitude, longitude, section_id),
        displayToast: false,
        onStartDispatch: layoutRequested.type,
        onSuccessDispatch: layoutReceived.type,
        onErrorDispatch: layoutRequestFailed.type,
        onStart,
        onSuccess,
        onError
      })
    )
  }
// }

// Selector Functions
export const selectLayout = createSelector(
  state => state.Layouts,
  Layouts => Layouts
)
