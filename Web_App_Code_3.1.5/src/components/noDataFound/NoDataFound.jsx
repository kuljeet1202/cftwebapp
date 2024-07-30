"use client"
import { translate } from "src/utils"

const NoDataFound = () => {
  return (
    <div className='no_data_found text-center my-5'>{translate('nodatafound')}</div>
  )
}

export default NoDataFound