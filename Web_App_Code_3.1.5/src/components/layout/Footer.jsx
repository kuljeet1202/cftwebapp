'use client'

import { FaFacebookSquare, FaInstagram, FaLinkedin } from 'react-icons/fa'
import Link from 'next/link'
import moment from 'moment/moment'
import { useSelector } from 'react-redux'
import { selectCurrentLanguage } from '../../store/reducers/languageReducer'
import { placeholderImage, translate } from '../../utils'
import { settingsData } from '../../store/reducers/settingsReducer'
import { CategoriesApi } from 'src/hooks/categoriesApi'
import { useQuery } from '@tanstack/react-query'
import { access_key } from 'src/utils/api'
import { FaSquareXTwitter } from 'react-icons/fa6'
import { catNavSelector, categoriesCacheData } from 'src/store/reducers/CatNavReducers'

const Footer = () => {
  const settings = useSelector(settingsData)

  const categories = useSelector(categoriesCacheData)
  
  const categoriesData = categories


  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  return (
    <>
      <section id='footer'>
        <div className='container'>
          <div className='row'>
            <div className='col-lg-3 col-12'>
              <div className='News'>
                <Link href='/'>
                  <img id='NewsLogo' src={settings && settings?.web_setting?.web_footer_logo} onError={placeholderImage} alt='footer logo image' />
                </Link>
              </div>
              <div className='Lorem-text'>
                <p className='lorem'>
                  {settings && settings?.web_setting?.web_footer_description}
                  <br />
                </p>
              </div>
            </div>
              {categoriesData && categoriesData.length > 0 ? (
            <div className='col-lg-3 col-12'>
              <p id='footer-nav'>{translate('categories')}</p>
                <ul className='newscate'>
                  {categoriesData.map((element, index) => {
                    return (
                      // element.sub_categories.length > 0 ? (
                      <li key={index}>
                        <Link
                          href={{
                            pathname: `/categories-news/${element.slug}`,
                            // query: {
                            //   category_id: element.id
                            // }
                          }}
                          onClick={scrollToTop}
                        >
                          {element.category_name}{' '}
                        </Link>
                      </li>
                      // ): null
                    )
                  })}
                </ul>
            </div>
              ) : null}

            <div className='col-lg-3 col-12'>
              <p id='footer-nav'>{translate('usefulllinks')}</p>
              <ul className='useL'>
                <li className='nav-item'>
                  <Link href='/' onClick={() => scrollToTop()}>
                    {translate('home')}
                  </Link>
                </li>
                <li className='nav-item'>
                  <Link href='/live-news' onClick={() => scrollToTop()}>
                    {translate('livenews')}
                  </Link>
                </li>
                <li className='nav-item'>
                  <Link href='/all-breaking-news' onClick={() => scrollToTop()}>
                    {translate('breakingnews')}
                  </Link>
                </li>
              </ul>
            </div>

            {
              process.env.NEXT_PUBLIC_FACEBOOK || process.env.NEXT_PUBLIC_INSTAGRAM || process.env.NEXT_PUBLIC_LINKEDIN || process.env.NEXT_PUBLIC_TWITTER ? ( <div className='col-lg-3 col-12'>
                <p id='footer-nav'>{translate('followus')} </p>
                <div className='social_media'>
                  {process.env.NEXT_PUBLIC_FACEBOOK ? (
                    <a
                      target='_blank'
                      id='social_platforms'
                      className='btn btn-outline-white'
                      href={process.env.NEXT_PUBLIC_FACEBOOK}
                      rel='noreferrer'
                    >
                      <FaFacebookSquare /> {translate('facebook')}
                    </a>
                  ) : null}
                  {process.env.NEXT_PUBLIC_INSTAGRAM ? (
                    <a
                      target='_blank'
                      id='social_platforms'
                      className='btn btn-outline-white'
                      href={process.env.NEXT_PUBLIC_INSTAGRAM}
                      rel='noreferrer'
                    >
                      <FaInstagram /> {translate('instagram')}
                    </a>
                  ) : null}
                  {process.env.NEXT_PUBLIC_LINKEDIN ? (
                    <a
                      target='_blank'
                      id='social_platforms'
                      className='btn btn-outline-white'
                      href={process.env.NEXT_PUBLIC_LINKEDIN}
                      rel='noreferrer'
                    >
                      <FaLinkedin /> {translate('linkedin')}
                    </a>
                  ) : null}
                  {process.env.NEXT_PUBLIC_TWITTER ? (
                    <a
                      target='_blank'
                      id='social_platforms'
                      className='btn btn-outline-white'
                      href={process.env.NEXT_PUBLIC_TWITTER}
                      rel='noreferrer'
                    >
                      <FaSquareXTwitter /> {translate('twitter')}
                    </a>
                  ) : null}
                </div>
              </div>
              ) : null

            }


          </div>
          <hr className='hr_line' />

          <div className='d-flex copyright' id='copyright1'>
            <p id='footer-Copyright' className='h6 p-2'>
              {translate('copyright')} © {moment().year()} {translate('allrights')}{' '}
              {settings && settings?.web_setting?.web_name}
            </p>
          </div>
        </div>
      </section>
    </>
  )
}

export default Footer
