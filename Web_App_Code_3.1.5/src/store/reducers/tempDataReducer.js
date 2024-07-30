// Import necessary modules
import { createSelector, createSlice } from '@reduxjs/toolkit'
import { store } from '../store'

// Initial state with some default data
const initialState = {
  data: {
    categoryCount: 0 ,// Set the default value as needed
    subCategories: []
  }
}

// Create a Redux slice
export const tempdataSlice = createSlice({
  name: 'tempdata',
  initialState,
  reducers: {
    categoryCount: (state, action) => {
      const { categoryCount } = action.payload.data
      state.data.categoryCount = categoryCount
      // console.log("In reducer-> ",action.payload.data)
    },
    setSubCategories: (state, action) => {
      const { subCategories  } = action.payload.data
      // console.log("In reducer-> ",action.payload.data)
      state.data.subCategories = subCategories
    }
  }
})

// Export the categoryCount reducer and action
export const { categoryCount,setSubCategories } = tempdataSlice.actions
export default tempdataSlice.reducer

// Function to load category count data
export const loadCategoryCount = data => {
  store.dispatch(categoryCount({ data }))
}
export const loadSubCategories = data => {
  store.dispatch(setSubCategories({ data }))
}

// Selector function to get tempdata from the state
export const tempdataSelector = state => state.tempdata

// Selector function to get categoryCount from the state
export const categoryCountSelector = createSelector(tempdataSelector, tempdata => tempdata.data.categoryCount)
export const subCategorySelector = createSelector(tempdataSelector, tempdata => tempdata.data.subCategories)
