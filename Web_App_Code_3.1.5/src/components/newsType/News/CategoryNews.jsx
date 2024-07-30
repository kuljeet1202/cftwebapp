'use client'
import { FiCalendar } from 'react-icons/fi'
import Link from 'next/link'
import BreadcrumbNav from '../../breadcrumb/BreadcrumbNav'
import { useSelector } from 'react-redux'
import { selectCurrentLanguage } from '../../../store/reducers/languageReducer'
import { formatDate, placeholderImage, translate,NoDataFound } from '../../../utils'
import { useRouter } from 'next/router.js'
import { access_key, getLanguage } from 'src/utils/api'
import { useQuery } from '@tanstack/react-query'
import Layout from 'src/components/layout/Layout'
import Card from 'src/components/skeletons/Card'
import { locationData } from 'src/store/reducers/settingsReducer'
import { getNewsApi } from 'src/hooks/newsApi'
import ReactPaginate from 'react-paginate'
import { useEffect, useState } from 'react'
// import NoDataFound from 'src/components/noDataFound/NoDataFound'
import { subCategorySelector } from 'src/store/reducers/tempDataReducer'
import SwiperCore, { Navigation, Pagination } from 'swiper'
import { Swiper, SwiperSlide } from 'swiper/react'
import 'swiper/swiper-bundle.css'
SwiperCore.use([Navigation, Pagination])

const CategoryNews = () => {
  const [currentPage, setCurrentPage] = useState(0)
  const dataPerPage = 8 // number of posts per page
  const router = useRouter()
  const query = router.query
  const catId = query.category_slug
  const catSlug = query.slug
  const slug = query.slug
  let { id: language_id } = getLanguage()
  const changelanguage = useSelector(selectCurrentLanguage)
  const location = useSelector(locationData)
  const storedLatitude = location && location.lat
  const storedLongitude = location && location.long

  const subCategories = useSelector(subCategorySelector)

  // console.log('router ==== ',subCategories)

  // handle page change
  const handlePageChange = ({ selected }) => {
    setCurrentPage(selected)
  }
  // console.log(language_id)
  // api call
  const getNewsByCategoryApi = async page => {


    if (location || currentPage || catSlug) {

      try {
        const { data } = await getNewsApi.getNews({
          access_key: access_key,
          offset: page * dataPerPage,
          limit: dataPerPage,
          language_id: language_id,
          category_slug: catSlug,
          latitude: storedLatitude,
          longitude: storedLongitude
        })
        // console.log('categories', data)
        data.data.sort((a, b) => new Date(b.date) - new Date(a.date));
        return data
      } catch (error) {
        console.log(error)
      }
    }
  }


  // react query
  const { isLoading, data: Data } = useQuery({
    queryKey: ['category-news', language_id, location, currentPage, catSlug],
    queryFn: () => getNewsByCategoryApi(currentPage)
  })

  useEffect(() => {
    // console.log("category-news")
  }, [])

  // slice the array to get the current posts
  const currentData = Data && Data.data && Data.data.slice(0, dataPerPage)
  const CurrentCategoryName = Data && Data.data && Data.data[0]?.category?.category_name
  const lengthdata = (Data && Data.total) || 0

  const swiperOption = {
    loop: subCategories.length > 10 ? true : false,
    speed: 3000,
    spaceBetween: 10,
    slidesPerView: 'auto',
    navigation: false,
    freeMode: true,
    observer: true,
    observeParents: true,
    parallax: true,
    breakpoints: {
      0: {
        slidesPerView: 2.5
      },
      575: {
        slidesPerView: 4
      },
      1200: {
        slidesPerView: 10
      }
    },
    autoplay: {
      delay: 0
    }
  }
  return (
    <Layout>
      <section className='categoryview_Section'>
        <BreadcrumbNav SecondElement={'category' ? 'category' : ''} ThirdElement={CurrentCategoryName && CurrentCategoryName} link="/all-categories" />
        <div id='cv-main' className='bg-white py-3'>
          <div id='cv-content' className='my-5 container'>
            {isLoading ? (
              <div className='row'>
                {[...Array(3)].map((_, index) => (
                  <div className='col-md-4 col-12' key={index}>
                    <Card isLoading={true} />
                  </div>
                ))}
              </div>
            ) : (
              <div className='row'>
                {/* {
                  subCategories.length > 0 ?

                    <div className="col-12 mb-5 subcategoryWrapper">
                      <h4 className='subCatTitle'>Sub-Categories :</h4>

                      <Swiper {...swiperOption}>
                        {
                          subCategories.map((subCat) => {
                            return (
                              <SwiperSlide className='text-center'
                                key={subCat.id}
                              >
                                <Link href={{
                                  pathname: `/categories-news/sub-category/${subCat.slug}`,
                                }}
                                  id='catNav-links'
                                ><b>{subCat?.subcategory_name}</b></Link>
                              </SwiperSlide>
                            )
                          })
                        }

                      </Swiper>
                    </div>
                    : null
                } */}


                {currentData && currentData?.length > 0 ? (
                  currentData.map(element => (
                    <div className='col-lg-3 col-md-4 col-12 ' key={element.id}>
                      <Link
                        id='Link-all'
                        href={{ pathname: `/news/${element.slug}`, }}
                      >
                        <div id='cv-card' className='card'>
                          <img id='cv-card-image' src={element.image} className='card-img' alt={element.title} onError={placeholderImage} />
                          <div id='cv-card-body' className='card-body'>
                            <button id='cv-btnCatagory' className='btn btn-sm' type='button'>
                              {element.category.category_name}
                            </button>
                            <p id='cv-card-title' className='card-title'>
                              {element.title}
                            </p>
                            <p id='cv-card-date'>
                              <FiCalendar size={18} id='cv-logoCalendar' />
                              {formatDate(element.date)}
                            </p>
                          </div>
                        </div>
                      </Link>
                    </div>
                  ))
                ) : (
                  <>
                    {NoDataFound()}
                   
                  </>
                )}
                {lengthdata > 9 ? (
                  <ReactPaginate
                    initialPage={currentPage}
                    previousLabel={translate('previous')}
                    nextLabel={translate('next')}
                    pageCount={Math.ceil(lengthdata / dataPerPage)}
                    onPageChange={handlePageChange}
                    containerClassName={'pagination'}
                    previousLinkClassName={'pagination__link'}
                    nextLinkClassName={'pagination__link'}
                    disabledClassName={'pagination__link--disabled'}
                    activeClassName={'pagination__link--active'}
                  />
                ) : null}


              </div>
            )}
          </div>
        </div>
      </section>
    </Layout>
  )
}

export default CategoryNews
