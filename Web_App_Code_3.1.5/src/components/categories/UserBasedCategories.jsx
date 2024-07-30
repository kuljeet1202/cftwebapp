'use client'
import React, { useEffect, useState } from 'react'
import SwitchButton from 'bootstrap-switch-button-react'
import { categoriesApi, getuserbyidApi, setusercategoriesApi } from '../../store/actions/campaign'
import { translate } from '../../utils'
import { useSelector } from 'react-redux'
import { selectCurrentLanguage } from '../../store/reducers/languageReducer'
import toast from 'react-hot-toast'
import Layout from '../layout/Layout'
import Card from '../skeletons/Card'
import { categoryCountSelector } from 'src/store/reducers/tempDataReducer'
import { categoriesCacheData } from 'src/store/reducers/CatNavReducers'
import { getUserManageData, loadGetUserByIdApi } from 'src/store/reducers/userReducer'
import Loader from './Loader'

const UserBasedCategories = () => {
  const [data, setData] = useState([])
  const [loading, setLoading] = useState(true)
  const [finalToggleID, setFinalToggleID] = useState('')
  const [loader, setLoader] = useState(false)
  
  const currentLanguage = useSelector(selectCurrentLanguage)
  const categories = useSelector(categoriesCacheData)
  const UserManageData = useSelector(getUserManageData)
  
  const [catLength, setCatLength] = useState(categories?.length)

  // get user by id
  useEffect(() => {
    const useridData = UserManageData
    // user categories
    const alluserIds = useridData?.user_category?.category_id
    // common id get

    // category api call
    categoriesApi({
      offset: 0,
      limit: catLength,
      language_id: currentLanguage.id,
      onSuccess: response => {
        const toggledData = response.data.map(element => {
          // here set isToggleOn has boolean with actual data

          const isToggledOn = alluserIds && alluserIds.includes(element.id)

          return { ...element, isToggledOn }
        })
        // Combine paginated data with existing data
        // setData(prevData => [...prevData, ...toggledData])
        setData(toggledData)

        setLoading(false)
      },
      onError: error => {
        if (error === 'No Data Found') {
          setData('')
          setLoading(false)
        }
      }
    })

  }, [currentLanguage])
  
  useEffect(() => {
    setCatLength(categories?.length)
    console.log(categories.length,'cat-length')
  }, [catLength,currentLanguage])


  // handle switch
  const handleSwitchChange = id => {
    setData((prevData) => {
      const newData = prevData.map((element) => {
        if (element.id === id) {
          return { ...element, isToggledOn: !element.isToggledOn };
        }
        return element;
      });

      const toggledIds = newData
        .filter((element) => element.isToggledOn)
        .map((element) => element.id);

      const finalToggleID = toggledIds.length === 0 ? 0 : toggledIds.join(',');
      setFinalToggleID(finalToggleID);


      return newData;
    });
  };


  // here final submit button
  const finalSubmit = e => {
    e.preventDefault()
    setLoader(true)
    // Check if there are any changes in the toggle state
    if (finalToggleID !== '') {
      setusercategoriesApi({
        category_id: finalToggleID,
        onSuccess: response => {
          toast.success(response.message)
          setLoader(false)
          loadGetUserByIdApi({
            onSuccess: (res) => {
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


            },
            onError: (err) => {
              console.log(err)
            }

          })

        },
        onError: error => {
          toast.error(error)
        }
      })
    } else {
      // No changes in toggle state, you can handle this case (optional)
      setLoader(false)
      toast.error('Please select categories')
    }
  }

  // button style
  const switchButtonStyle = {
    width: '100%',
    marginLeft: '3rem',
    marginRight: '3rem'
  }

  return (
    <Layout>
      <section className='manage_preferences py-5'>
        <div className='container'>
          {loading ? (
            <div className='row'>
              {[...Array(3)].map((_, index) => (
                <div className='col-md-4 col-12' key={index}>
                  <Card isLoading={true} />
                </div>
              ))}
            </div>
          ) : (
            <>
              <div className='row'>
                {data && data.length > 0
                  ? data.map((element) => (
                    <div className='col-md-4 col-12' key={element.id}>
                      <div className='manage_card'>
                        <div className='inner_manage'>
                          <div className='manage_image'>
                            <img src={element.image} alt={element.category_name} />
                          </div>
                          <div className='manage_title'>
                            <p className='mb-0'>{element.category_name}</p>
                          </div>
                          <div className='manage_toggle'>
                            <SwitchButton
                              checked={element.isToggledOn}
                              onlabel='ON'
                              onstyle='success'
                              offlabel='OFF'
                              offstyle='danger'
                              style={switchButtonStyle}
                              onChange={() => handleSwitchChange(element.id)}
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  ))
                  : null}
              </div>
              <button className='finalsumit_btn mb-5' onClick={e => finalSubmit(e)}>
                {
                  loader ? <Loader /> : translate('saveLbl')
                }
              </button>
            </>
          )}

        </div>
      </section>
    </Layout>
  )
}

export default UserBasedCategories
