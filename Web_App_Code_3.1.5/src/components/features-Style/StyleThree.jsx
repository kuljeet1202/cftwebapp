import React, { useState } from 'react'
import { BsFillPlayFill } from 'react-icons/bs'
import Card from 'react-bootstrap/Card'
import Link from 'next/link'
import VideoPlayerModal from '../videoplayer/VideoPlayerModal'
import { placeholderImage, translate, truncateText } from '../../utils'
import AdSpaces from '../view/adSpaces/AdSpaces'

const StyleThree = ({ Data }) => {
  const [Video_url, setVideo_url] = useState()
  const [modalShow, setModalShow] = useState(false)
  const [typeUrl, setTypeUrl] = useState(null)

  const handleVideoUrl = url => {
    setModalShow(true)
    setVideo_url(url)
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
          <AdSpaces ad_url={Data.ad_spaces.ad_url} ad_img={Data.ad_spaces.web_ad_image} style_web={'three'} />
        </>
      ) : null}

      {/* video */}
      {Data.videos && Data.videos?.length > 0 ? (
        <div id='vps-main' className='video_style_three'>
          <div className='container'>
            <div className='row'>
              <div id='vps-head-main' className=''>
                <div className='left-sec'>
                  <p id='vps-main-logo' className='mb-0'>
                    {Data && Data.title}
                  </p>
                  <div className='short_desc'>{Data && Data.short_description}</div>
                </div>
                <Link id='vps-Viewmore' onClick={() => scrollToTop()} href={`/video-news-view/${Data.slug}`}>
                  {translate('viewMore')}
                </Link>
              </div>
            </div>
            <div className='row'>
              <div className='col-lg-6 col-12'>
                <div id='vps-body-left'>
                  {Data.videos[0] ? (
                    <div
                      className='div'
                      onClick={() => {
                        handleVideoUrl(Data.videos[0].content_value)
                        TypeUrl(Data.videos[0].type)
                      }}
                    >
                      <Card id='vps-main-card' className='text-black'>
                        <Card.Img
                          id='vps-main-image'
                          src={Data.videos[0].image}
                          alt='news'
                          onError={placeholderImage}
                        />

                        <Card.ImgOverlay>
                          <div id='vps-btnVideo'>
                            <BsFillPlayFill id='vps-btnVideo-logo' className='pulse' fill='white' size={50} />
                          </div>
                        </Card.ImgOverlay>
                      </Card>
                      <p id='vps-card-title'>
                        <b>{Data.videos[0].title}</b>
                      </p>
                    </div>
                  ) : null}
                </div>
              </div>
              <div className='col-lg-6 col-12'>
                <div id='vps-body-right'>
                  {Data.videos[1] ? (
                    <Card
                      id='vps-image-cards'
                      className='text-black second_video'
                      onClick={() => {
                        handleVideoUrl(Data.videos[1].content_value)
                        TypeUrl(Data.videos[1].type)
                      }}
                    >
                      <Card.Img
                        id='vps-secondry-images'
                        src={Data.videos[1].image}
                        alt='news'
                        onError={placeholderImage}
                      />
                      <Card.ImgOverlay>
                        <div id='vps-btnVideo'>
                          <BsFillPlayFill id='vps-btnVideo-logo' className='pulse' fill='white' size={50} />
                        </div>
                      </Card.ImgOverlay>
                      <p id='vps-card-title'>
                        <b>{Data.videos[1].title}</b>
                      </p>
                    </Card>
                  ) : null}

                  {Data.videos[2] ? (
                    <Card
                      id='vps-image-cards'
                      className='text-black third_video'
                      onClick={() => {
                        handleVideoUrl(Data.videos[2].content_value)
                        TypeUrl(Data.videos[2].type)
                      }}
                    >
                      <Card.Img
                        id='vps-secondry-images'
                        src={Data.videos[2].image}
                        alt='news'
                        onError={placeholderImage}
                      />
                      <Card.ImgOverlay>
                        <div id='vps-btnVideo'>
                          <BsFillPlayFill id='vps-btnVideo-logo' className='pulse' fill='white' size={50} />
                        </div>
                      </Card.ImgOverlay>
                      <p id='vps-card-title'>
                        <b>{Data.videos[2].title}</b>
                      </p>
                    </Card>
                  ) : null}
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
          <AdSpaces ad_url={Data.ad_spaces.ad_url} ad_img={Data.ad_spaces.web_ad_image} style_web={'three'} />
        </>
      ) : null}

      {/* news */}
      {Data && Data.news?.length > 0 ? (
        <div id='vps-main' className='news_style_three'>
          <div className='container'>
            <div className='row'>
              <div id='vps-head-main' className=''>
                <div className='left-sec'>
                  <p id='vps-main-logo' className='mb-0'>
                    {Data && Data.title}
                  </p>
                  <div className='short_desc'>{Data && Data.short_description}</div>
                </div>
                <Link id='vps-Viewmore' onClick={() => scrollToTop()} href={`/view-all/${Data.slug}`}>
                  {translate('viewMore')}
                </Link>
              </div>
            </div>
            <div className='row'>
              <div className='col-lg-6 col-12'>
                <div id='vps-body-left'>
                  {Data.news[0] ? (
                    <Link href={{ pathname: `/news/${Data.news[0].slug}` }}>
                      <Card id='vps-main-card' className='text-black'>
                        <Card.Img id='vps-main-image' src={Data.news[0].image} alt='news' onError={placeholderImage} />
                      </Card>
                      <span className='style_three_cat_name'>{truncateText(Data.news[0].category_name, 10)}</span>
                      <p id='vps-card-title' className='mt-2'>
                        <b>{Data.news[0].title}</b>
                      </p>
                    </Link>
                  ) : null}
                </div>
              </div>
              <div className='col-lg-6 col-12'>
                <div id='vps-body-right'>
                  {Data.news[1] ? (
                    <Link href={{ pathname: `/news/${Data.news[1].slug}` }}>
                      <Card id='vps-image-cards' className='text-black second_video'>
                        <Card.Img
                          id='vps-secondry-images'
                          src={Data.news[1].image}
                          alt='news'
                          onError={placeholderImage}
                        />
                        <Card.ImgOverlay>
                          <div className='inner_Card_content'>
                            <span className='style_three_cat_name'>{truncateText(Data.news[1].category_name, 10)}</span>
                            <p id='vps-card-title'>
                              <b>{Data.news[1].title}</b>
                            </p>
                          </div>
                        </Card.ImgOverlay>
                      </Card>
                    </Link>
                  ) : null}

                  {Data.news[2] ? (
                    <Link href={{ pathname: `/news/${Data.news[2].slug}` }}>
                      <Card id='vps-image-cards' className='text-black third_video'>
                        <Card.Img
                          id='vps-secondry-images'
                          src={Data.news[2].image}
                          alt='news'
                          onError={placeholderImage}
                        />
                        <Card.ImgOverlay>
                          <div className='inner_Card_content'>
                            <span className='style_three_cat_name'>{truncateText(Data.news[2].category_name, 10)}</span>
                            <p id='vps-card-title'>
                              <b>{Data.news[2].title}</b>
                            </p>
                          </div>
                        </Card.ImgOverlay>
                      </Card>
                    </Link>
                  ) : null}
                </div>
              </div>
            </div>
          </div>
        </div>
      ) : null}

      {/* ad spaces */}
      {Data.ad_spaces && Data.id === Data.ad_spaces.ad_featured_section_id && Data.news_type === 'breaking_news' ? (
        <>
          <AdSpaces ad_url={Data.ad_spaces.ad_url} ad_img={Data.ad_spaces.web_ad_image} style_web={'three'} />
        </>
      ) : null}

      {/* breaking news */}
      {Data && Data.breaking_news?.length > 0 ? (
        <div id='vps-main' className='breaking_news_style_three'>
          <div className='container'>
            <div className='row'>
              <div id='vps-head-main' className=''>
                <div className='left-sec'>
                  <p id='vps-main-logo' className='mb-0'>
                    {Data && Data.title}
                  </p>
                  <div className='short_desc'>{Data && Data.short_description}</div>
                </div>
                <Link id='vps-Viewmore' onClick={() => scrollToTop()} href={`/view-all/${Data.slug}`}>
                  {translate('viewMore')}
                </Link>
              </div>
            </div>
            <div className='row'>
              <div className='col-lg-6 col-12'>
                <div id='vps-body-left'>
                  {Data.breaking_news[0] ? (
                    <Link href={{ pathname: `/breaking-news/${Data.breaking_news[0].slug}` }}>
                      <Card id='vps-main-card' className='text-black'>
                        <Card.Img
                          id='vps-main-image'
                          src={Data.breaking_news[0].image}
                          alt='news'
                          onError={placeholderImage}
                        />
                      </Card>
                      <p id='vps-card-title'>
                        <b>{Data.breaking_news[0].title}</b>
                      </p>
                    </Link>
                  ) : null}
                </div>
              </div>
              <div className='col-lg-6 col-12'>
                <div id='vps-body-right'>
                  {Data.breaking_news[1] ? (
                    <Link href={{ pathname: `/breaking-news/${Data.breaking_news[1].slug}` }}>
                      <Card id='vps-image-cards' className='text-black second_video'>
                        <Card.Img
                          id='vps-secondry-images'
                          src={Data.breaking_news[1].image}
                          alt='news'
                          onError={placeholderImage}
                        />
                        <Card.ImgOverlay>
                          <p id='vps-card-title'>
                            <b>{Data.breaking_news[1].title}</b>
                          </p>
                        </Card.ImgOverlay>
                      </Card>
                    </Link>
                  ) : null}

                  {Data.breaking_news[2] ? (
                    <Link href={{ pathname: `/breaking-news/${Data.breaking_news[2].slug}` }}>
                      <Card id='vps-image-cards' className='text-black third_video'>
                        <Card.Img
                          id='vps-secondry-images'
                          src={Data.breaking_news[2].image}
                          alt='news'
                          onError={placeholderImage}
                        />
                        <Card.ImgOverlay>
                          <p id='vps-card-title'>
                            <b>{Data.breaking_news[2].title}</b>
                          </p>
                        </Card.ImgOverlay>
                      </Card>
                    </Link>
                  ) : null}
                </div>
              </div>
            </div>
          </div>
        </div>
      ) : null}
    </>
  )
}

export default StyleThree
