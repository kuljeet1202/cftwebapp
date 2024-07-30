'use client'
import React, { useRef } from 'react'
import { Button, Dropdown, Offcanvas } from 'react-bootstrap'
import { BiBell, BiUserCircle } from 'react-icons/bi'
import { GiHamburgerMenu } from 'react-icons/gi'
import { profileimgError, translate, truncateText } from '../../utils'
import Link from 'next/link'
import {
  loadLanguageLabels,
  selectCurrentLanguage,
  selectLanguages,
  setCurrentLanguage
} from '../../store/reducers/languageReducer'
import { FaAngleDown } from 'react-icons/fa'
import { useSelector } from 'react-redux'
import { selectUser } from '../../store/reducers/userReducer'
import { settingsData } from '../../store/reducers/settingsReducer'
import { AiOutlineSearch } from 'react-icons/ai'
import { SetSearchPopUp } from '../../store/stateSlice/clickActionSlice'
import { store } from '../../store/store'
import usersvg from '../../../public/assets/images/user.svg'
import { useEffect } from 'react'
import { useRouter } from 'next/router'
import { categoriesCacheData } from 'src/store/reducers/CatNavReducers'

const MobilesideBar = ({
  isuserRole,
  name,
  logout,
  deleteAccount,
  onClickHandler,
  modalShow,
  setModalShow,
  islogout,
  setIsLogout,
  handleShow,
  show,
  handleClose,
  ProfileModal,
  ...props
}) => {
  const userData = useSelector(selectUser)

  const categories = useSelector(categoriesCacheData)

  const currentLanguage = useSelector(selectCurrentLanguage)

  const languagesData = useSelector(selectLanguages)

  const settingsOnOff = useSelector(settingsData)

  // language change
  const languageChange = (name, code, id) => {
    loadLanguageLabels({ code: code })
    setCurrentLanguage(name, code, id)
  }

  // useEffect(() => {
  //   if(currentLanguage.code){
  //     loadLanguageLabels({ code: currentLanguage?.code })
  //   }
  // }, [currentLanguage])

  const closeRef = useRef()

  let userName = ''

  const checkUserData = userData => {
    if (userData.data && userData.data.name !== '') {
      return (userName = userData.data.name)
    } else if (userData.data && userData.data.email !== '') {
      return (userName = userData.data.email)
    } else if (userData.data && (userData.data.mobile !== null || userData.data.mobile !== '')) {
      return (userName = userData.data.mobile)
    }
  }

  const searchPopUp = useSelector(state => state.clickAction.searchPopUp)
  const actionSearch = () => {
    store.dispatch(SetSearchPopUp(!searchPopUp))
  }

  const navigate = useRouter()

  const handleSubCategoryChange = (slug) => {
    // console.log(categories.sub_categories)
    if (slug) {
      navigate.push(`/categories-news/sub-category/${slug}`)
      handleClose()
    }
  }

  return (
    <>
      <button className='btn' onClick={handleShow}>
        <GiHamburgerMenu />
      </button>

      <Offcanvas id='Nav-Offcanvas' className='headermodal' show={show} onHide={handleClose} {...props}>
        <Offcanvas.Header closeButton ref={closeRef}>
          <Offcanvas.Title>
            <li id='Nav-btns'>
              {islogout && checkUserData(userData) ? (
                <Dropdown>
                  <Dropdown.Toggle id='btnSignIn' className=''>
                    <img
                      className='profile_photo'
                      src={userData.data && userData.data.profile ? userData.data.profile : usersvg.src}
                      onError={profileimgError}
                      alt='profile'
                    />
                    {truncateText(userName, 10)}
                  </Dropdown.Toggle>

                  <Dropdown.Menu style={{ backgroundColor: '#1A2E51' }}>
                    <Dropdown.Item id='btnLogout'>
                      <Link id='btnBookmark' href='/bookmark' onClick={handleClose}>
                        {translate('bookmark')}
                      </Link>
                    </Dropdown.Item>
                    <Dropdown.Item id='btnLogout' onClick={handleClose}>
                      <Link id='btnBookmark' href='/user-based-categories'>
                        {translate('managePreferences')}
                      </Link>
                    </Dropdown.Item>
                    {isuserRole ? (
                      <>
                        <Dropdown.Item id='btnLogout'>
                          <Link id='btnBookmark' href='/create-news' onClick={() => handleClose()}>
                            {translate('createNewsLbl')}
                          </Link>
                        </Dropdown.Item>

                        <Dropdown.Item id='btnLogout'>
                          <Link id='btnBookmark' href='/manage-news' onClick={() => handleClose()}>
                            {translate('manageNewsLbl')}
                          </Link>
                        </Dropdown.Item>
                      </>
                    ) : null}
                    <Dropdown.Item id='btnLogout'>
                      <Link
                        id='btnBookmark'
                        // onClick={() => {
                        //   ProfileModal(true)
                        //   handleClose()
                        // }}
                        href={'/profile-update'}
                      >
                        {translate('update-profile')}
                      </Link>
                    </Dropdown.Item>
                    <Dropdown.Divider />
                    <Dropdown.Item onClick={logout} id='btnLogout' className=''>
                      {translate('logout')}
                    </Dropdown.Item>
                    <Dropdown.Item id='btnLogout' onClick={e => deleteAccount(e)}>
                        {translate('deleteAcc')}
                      </Dropdown.Item>
                  </Dropdown.Menu>
                </Dropdown>
              ) : (
                <Button variant='danger' onClick={() => setModalShow(true)} id='btnSignIn' className='' type='button'>
                  <BiUserCircle size={23} id='btnLogo' />
                  {translate('login')}
                </Button>
              )}
            </li>

            <li id='Nav-btns'>
              <Dropdown>
                <Dropdown.Toggle id='btnSignIn' className=''>
                  {currentLanguage?.name}
                </Dropdown.Toggle>

                <Dropdown.Menu style={{ backgroundColor: '#1A2E51' }}>
                  {languagesData &&
                    languagesData.map((data, index) => {
                      return (
                        <Dropdown.Item
                          key={index}
                          id='btnLogout'
                          onClick={() => languageChange(data.language, data.code, data.id)}
                        >
                          {data.language}
                        </Dropdown.Item>
                      )
                    })}
                </Dropdown.Menu>
              </Dropdown>
            </li>
            <li id='Nav-btns'>
              {islogout && checkUserData(userData) ? (
                <Link
                  href='/personal-notification'
                  id='btnNotification'
                  type='button'
                  className='btn'
                  onClick={handleClose}
                >
                  <BiBell size={23} />
                  <span className='noti_badge_data'></span>
                </Link>
              ) : null}
            </li>
            {/* searchbar */}
            <li id='Nav-btns' className='mt-2'>
              <div
                id='btnNotification'
                type='button'
                className='btn'
                onClick={() => {
                  actionSearch()
                  handleClose()
                }}
              >
                <AiOutlineSearch size={23} />
              </div>
            </li>
          </Offcanvas.Title>
        </Offcanvas.Header>
        <Offcanvas.Body>
          <ul className=''>
            <li className='nav-item'>
              <b>
                <Link
                  activeclassname='active'
                  exact='true'
                  id='nav-links'
                  className=''
                  aria-current='page'
                  href='/'
                  onClick={handleClose}
                >
                  {translate('home')}
                </Link>
              </b>
            </li>
            {settingsOnOff && settingsOnOff.live_streaming_mode === '1' ? (
              <li className='nav-item'>
                <b>
                  <Link
                    activeclassname='active'
                    exact='true'
                    id='nav-links'
                    className=''
                    aria-current='page'
                    href='/live-news'
                    onClick={handleClose}
                  >
                    {translate('livenews')}
                  </Link>
                </b>
              </li>
            ) : null}
            {settingsOnOff && settingsOnOff.breaking_news_mode === '1' ? (
              <li className='nav-item'>
                <b>
                  <Link
                    activeclassname='active'
                    exact='true'
                    id='nav-links'
                    className=''
                    aria-current='page'
                    href='/all-breaking-news  '
                    onClick={handleClose}
                  >
                    {translate('breakingnews')}
                  </Link>
                </b>
              </li>
            ) : null}
            <li className='nav-item'>
              <b>
                <Link
                  activeclassname='active'
                  exact='true'
                  id='nav-links'
                  className='link-color'
                  aria-current='page'
                  href='/more-pages'
                  onClick={handleClose}
                >
                  {translate('More Pages')}
                </Link>
              </b>
            </li>
            {settingsOnOff && settingsOnOff.category_mode === '1' ? (
              <li className='nav-item has-children'>
                {categories && categories.length > 0 ? (
                  <span className='menu-toggle' onClick={onClickHandler}>
                    <b>
                      <p id='nav-links' className=''>
                        {translate('categories')}
                      </p>
                    </b>
                    <i className=''>
                      <FaAngleDown />
                    </i>
                  </span>
                ) : null}
                <ul className='sub-menu mobile_catogories'>
                  {categories &&
                    categories.slice(0, 10).map((element, index) => (
                      <li className='nav-item' key={index}>
                        {
                          <Link
                              className='catNav-links'
                              key={index}
                              href={{
                                pathname: `/categories-news/${element.slug}`,
                                query: {
                                  category_id: element.id
                                }
                              }}
                              onClick={handleClose}
                            >
                              {' '}
                              <b>{element.category_name}</b>{' '}
                            </Link>
                        }
                      </li>
                    ))}
                  {categories && categories.length > 10 && (
                    <li className='nav-item'>
                      <Link className='catNav-links' href={'/all-categories'} onClick={handleClose}>
                        {' '}
                        <b>{translate('More >>')}</b>{' '}
                      </Link>
                    </li>
                  )}
                </ul>
              </li>
            ) : null}
          </ul>
        </Offcanvas.Body>
      </Offcanvas>
    </>
  )
}

export default MobilesideBar
