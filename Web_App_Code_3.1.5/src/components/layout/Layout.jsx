'use client'
import { useEffect } from 'react'
import { laodSettingsApi, settingsData, loadSystemTimezone } from 'src/store/reducers/settingsReducer'
import { useDispatch, useSelector } from 'react-redux'
import { selectCurrentLanguage, selectCurrentLanguageLabels } from 'src/store/reducers/languageReducer'
import { useRouter } from 'next/router'
import Header from './Header'
import CatNav from '../categories/CatNav'
import WeatherCard from '../weather/WeatherCard'
import SearchPopup from '../search/SearchPopup'
import Footer from './Footer'
import { protectedRoutes } from 'src/routes/routes'
import toast from 'react-hot-toast'
import { loadGetUserByIdApi, selectUser } from 'src/store/reducers/userReducer'
import { usePathname } from 'next/navigation'
import { categoriesUpdateLanguage, loadCategories } from 'src/store/reducers/CatNavReducers'

const Layout = ({ children }) => {
  const settings = useSelector(settingsData)
  const userData = useSelector(selectUser)
  const router = useRouter()
  const pathname = usePathname()

  const dispatch = useDispatch()


  useSelector(selectCurrentLanguageLabels)
  const currentLanguage = useSelector(selectCurrentLanguage)
  // web settings load
  useEffect(() => {
    laodSettingsApi({
      onSuccess: res => {

      },
      onError: error => {
        console.log(error)
      }
    })
    // language laod
  }, [])

  // client side rendering route get and this is only for vercel deploy logic
  useEffect(() => {
    // Check if the slug is present in the URL
    if (process.env.NEXT_PUBLIC_SEO === 'false') {
      if (router.pathname) {
        router.replace(window.location.pathname + window.location.search)
      }
    }
  }, [])

  useEffect(() => {
    document.documentElement.style.setProperty('--primary-color', settings && settings?.web_setting?.web_color_code)
  }, [settings])

  // Check if the user is authenticated based on the presence of the token
  const isAuthenticated = userData && userData?.data?.token

  // Check if the current route requires authentication
  const requiresAuth = protectedRoutes.includes(pathname)

  useEffect(() => {
    authCheck()
  }, [requiresAuth])

  const authCheck = () => {
    if (requiresAuth) {
      if (isAuthenticated === undefined) {
        router.push('/')
        toast.error('please login first')
        return
      }
    }
  }
  useEffect(() => {
    if (currentLanguage?.id) {
      loadCategories({
        offset: "0",
        limit: "15",
        language_id: currentLanguage?.id,
        onSuccess: (res) => {
          dispatch(categoriesUpdateLanguage(currentLanguage.id))
        },
        onErro: (err) => {
          console.log("error", err)
          dispatch(categoriesUpdateLanguage(""))
        }
      })

    }
  }, [currentLanguage])

  const GetUserByIdFetchData = () => {
    if (!userData.data?.firebase_id) return false
    loadGetUserByIdApi({
      onSuccess: (res) => {
        // console.log(res)
        const data = res
        if (data && data.data.status === 0) {
          toast.error('You are deactivated by admin!')
          signOut(authentication)
            .then(() => {
              logoutUser()
              navigate.push('/')
            })
            .catch(error => {
              toast.error(error)
            })
          return false
        }

        if (data && data.data) {
          const roles = data.data.role
          if (roles !== 0) {
            // setisuserRole(true)
          }
          return data.data
        } else {
          // Handle the case when data or data.data is undefined or empty
          // Return an appropriate value or handle the situation accordingly
          // For example:
          // setisuserRole(false);
          return []
        }
      },
      onError: (err) => {
        console.log(err)
      }
    }
    )
  }
  useEffect(() => {
    GetUserByIdFetchData()
  }, [currentLanguage, userData.data?.firebase_id])

  useEffect(() => {
    const handleCopy = (e) => {
      e.preventDefault();
    };
    document.addEventListener('copy', handleCopy);
    return () => {
      document.removeEventListener('copy', handleCopy);
    };
  }, []);
  return (
    <>
      {settings ? (
        <>
          <SearchPopup />
          <WeatherCard />
          <Header />
          <CatNav />
          <div>{children}</div>
          <Footer />
        </>
      ) : (
        <div className='loader-container'>
          <div className='loader'></div>
        </div>
      )}
    </>
  )
}
export default Layout
