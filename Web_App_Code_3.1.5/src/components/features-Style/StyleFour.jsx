import Link from 'next/link'
import { HiOutlineArrowLongRight } from 'react-icons/hi2'
import { placeholderImage, translate, truncateText } from '../../utils'
import { BsFillPlayFill } from 'react-icons/bs'
import VideoPlayerModal from '../videoplayer/VideoPlayerModal'
import { useState } from 'react'
import AdSpaces from '../view/adSpaces/AdSpaces'

const StyleFour = ({ Data }) => {
  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  const [Video_url, setVideo_url] = useState()
  const [typeUrl, setTypeUrl] = useState(null)
  const [modalShow, setModalShow] = useState(false)

  const handleVideoUrl = url => {
    setModalShow(true)
    setVideo_url(url)
  }


  const TypeUrl = type => {
    setTypeUrl(type)
  }

  return (
    <>
      {/* ad spaces */}
      {Data.ad_spaces && Data.id === Data.ad_spaces.ad_featured_section_id && Data.news_type === 'videos' ? (
        <>
          <AdSpaces ad_url={Data.ad_spaces.ad_url} ad_img={Data.ad_spaces.web_ad_image} style_web={'four'} />
        </>
      ) : null}

      {/* videos */}
      {Data.videos && Data.videos?.length > 0 ? (
        <div id='rns-main' className='video_style_four'>
          <div className='container'>
            <div className='row'>
              <div id='rns-cards-main' className=''>
                <div id='rns-head-main' className=''>
                  <div className='left-sec'>
                    <h4 id='rns-main-logo' className='mb-0'>
                      {Data.title}
                    </h4>
                    <div className='short_desc'>{Data && Data.short_description}</div>
                  </div>

                  <Link id='rns-Viewmore' href={`/video-news-view/${Data.slug}`} onClick={() => scrollToTop()}>
                    {translate('viewMore')}
                  </Link>
                </div>

                <div className='row'>
                  {Data.videos.map((value, index) => {
                    return (
                      <div className='col-xxl-4 col-lg-4 col-md-4 col-sm-6 col-12' key={value.id}>
                        <div
                          id='rns-card'
                          className='card card_hover_two'
                          onClick={() => {
                            handleVideoUrl(value.content_value)
                            TypeUrl(value.type)
                          }}
                        >
                          <div className='banner_thumb'>
                            <img
                              id='rns-image'
                              src={value.image}
                              className='card-img-top'
                              alt={value.title}
                              onError={placeholderImage}
                            />
                          </div>
                          <div id='rns-img-overlay' className=' card-inverse'>
                            <div id='vps-btnVideo'>
                              <BsFillPlayFill id='vps-btnVideo-logo' className='pulse' fill='white' size={50} />
                            </div>
                          </div>
                          <div id='rns-card-body' className='card-block pb-0'>
                            <p className='card-title mb-0'>{value.title}</p>
                          </div>
                        </div>
                      </div>
                    )
                  })}
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
          <AdSpaces ad_url={Data.ad_spaces.ad_url} ad_img={Data.ad_spaces.web_ad_image} style_web={'four'} />
        </>
      ) : null}

      {/* news */}
      {Data && Data.news?.length > 0 ? (
        <div id='rns-main' className='news_style_four'>
          <div className='container'>
            <div className='row'>
              <div id='rns-cards-main' className=''>
                <div id='rns-head-main' className=''>
                  <div className='left-sec'>
                    <h4 id='rns-main-logo' className='mb-0'>
                      {Data.title}
                    </h4>
                    <div className='short_desc'>{Data && Data.short_description}</div>
                  </div>

                  <Link id='rns-Viewmore' href={`/view-all/${Data.slug}`} onClick={() => scrollToTop()}>
                    {translate('viewMore')}
                  </Link>
                </div>

                <div className='row'>
                  {Data.news.map((value, index) => {
                    return (
                      <div className='col-xxl-4 col-lg-4 col-md-4 col-sm-6 col-12' key={value.id}>
                        <Link id='rns-card' className='card card_hover_two' href={{ pathname: `/news/${value.slug}` }}>
                          <div className='banner_thumb'>
                            <img
                              id='rns-image'
                              src={value.image}
                              className='card-img-top'
                              alt={value.title}
                              onError={placeholderImage}
                            />
                          </div>
                          <div id='rns-img-overlay' className=' card-inverse'>
                            <div id='btnrnsCatagory' className='btn btn-sm' type='button'>
                              {truncateText(value.category_name, 10)}
                            </div>
                          </div>
                          <div id='rns-card-body' className='card-block'>
                            <p className='card-title'>{value.title}</p>
                            <p id='btnrnsRead' className='btn overlay' type='button'>
                              {translate('readmore')}
                              <HiOutlineArrowLongRight id='rns-arrow' size={20} />
                            </p>
                          </div>
                        </Link>
                      </div>
                    )
                  })}
                </div>
              </div>
            </div>
          </div>
        </div>
      ) : null}

      {/* ad spaces */}
      {Data.ad_spaces && Data.id === Data.ad_spaces.ad_featured_section_id && Data.news_type === 'breaking_news' ? (
        <>
          <AdSpaces ad_url={Data.ad_spaces.ad_url} ad_img={Data.ad_spaces.web_ad_image} style_web={'four'} />
        </>
      ) : null}

      {/* breaking news */}
      {Data && Data.breaking_news?.length > 0 ? (
        <div id='rns-main'>
          <div className='container'>
            <div className='row'>
              <div id='rns-cards-main' className=''>
                <div id='rns-head-main' className=''>
                  <div className='left-sec'>
                    <h4 id='rns-main-logo' className='mb-0'>
                      {Data.title}
                    </h4>
                    <div className='short_desc'>{Data && Data.short_description}</div>
                  </div>
                  <Link id='rns-Viewmore' href={`/view-all/${Data.slug}`} onClick={() => scrollToTop()}>
                    {translate('viewMore')}
                  </Link>
                </div>

                <div className='row'>
                  {Data && Data.breaking_news.map((value, index) => {
                    return (
                      <div className='col-xxl-4 col-lg-4 col-md-4 col-sm-6 col-12' key={value.id}>
                        <Link
                          id='rns-card'
                          className='card card_hover_two'
                          href={{ pathname: `/breaking-news/${value.slug}` }}
                        >
                          <div className='banner_thumb'>
                            <img
                              id='rns-image'
                              src={value.image}
                              className='card-img-top'
                              alt={value.title}
                              onError={placeholderImage}
                            />
                          </div>
                          <div id='rns-card-body' className='card-block'>
                            <p className='card-title'>{value.title}</p>
                            <p id='btnrnsRead' className='btn overlay' type='button'>
                              {translate('readmore')}
                              <HiOutlineArrowLongRight id='rns-arrow' size={20} />
                            </p>
                          </div>
                        </Link>
                      </div>
                    )
                  })}
                </div>
              </div>
            </div>
          </div>
        </div>
      ) : null}
    </>
  )
}

export default StyleFour
