import { BsFillPlayFill } from 'react-icons/bs'
import Link from 'next/link'
import SwiperCore, { Navigation, Pagination, Autoplay } from 'swiper'
import { Swiper, SwiperSlide } from 'swiper/react'
import 'swiper/swiper-bundle.css'
import Skeleton from 'react-loading-skeleton'
import { placeholderImage, stripHtmlTags, translate, truncateText } from '../../utils'
import { useState } from 'react'
import VideoPlayerModal from '../videoplayer/VideoPlayerModal'
import { useQuery } from '@tanstack/react-query'
import { access_key, getLanguage, getUser } from 'src/utils/api'
import { locationData } from 'src/store/reducers/settingsReducer'
import { useSelector } from 'react-redux'
import { useRouter } from 'next/navigation'
import { getFeatureSectionApi } from 'src/hooks/getFeatureSectionApi'
import AdSpaces from '../view/adSpaces/AdSpaces'

SwiperCore.use([Navigation, Pagination, Autoplay])

const StyleSix = ({ isLoading, Data }) => {

  const [Video_url, setVideo_url] = useState()
  const [modalShow, setModalShow] = useState(false)
  const [typeUrl, setTypeUrl] = useState(null)
  let user = getUser()
  let { id: language_id } = getLanguage()
  const location = useSelector(locationData)
  const storedLatitude = location && location.lat
  const storedLongitude = location && location.long
  const router = useRouter()
  const Newbreakpoints = {
    320: {
      slidesPerView: 1
    },
    375: {
      slidesPerView: 1.5
    },
    576: {
      slidesPerView: 1.5
    },
    768: {
      slidesPerView: 2
    },
    992: {
      slidesPerView: 3
    },
    1200: {
      slidesPerView: 3
    },
    1400: {
      slidesPerView: 4
    }
  }
  const swiperOptionUpdate = {
    loop: false,
    speed: 750,
    spaceBetween: 10,
    slidesPerView: 4,
    navigation: false,
    breakpoints: Newbreakpoints,
    autoplay: {
      delay: 2000000,
      disableOnInteraction: false
    }
    // pagination: { clickable: true },
  }

  const handleVideoUrl = url => {
    setModalShow(true)
    setVideo_url(url)
  }

  const getFeatureSectionById = async () => {
    try {
      // const { data } = await getFeatureSectionApi.getFeatureSectionById({
      const { data } = await getFeatureSectionApi.getFeatureSection({
        access_key: access_key,
        section_id: Data?.id,
        language_id: language_id,
        latitude: storedLatitude,
        longitude: storedLongitude
      })
      return data.data
    } catch (error) {
      console.log(error)
    }
  }

  // react query
  const { data: sliderData } = useQuery({
    queryKey: ['styleSixFeature', location, Data?.id],
    queryFn: getFeatureSectionById
  })

  const TypeUrl = type => {
    setTypeUrl(type)
  }


  return (
    <div id='first-section'>
      {/* ad spaces */}
      {Data.ad_spaces && Data.id === Data.ad_spaces.ad_featured_section_id && Data.news_type === 'videos' ? (
        <>
          <AdSpaces ad_url={Data.ad_spaces.ad_url} ad_img={Data.ad_spaces.web_ad_image} style_web='six' />
        </>
      ) : null}

      {/* video section */}
      {sliderData && sliderData[0].videos?.length > 0 ? (
        <div className='container'>
          <div id='style-six-body-section'>
            <div id='rns-head-main' className=''>
              <div className='left-sec'>
                <h4 id='rns-main-logo' className='mb-0'>
                  {Data.title}
                </h4>
                <div className='short_desc'>{Data && Data.short_description}</div>
              </div>

              {/* <Link id='rns-Viewmore' href={`/video-news-view/${Data.slug}`} onClick={() => scrollToTop()}>
                {translate('viewMore')}
              </Link> */}
            </div>
            <Swiper {...swiperOptionUpdate} className='custom-swiper'>
              {isLoading ? (
                // Show skeleton loading when data is being fetched
                <div className='col-12 loading_data'>
                  <Skeleton height={20} count={22} />
                </div>
              ) : (
                sliderData[0].videos.map(item => (
                  <SwiperSlide key={item.id}>
                    <Link href={{ pathname: `/news/${item.slug}` }}>
                      <div className='card fs-Newscard'>
                        <img
                          src={item.image}
                          alt={item.title}
                          className='fs-Newscard-image h-auto'
                          id='fs-Newscard-image01'
                          onError={placeholderImage}
                        />
                        <div className='card-img-overlay'>
                          {item && item.category_name ? (
                            <div
                              onClick={() =>
                                router.push({
                                  pathname: `/categories-news/${item.slug}`,
                                  query: { category_id: item.id }
                                })
                              }
                              id='btnCatagory'
                              className='btn'
                              type='button'
                            >
                              {truncateText(item.category_name, 25)}
                            </div>
                          ) : null}

                          <div
                            className='video_slider_button'
                            onClick={() => {
                              handleVideoUrl(item.content_value)
                              TypeUrl(item.type)
                            }}
                          >
                            <BsFillPlayFill className='pulse' fill='white' size={50} />
                          </div>

                          {sliderData[0].videos_type === 'news' ? (
                            <div id='Top-Deatils'>
                              {item && item.date ? (
                                <p id='Top-Posttime01'>
                                  {item.date
                                    ? new Date(item.date).toLocaleString('en-us', {
                                      day: 'numeric',
                                      month: 'short',
                                      year: 'numeric'
                                    })
                                    : ''}
                                </p>
                              ) : null}

                              <div
                                onClick={() =>
                                  router.push({
                                    pathname: `/news/${item.slug}`,
                                    query: { language_id: item.language_id }
                                  })
                                }
                                id='Top-Title01'
                              >
                                {truncateText(item.title, 30)} <br />
                                {stripHtmlTags(item.description.slice(0, 600))} ...
                              </div>
                            </div>
                          ) : null}
                        </div>
                      </div>
                    </Link>
                  </SwiperSlide>
                ))
              )}
              <VideoPlayerModal
                show={modalShow}
                onHide={() => setModalShow(false)}
                // backdrop="static"
                keyboard={false}
                url={Video_url}
                type_url={typeUrl}
              // title={Data[0].title}
              />
            </Swiper>
          </div>
        </div>
      ) : null}

      {/* ad spaces */}
      {Data.ad_spaces && Data.id === Data.ad_spaces.ad_featured_section_id && Data.news_type === 'news' ? (
        <>
          <AdSpaces ad_url={Data.ad_spaces.ad_url} ad_img={Data.ad_spaces.web_ad_image} style_web={'six'} />
        </>
      ) : null}

      {/* news section */}
      {sliderData && sliderData[0].news?.length > 0 ? (
        <div className='container'>
          <div id='style-six-body-section'>
            <div id='rns-head-main' className=''>
              <div className='left-sec'>
                <h4 id='rns-main-logo' className='mb-0'>
                  {Data.title}
                </h4>
                <div className='short_desc'>{Data && Data.short_description}</div>
              </div>

              {/* <Link id='rns-Viewmore' href={`/video-news-view/${Data.slug}`} onClick={() => scrollToTop()}>
                {translate('viewMore')}
              </Link> */}
            </div>
            <Swiper {...swiperOptionUpdate} className='custom-swiper'>
              {isLoading ? (
                // Show skeleton loading when data is being fetched
                <div className='col-12 loading_data'>
                  <Skeleton height={20} count={22} />
                </div>
              ) : (

                sliderData[0].news.map(item => (
                  <SwiperSlide key={item.id}>
                    <Link href={{ pathname: `/news/${item.slug}` }}>
                      <div className='card fs-Newscard'>
                        <img
                          src={item.image}
                          alt={item.title}
                          className='fs-Newscard-image h-auto'
                          id='fs-Newscard-image01'
                          onError={placeholderImage}
                        />
                        <div className='card-img-overlay'>
                          <div
                            id='btnCatagory'
                            className='btn'
                            onClick={() =>
                              router.push({
                                pathname: `/categories-news/${item.slug}`,
                                query: { category_id: item.id }
                              })
                            }
                          >
                            {truncateText(item.category_name, 25)}
                          </div>
                          <div id='Top-Deatils'>
                            <p id='Top-Posttime01'>
                              {item.date
                                ? new Date(item.date).toLocaleString('en-in', {
                                  day: 'numeric',
                                  month: 'short',
                                  year: 'numeric'
                                })
                                : ''}
                            </p>
                            <div
                              onClick={() =>
                                router.push({
                                  pathname: `/news/${item.slug}`,
                                  query: { language_id: item.language_id }
                                })
                              }
                              id='Top-Title01'
                            >
                              {truncateText(item.title, 30)}
                            </div>
                          </div>
                        </div>
                      </div>
                    </Link>
                  </SwiperSlide>
                ))
              )}

              <VideoPlayerModal
                show={modalShow}
                onHide={() => setModalShow(false)}
                // backdrop="static"
                keyboard={false}
                url={Video_url}
                type_url={typeUrl}
              // title={Data[0].title}
              />
            </Swiper>
          </div>
        </div>
      ) : null}

      {/* ad spaces */}
      {Data.ad_spaces && Data.id === Data.ad_spaces.ad_featured_section_id && Data.news_type === 'breaking_news' ? (
        <>
          <AdSpaces ad_url={Data.ad_spaces.ad_url} ad_img={Data.ad_spaces.web_ad_image} style_web={'six'} />
        </>
      ) : null}

      {/* breaking news section */}
      {sliderData && sliderData[0].breaking_news?.length > 0 ? (
        <div className='container'>
          <div id='style-six-body-section'>
            <div id='rns-head-main' className=''>
              <div className='left-sec'>
                <h4 id='rns-main-logo' className='mb-0'>
                  {Data.title}
                </h4>
                <div className='short_desc'>{Data && Data.short_description}</div>
              </div>

              {/* <Link id='rns-Viewmore' href={`/video-news-view/${Data.slug}`} onClick={() => scrollToTop()}>
                {translate('viewMore')}
              </Link> */}
            </div>
            <Swiper {...swiperOptionUpdate} className='custom-swiper'>
              {isLoading ? (
                // Show skeleton loading when data is being fetched
                <div className='col-12 loading_data'>
                  <Skeleton height={20} count={22} />
                </div>
              ) : (
                sliderData[0].breaking_news.map(item => (
                  <SwiperSlide key={item.id}>
                    {/* <Link href={item.content_value ? '#' : `/breaking-news/${item.slug}`}> */}
                    <div className='card fs-Newscard'>
                      <img
                        src={item.image}
                        alt={item.title}
                        className='fs-Newscard-image h-auto'
                        id='fs-Newscard-image01'
                        onError={placeholderImage}
                      />
                      <div className='card-img-overlay'>
                        {item.content_value ? (
                          <div
                            className='video_slider_button'
                            onClick={() => {
                              handleVideoUrl(item.content_value)
                              TypeUrl(item.type)
                            }}
                          >
                            <BsFillPlayFill className='pulse' fill='white' size={50} />
                          </div>
                        ) : null}
                        <div id='Top-Deatils'>
                          <Link
                            href={{ pathname: `/breaking-news/${item.slug}`, }}
                            id='Top-Title01'
                          >
                            {truncateText(item.title, 30)}
                            {stripHtmlTags(item.description.slice(0, 600))} ...
                          </Link>
                        </div>
                      </div>
                    </div>
                    {/* </Link> */}
                  </SwiperSlide>
                ))
              )}
              <VideoPlayerModal
                show={modalShow}
                onHide={() => setModalShow(false)}
                // backdrop="static"
                keyboard={false}
                url={Video_url}
                type_url={typeUrl}
              // title={Data[0].title}
              />
            </Swiper>
          </div>
        </div>
      ) : null}
    </div>
  )
}

export default StyleSix
