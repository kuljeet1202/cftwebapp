'use client'
import { useDispatch, useSelector } from 'react-redux'
import {
  loadLanguageLabels,
  selectCurrentLanguage,
  selectLanguages,
  setCurrentLanguage
} from '../../store/reducers/languageReducer'
import axios from 'axios'
import { SlCalender } from 'react-icons/sl'
import { HiArrowLongUp, HiArrowLongDown } from 'react-icons/hi2'
import { Dropdown } from 'react-bootstrap'
import { FaFacebookSquare, FaInstagram, FaLinkedin } from 'react-icons/fa'
import { FaSquareXTwitter } from "react-icons/fa6";

import { useQuery } from '@tanstack/react-query'
import Skeleton from 'react-loading-skeleton'
import { useEffect } from 'react'
import { loadLocation, locationData, settingsData } from 'src/store/reducers/settingsReducer'
import toast from 'react-hot-toast'
import { registerFcmTokenApi } from 'src/store/actions/campaign'
import { isLogin } from 'src/utils'
import { useRouter } from 'next/router'

const WeatherCard = () => {
  const currentLanguage = useSelector(selectCurrentLanguage)
  const getLocation = useSelector(settingsData)
  const location = useSelector(locationData)
  const getLocationData = getLocation?.location_news_mode
  const storedLatitude = location && location.lat
  const storedLongitude = location && location.long

  const dispatch = useDispatch()

  const router = useRouter()

  const weatherApi = async () => {
    return new Promise((resolve, reject) => {
      try {
        if ('geolocation' in navigator) {
          navigator.geolocation.getCurrentPosition(async position => {
            const { latitude, longitude } = position.coords

            if (getLocationData === '1') {
              loadLocation(latitude, longitude)
            } else {
              loadLocation(null, null)
            }

            const response = await axios.get(
              `https://api.weatherapi.com/v1/forecast.json?key=${process.env.NEXT_PUBLIC_WEATHER_API_KEY}&q=${latitude},${longitude}&days=1&aqi=no&alerts=no&lang=${currentLanguage.code}`
            )

            resolve(response.data) // Resolve the promise with the fetched data
          })
        } else {
          toast.error('Geolocation not supported')
        }
      } catch (error) {
        loadLocation(null, null)
        reject(error) // Reject the promise if an error occurs
      }
    })
  }

  // react query
  const { isLoading, data: weather } = useQuery({
    queryKey: ['weather', currentLanguage, getLocationData],
    queryFn: weatherApi,

  })

  // to get today weekday nameselectLanguages
  const today = new Date()

  const dayOfMonth = today.getDate() // returns the day of the month (1-31)
  // const month = today.getMonth() + 1;  // returns the month (0-11); add 1 to get the correct month
  const year = today.getFullYear() // returns the year (4 digits)

  const month = today.toLocaleString('default', { month: 'long' })

  // Assuming forecastData is an array of forecast objects

  const maxTempC = weather && weather.forecast.forecastday[0]?.day.maxtemp_c
  const minTempC = weather && weather.forecast.forecastday[0]?.day.mintemp_c

  const languagesData = useSelector(selectLanguages)

  // language change
  const languageChange = async (name, code, id, display_name) => {
    loadLanguageLabels({ code: code })
    setCurrentLanguage(name, code, id, display_name)
    if (isLogin()) {

      await registerFcmTokenApi({
        token: location.fcmtoken,
        latitude: storedLatitude,
        longitude: storedLongitude,
        onSuccess: async res => {
        },
        onError: async err => {
          console.log(err)
        }
      })
    }
  }

  // useEffect(() => {
  //   if(currentLanguage.code){
  //     loadLanguageLabels({ code: currentLanguage?.code })
  //   }
  // }, [currentLanguage])

  return (
    <div id='rns-weather-card'>
      <div id='weather-main-text' className='container'>
        <div className='row align-items-center'>
          <div className='col-md-6 col-12'>
            <div className='left-weather'>
              <div className='calender_icon me-2'>
                <p className=' mb-0'>
                  <SlCalender />
                  {`${dayOfMonth}`}
                  {`${month}`}
                  ,{`${year}`}
                </p>
              </div>
              {isLoading ? (
                <>
                  <Skeleton height={5} count={3} />
                </>
              ) : (
                weather && (
                  <>
                    <img src={weather && weather?.current?.condition?.icon} alt='weather news' className='weather_icon' />
                    <b className='me-2'>{weather && weather?.current?.temp_c}°C</b>
                    <div className='left-state'>
                      <p className='location-wcard mb-0 '>
                        {weather && weather?.location && weather?.location?.name},
                        {weather && weather?.location && weather?.location?.region},
                        {weather && weather?.location && weather?.location?.country}
                      </p>
                      <p className='day-Wtype-wcard mb-0 '>
                        <HiArrowLongUp />
                        {maxTempC}°C <HiArrowLongDown />
                        {minTempC}°C
                      </p>
                    </div>
                  </>
                )
              )}
            </div>
          </div>
          <div className='col-md-6 col-12'>
            <div className='right-weather'>
              {
                router.pathname === '/' ? <>
                  <ul className='language_section'>
                    <li>
                      <Dropdown>
                        <Dropdown.Toggle className='language_drop'>
                          {currentLanguage?.displayName ? currentLanguage?.displayName : currentLanguage?.name}
                        </Dropdown.Toggle>

                        <Dropdown.Menu style={{ backgroundColor: '#1A2E51' }}>
                          {languagesData &&
                            languagesData.map((data, index) => {
                              return (
                                <Dropdown.Item
                                  key={index}
                                  id='btnLogout'
                                  onClick={() => languageChange(data.language, data.code, data.id, data.display_name)}
                                >
                                  {data.display_name ? data.display_name : data.language}
                                </Dropdown.Item>
                              )
                            })}
                        </Dropdown.Menu>
                      </Dropdown>
                    </li>
                  </ul>
                  <div className='slash-line'></div>
                </>
                  : null
              }
              <div className='social_media_top'>
                {process.env.NEXT_PUBLIC_FACEBOOK ? (
                  <a
                    target='_blank'
                    id='social_platforms'
                    className='me-2'
                    href={process.env.NEXT_PUBLIC_FACEBOOK}
                    rel='noreferrer'
                  >
                    <FaFacebookSquare />
                  </a>
                ) : null}
                {process.env.NEXT_PUBLIC_INSTAGRAM ? (
                  <a
                    target='_blank'
                    id='social_platforms'
                    className='me-2'
                    href={process.env.NEXT_PUBLIC_INSTAGRAM}
                    rel='noreferrer'
                  >
                    <FaInstagram />
                  </a>
                ) : null}
                {process.env.NEXT_PUBLIC_LINKEDIN ? (
                  <a
                    target='_blank'
                    id='social_platforms'
                    className='me-2'
                    href={process.env.NEXT_PUBLIC_LINKEDIN}
                    rel='noreferrer'
                  >
                    <FaLinkedin />
                  </a>
                ) : null}
                {process.env.NEXT_PUBLIC_TWITTER ? (
                  <a
                    target='_blank'
                    id='social_platforms'
                    className=''
                    href={process.env.NEXT_PUBLIC_TWITTER}
                    rel='noreferrer'
                  >
                    <FaSquareXTwitter />
                  </a>
                ) : null}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default WeatherCard
