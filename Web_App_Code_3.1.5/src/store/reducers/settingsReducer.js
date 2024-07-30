
import { createSelector, createSlice } from "@reduxjs/toolkit";
import { store } from "../store";
import { apiCallBegan } from "../actions/apiActions";
import { getSettingsApi } from "../../utils/api";
import moment from "moment";

const initialState = {
    data: null,
    loading: false,
    lat: null,
    long: null,
    fcmtoken: "",
    systemTimezone: '',
    lastFetch: null
}

export const settingsSlice = createSlice({
    name: "settings",
    initialState,
    reducers: {
        settingsRequested: (settings) => {
            settings.loading = true;
        },
        settingsSuccess: (settings, action) => {
            settings.data = action.payload.data
            settings.loading = false;
            settings.lastFetch = Date.now()
            // token.data = action.payload.data
        },
        settingsFailed: (settings) => {
            settings.loading = false;
        },
        latlong: (settings, action) => {
            let { lat, long } = action.payload;
            settings.lat = lat
            settings.long = long
        },
        fcmToken: (settings, action) => {
            settings.fcmtoken = action.payload.data
        },
        systemTimezone: (settings, action) => {
            settings.systemTimezone = action.payload.data
        },

    }
})


export const { settingsRequested, settingsSuccess, settingsFailed, latlong, fcmToken, systemTimezone } = settingsSlice.actions;
export default settingsSlice.reducer;

// load websettings api call
export const laodSettingsApi = ({
    type = "",
    onSuccess = () => { },
    onError = () => { },
    onStart = () => { } }) => {
    const { lastFetch } = store.getState().settings;
    const diffInMinutes = moment().diff(moment(lastFetch), "minutes");
    // // If API data is fetched within last 10 minutes then don't call the API again
    if (diffInMinutes < process.env.NEXT_PUBLIC_LOAD_MIN) return false
    store.dispatch(apiCallBegan({
        ...getSettingsApi(type),
        displayToast: false,
        onStartDispatch: settingsRequested.type,
        onSuccessDispatch: settingsSuccess.type,
        onErrorDispatch: settingsFailed.type,
        onStart,
        onSuccess,
        onError
    }))
};

// load location
export const loadLocation = (lat, long) => {
    store.dispatch(latlong({ lat, long }))
}

export const locationData = createSelector(
    state => state.settings,
    settings => settings
)

// load fcmToken
export const loadFcmToken = (data) => {
    store.dispatch(fcmToken({ data }))
}
// load fcmToken
export const loadSystemTimezone = (data) => {
    store.dispatch(systemTimezone({ data }))
}
export const systemTimezoneData = createSelector(
    state => state.settings,
    settings => settings
)

// Selector Functions
export const settingsData = createSelector(
    state => state.settings,
    settings => settings?.data,
)
