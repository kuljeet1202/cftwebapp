import breakingNews2_jpg from '../../../public/assets/images/earthImage.png'
import Link from 'next/link'
import { placeholderImage, translate, truncateText } from '../../utils'
import SwiperCore, { Navigation, Pagination } from 'swiper'
import { Swiper, SwiperSlide } from 'swiper/react'
import 'swiper/swiper-bundle.css'
import Skeleton from 'react-loading-skeleton'
import { BsFillPlayFill } from 'react-icons/bs'
import VideoPlayerModal from '../videoplayer/VideoPlayerModal'
import { useState } from 'react'
import AdSpaces from '../view/adSpaces/AdSpaces'

SwiperCore.use([Navigation, Pagination])
const StyleFive = ({ isLoading, Data }) => {
  const [Video_url, setVideo_url] = useState()
  const [modalShow, setModalShow] = useState(false)
  const [typeUrl, setTypeUrl] = useState(null)

  const handleVideoUrl = url => {
    setModalShow(true)
    setVideo_url(url)
  }

  const showNavigation = Data.news?.length > 1

  const showNavigationBreaking = Data.breaking_news?.length > 1

  const showNavigationVideo = Data.videos?.length > 1

  const swiperOption = {
    loop: true,
    speed: 750,
    spaceBetween: 10,
    slidesPerView: 2,
    navigation: showNavigation,
    breakpoints: {
      0: {
        slidesPerView: 1
      },

      768: {
        slidesPerView: 2
      },

      992: {
        slidesPerView: 2
      },
      1200: {
        slidesPerView: 3
      }
    },
    autoplay: true
  }

  const swiperOptionVideo = {
    loop: true,
    speed: 750,
    spaceBetween: 10,
    slidesPerView: 2,
    navigation: showNavigationVideo,
    breakpoints: {
      0: {
        slidesPerView: 1
      },

      768: {
        slidesPerView: 2
      },

      992: {
        slidesPerView: 2
      },
      1200: {
        slidesPerView: 3
      }
    },
    autoplay: true
  }

  const swiperOptionBreaking = {
    loop: true,
    speed: 750,
    spaceBetween: 10,
    slidesPerView: 2,
    navigation: showNavigationBreaking,
    breakpoints: {
      0: {
        slidesPerView: 1
      },

      768: {
        slidesPerView: 2
      },

      992: {
        slidesPerView: 2
      },
      1200: {
        slidesPerView: 3
      }
    },
    autoplay: true
  }

  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  const TypeUrl = type => {
    setTypeUrl(type)
  }

  return (
    <>
      {/* ad spaces */}
      {Data.ad_spaces && Data.id === Data.ad_spaces.ad_featured_section_id && Data.news_type === 'videos' ? (
        <>
          <AdSpaces ad_url={Data.ad_spaces.ad_url} ad_img={Data.ad_spaces.web_ad_image} style_web={'five'} />
        </>
      ) : null}

      {/* videos */}
      {Data.videos && Data.videos?.length > 0 ? (
        <div id='bns-main' className='video_style_five'>
          <div className='container custom-card'>
            <div className='row'>
              <div className='col-md-4 col-12'>
                <div id='bns-main-card' className='card'>
                  <img
                    id='bns-main-image'
                    src={breakingNews2_jpg.src}
                    className='card-img'
                    alt={Data.title}
                    onError={placeholderImage}
                  />
                  <div id='bns-main-text' className='card-img-overlay'>
                    <p id='bns-logo-col' className='card-text'>
                      <b>{Data.title}</b>
                    </p>
                    <p id='bns-logo-row' className='card-text'>
                      <b>{Data.title}</b>
                    </p>
                    <Link
                      id='btnbnsViewAll'
                      className='btn'
                      type='button'
                      href={`/video-news-view/${Data.slug}`}
                      onClick={() => scrollToTop()}
                    >
                      {translate('viewall')}
                    </Link>
                  </div>
                </div>
              </div>
              <div className='col-md-8 col-12'>
                <div id='bns-rest-cards'>
                  <Swiper {...swiperOptionVideo}>
                    {isLoading ? (
                      // Show skeleton loading when data is being fetched
                      <div className='col-12 loading_data'>
                        <Skeleton height={20} count={22} />
                      </div>
                    ) : (
                      Data.videos.map(element => (
                        <SwiperSlide key={element.id}>
                          <div id='bns-card' className='card' key={element.id}>
                            <div
                              id='Link-all'
                              onClick={() => {
                                handleVideoUrl(element.content_value)
                                TypeUrl(element.type)
                              }}
                            >
                              <img
                                id='bns-image'
                                src={element.image}
                                className='card-img-top'
                                alt={element.title}
                                onError={placeholderImage}
                              />
                              <div id='rns-img-overlay' className=' card-inverse'>
                                <div id='vps-btnVideo'>
                                  <BsFillPlayFill id='vps-btnVideo-logo' className='pulse' fill='white' size={50} />
                                </div>
                              </div>
                              <div id='bns-card-body' className='card-body ps-0'>
                                <h5 id='bns-card-text' className=''>
                                  {truncateText(element.title, 20)}
                                </h5>
                              </div>
                            </div>
                          </div>
                        </SwiperSlide>
                      ))
                    )}
                  </Swiper>
                </div>
              </div>
              <VideoPlayerModal
                show={modalShow}
                onHide={() => setModalShow(false)}
                // backdrop="static"
                keyboard={false}
                url={Video_url}
                type_url={typeUrl}
              // title={Data[0].title}
              />
            </div>
          </div>
        </div>
      ) : null}

      {/* ad spaces */}
      {Data.ad_spaces && Data.id === Data.ad_spaces.ad_featured_section_id && Data.news_type === 'news' ? (
        <>
          <AdSpaces ad_url={Data.ad_spaces.ad_url} ad_img={Data.ad_spaces.web_ad_image} style_web={'five'} />
        </>
      ) : null}

      {/* news */}
      {Data && Data.news?.length > 0 ? (
        <div id='bns-main' className='news_style_five'>
          <div className='container custom-card'>
            <div className='row'>
              <div className='col-md-4 col-12'>
                <div id='bns-main-card' className='card'>
                  <img
                    id='bns-main-image'
                    src={breakingNews2_jpg.src}
                    className='card-img'
                    alt={Data.title}
                    onError={placeholderImage}
                  />
                  <div id='bns-main-text' className='card-img-overlay'>
                    <p id='bns-logo-col' className='card-text'>
                      <b>{Data.title}</b>
                    </p>
                    <p id='bns-logo-row' className='card-text'>
                      <b>{Data.title}</b>
                    </p>
                    <Link
                      id='btnbnsViewAll'
                      className='btn'
                      type='button'
                      href={`/view-all/${Data.slug}`}
                      onClick={() => scrollToTop()}
                    >
                      {translate('viewall')}
                    </Link>
                  </div>
                </div>
              </div>
              <div className='col-md-8 col-12'>
                <div id='bns-rest-cards'>
                  <Swiper {...swiperOption}>
                    {isLoading ? (
                      // Show skeleton loading when data is being fetched
                      <div className='col-12 loading_data'>
                        <Skeleton height={20} count={22} />
                      </div>
                    ) : (
                      Data.news.map(element => (
                        <SwiperSlide key={element.id}>
                          <div id='bns-card' className='card' key={element.id}>
                            <Link id='Link-all' href={{ pathname: `/news/${element.slug}` }}>
                              <img
                                id='bns-image'
                                src={element.image}
                                className='card-img-top'
                                alt={element.title}
                                onError={placeholderImage}
                              />
                              <div id='bns-card-body' className='card-body ps-0'>
                                <div
                                  id='btnbnsCatagory'
                                  className='btn btn-sm'
                                >
                                  {truncateText(element.category_name, 10)}
                                </div>
                                <h5 id='bns-card-text' className=''>
                                  {element.title}
                                </h5>
                              </div>
                            </Link>
                          </div>
                        </SwiperSlide>
                      ))
                    )}
                  </Swiper>
                </div>
              </div>
            </div>
          </div>
        </div>
      ) : null}

      {/* ad spaces */}
      {Data.ad_spaces && Data.id === Data.ad_spaces.ad_featured_section_id && Data.news_type === 'breaking_news' ? (
        <>
          <AdSpaces ad_url={Data.ad_spaces.ad_url} ad_img={Data.ad_spaces.web_ad_image} style_web={'five'} />
        </>
      ) : null}

      {/* breaking news */}
      {Data && Data.breaking_news?.length > 0 ? (
        <div id='bns-main'>
          <div className='container custom-card'>
            <div className='row'>
              <div className='col-md-4 col-12'>
                <div id='bns-main-card' className='card'>
                  <img
                    id='bns-main-image'
                    src={breakingNews2_jpg.src}
                    className='card-img'
                    alt={Data.title}
                    onError={placeholderImage}
                  />
                  <div id='bns-main-text' className='card-img-overlay'>
                    <p id='bns-logo-col' className='card-text'>
                      <b>{Data.title}</b>
                    </p>
                    <p id='bns-logo-row' className='card-text'>
                      <b>{Data.title}</b>
                    </p>
                    <Link
                      id='btnbnsViewAll'
                      className='btn'
                      type='button'
                      href={`/view-all/${Data.slug}`}
                      onClick={() => scrollToTop()}
                    >
                      {translate('viewall')}
                    </Link>
                  </div>
                </div>
              </div>
              <div className='col-md-8 col-12'>
                <div id='bns-rest-cards'>
                  <Swiper {...swiperOptionBreaking}>
                    {isLoading ? (
                      // Show skeleton loading when data is being fetched
                      <div className='col-12 loading_data'>
                        <Skeleton height={20} count={22} />
                      </div>
                    ) : (
                      Data.breaking_news.map(element => (
                        <SwiperSlide key={element.id}>
                          <div id='bns-card' className='card' key={element.id}>
                            <Link id='Link-all' href={{ pathname: `/breaking-news/${element.slug}` }}>
                              <img
                                id='bns-image'
                                src={element.image}
                                className='card-img-top'
                                alt={element.title}
                                onError={placeholderImage}
                              />
                              <div id='bns-card-body' className='card-body ps-0'>
                                <h5 id='bns-card-text' className=''>
                                  {element.title}
                                </h5>
                              </div>
                            </Link>
                          </div>
                        </SwiperSlide>
                      ))
                    )}
                  </Swiper>
                </div>
              </div>
            </div>
          </div>
        </div>
      ) : null}
    </>
  )
}

export default StyleFive
