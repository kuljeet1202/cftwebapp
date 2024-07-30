'use client'
import bookmarkIMG from '../../../public/assets/images/bookmark.png'
import { FiCalendar } from 'react-icons/fi'
import { BsTrash } from 'react-icons/bs'
import BreadcrumbNav from '../breadcrumb/BreadcrumbNav'
import { placeholderImage, translate } from '../../utils'
import Skeleton from 'react-loading-skeleton'
import { useRouter } from 'next/navigation'
import { bookmarkApi } from 'src/hooks/bookmarkApi'
import { useQuery } from '@tanstack/react-query'
import { access_key, getLanguage, getUser } from 'src/utils/api'
import { useEffect, useState } from 'react'
import Layout from '../layout/Layout'
import Card from '../skeletons/Card'
import { BsBookmark } from 'react-icons/bs';

const BookmarkSection = () => {
  const { id: language_id } = getLanguage()
  const user = getUser()
  const [Data, setData] = useState([])
  const navigate = useRouter()
  // const initialData = useRef([])

  // api call
  const getbookmarkApi = async () => {
    try {
      const { data } = await bookmarkApi.getBookmark({
        access_key: access_key,
        language_id: language_id,
        offset: 0,
        limit: 10,
      })
      setData(data.data)
      // console.log('bookmardData : ',data)
    } catch (error) {
      console.log(error)
    }
  }

  const setbookmarkApi = async (news_id, status) => {
    try {
      const { data } = await bookmarkApi.setBookmark({
        access_key: access_key,
        news_id: news_id,
        status: status
      })
      setData(Data.filter(item => item.news_id !== news_id))
    } catch (error) {
      console.log(error)
    }
  }

  // react query
  const { isLoading, } = useQuery({
    queryKey: ['getbookmark', access_key, user, language_id],
    queryFn: getbookmarkApi,

  })

  const { } = useQuery({
    queryKey: ['setbookmark'],
    queryFn: setbookmarkApi,
  })

  useEffect(() => {
    
  }, [Data])
  

  // Function to format date as desired
  const formatDate = (dateString) => {
    const options = { day: 'numeric', month: 'numeric', year: 'numeric', };
    return new Date(dateString).toLocaleDateString('en-IN', options);
  };

  return (
    <Layout>
      <BreadcrumbNav SecondElement={translate('bookmarkLbl')} ThirdElement='0' />

      <div id='bs-main' className='py-5 bookmark_page'>
        <div id='bs-content' className='container'>
          <div className='row'>
            {isLoading ? (
              <div className='row'>
                {[...Array(3)].map((_, index) => (
                  <div className='col-md-4 col-12' key={index}>
                    <Card isLoading={true} />
                  </div>
                ))}
              </div>
            ) : Data && Data.length > 0 ? (
              Data.map(element => (

                <div className='col-md-6 col-lg-4 col-12' key={element.id}>
                  <div id='bs-card' className='card'>
                    <div className='bs_image_card'>
                      <img
                        id='bs-card-image'
                        src={element.image}
                        className='card-img'
                        alt='bookmark news'
                        onError={placeholderImage}
                        onClick={() =>
                          navigate.push({ pathname: `/news/${element.slug}` })
                        }
                      />
                      <button id='bs-btnBookmark' className='btn' onClick={e => setbookmarkApi(element.news_id, '0')}>
                        <BsBookmark id='bs-bookmark-logo' size={18} />
                      </button>
                    </div>
                    <div id='bs-card-body' className='card-body'>
                      <button
                        id='bs-btnCatagory'
                        className='btn btn-sm'
                        type='button'
                        onClick={() =>
                          navigate.push({ pathname: `/news/${element.slug}`, })
                        }
                      >
                        {element.category_name}
                      </button>
                      <h5
                        id='bs-card-title'
                        className='card-title'
                        onClick={() =>
                          navigate.push({ pathname: `/news/${element.slug}`, })
                        }
                      >
                        {element.title}
                      </h5>
                      <p id='bs-card-date'>
                        <FiCalendar size={18} id='bs-logoCalendar' />
                        {formatDate(element.date.slice(0, 10))}
                      </p>
                    </div>
                  </div>
                </div>
              ))
            ) : (
              // Show "No data found" message if no data is available
              <div className='col-12 no_data mt-5'>
                <div id='bs-no-main'>
                  <img id='bs-no-image' src={bookmarkIMG.src} alt='bookmark no data found news' />
                  <p id='bs-no-title'>
                    <b>{translate('addbookmark')}</b>
                  </p>
                  <p id='bs-no-text'>{translate('dontforgetbookmark')}</p>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </Layout>
  )
}

export default BookmarkSection
