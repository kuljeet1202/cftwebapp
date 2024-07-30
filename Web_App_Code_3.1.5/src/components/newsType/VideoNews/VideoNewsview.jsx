'use client'
import { useState } from 'react'
import { BsFillPlayFill } from 'react-icons/bs'
import VideoPlayerModal from '../../videoplayer/VideoPlayerModal'
import { useSelector } from 'react-redux'
import { selectCurrentLanguage } from '../../../store/reducers/languageReducer'
import { placeholderImage, translate ,NoDataFound} from '../../../utils'
import BreadcrumbNav from '../../breadcrumb/BreadcrumbNav'
import no_image from '../../../../public/assets/images/no_image.jpeg'
import { useRouter } from 'next/router'
import { useQuery } from '@tanstack/react-query'
import { getFeatureSectionApi } from 'src/hooks/getFeatureSectionApi'
import { access_key, getLanguage, getUser } from 'src/utils/api'
import Layout from 'src/components/layout/Layout'
import Card from 'src/components/skeletons/Card'
import { locationData } from 'src/store/reducers/settingsReducer'
// import NoDataFound from 'src/components/noDataFound/NoDataFound'

const VideoNewsview = () => {
  const [Video_url, setVideo_url] = useState()
  const [modalShow, setModalShow] = useState(false)
  const [typeUrl, setTypeUrl] = useState(null)
  const router = useRouter()
  const query = router.query
  const catid = query.slug
  const currentLanguage = useSelector(selectCurrentLanguage)
  const location = useSelector(locationData)
  const storedLatitude = location && location.lat
  const storedLongitude = location && location.long
  let user = getUser()
  let { id: language_id } = getLanguage()

  // api call
  const getFeatureSectionById = async () => {
    try {
      // const { data } = await getFeatureSectionApi.getFeatureSectionById({
      const { data } = await getFeatureSectionApi.getFeatureSection({
        access_key: access_key,
        language_id: language_id,
        user_id: user,
        offset: '',
        limit: '10',
        slug:catid,
        latitude: storedLatitude,
        longitude: storedLongitude
      })
      return data.data
    } catch (error) {
      console.log(error)
    }
  }

  // react query
  const { isLoading, data: Data } = useQuery({
    queryKey: ['getFeatureSectionById', catid, currentLanguage, location],
    queryFn: getFeatureSectionById
  })

  const handleLiveNewsVideoUrl = url => {
    setModalShow(true)
    setVideo_url(url)
  }

  const TypeUrl = type => {
    setTypeUrl(type)
  }

  return (
    <Layout>
      <BreadcrumbNav SecondElement={'Video News'} ThirdElement='0' />
      <div className='py-5 video_section_all'>
        <div className='container'>
          {isLoading ? (
            <div>
              <div className='row'>
                {[...Array(3)].map((_, index) => (
                  <div className='col-md-4 col-12' key={index}>
                    <Card isLoading={true} />
                  </div>
                ))}
              </div>
            </div>
          ) : (
            <div className='row'>
              {Data && Data[0]?.videos?.length > 0 ? (
                Data[0].videos.map(element => (
                  <div className='col-md-4 col-12' key={element.id}>
                    <div
                      id='vnv-card'
                      className='card'
                      onClick={() => {
                        handleLiveNewsVideoUrl(element.content_value)
                        TypeUrl(element.type)
                      }}
                    >
                      <img
                        id='vnv-card-image'
                        src={element.image ? element.image : no_image}
                        className='card-img'
                        alt={element.title}
                        onError={placeholderImage}
                      />
                      <div className='card-image-overlay' id='vnv-btnVideo'>
                        <BsFillPlayFill id='vnv-btnVideo-logo' className='pulse' fill='white' size={50} />
                      </div>

                      <div id='vnv-card-body' className='card-body'>
                        {/* <button id='vnv-btnCatagory' className='btn btn-sm' type="button" >{element.category_name}</button> */}
                        <h5 id='vnv-card-title' className='card-title'>
                          {element.title}
                        </h5>
                        {/* <Link id='btnvnvRead' className='btn overlay' type="button" href="/news-view" ><IoArrowForwardCircleSharp size={50}/></Link> */}
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

                    {/* </Link> */}
                  </div>
                ))
              ) : (
                <>
                {NoDataFound()}
               
              </>
              )}
            </div>
          )}
        </div>
      </div>
    </Layout>
  )
}

export default VideoNewsview
