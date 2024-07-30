'use client'
import Link from 'next/link'
import BreadcrumbNav from '../breadcrumb/BreadcrumbNav'
import { useSelector } from 'react-redux'
import { selectCurrentLanguage } from '../../store/reducers/languageReducer'
import { placeholderImage, translate, NoDataFound } from '../../utils'
import no_image from '../../../public/assets/images/no_image.jpeg'
import { AllBreakingNewsApi } from 'src/hooks/allBreakingNewsApi'
import { access_key, getLanguage } from 'src/utils/api'
import { useQuery } from '@tanstack/react-query'
import Layout from '../layout/Layout'
import Card from '../skeletons/Card'
// import NoDataFound from '../noDataFound/NoDataFound'
import { useRouter } from 'next/router'

const AllBreakingNews = () => {
  let { id: language_id } = getLanguage()
  const currentlanguage = useSelector(selectCurrentLanguage)

  const router = useRouter()
  // api call 
  const getBreakingNewsApi = async () => {
    try {
      const { data } = await AllBreakingNewsApi.getBreakingNews({ language_id, access_key })
      return data.data
    } catch (error) {
      console.log(error)
    }
  }

  // react query
  const { isLoading, data: Data } = useQuery({
    queryKey: ['all-breaking-news', language_id, access_key, currentlanguage],
    queryFn: getBreakingNewsApi
  })


  return (
    <Layout>
      <BreadcrumbNav SecondElement={translate('breakingNewsLbl')} ThirdElement='0' />
      <div id='BNV-main'>
        <div id='BNV-content' className='container'>
          {isLoading ? (
            <div className='row'>
              {[...Array(3)].map((_, index) => (
                <div className='col-md-4 col-12' key={index}>
                  <Card isLoading={true} />
                </div>
              ))}
            </div>
          ) : (
            <div className='row my-5'>
              {Data && Data.length > 0 ? (
                Data.map(element => (
                  <div className='col-md-4 col-12' key={element.id}>
                    <Link id='Link-all'
                      href={{
                        pathname: `/breaking-news/${element.slug}`
                      }}>
                      <div id='BNV-card' className='card'>
                        <img
                          id='BNV-card-image'
                          src={element.image ? element.image : no_image}
                          className='card-img'
                          alt='breaking news image'
                          onError={placeholderImage}
                        />
                        <div id='BNV-card-body' className='card-body'>
                          <h5 id='BNV-card-title' className='card-title'>
                            {element.title.slice(0, 150)}...
                          </h5>
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
            </div>
          )}
        </div>
      </div>
    </Layout>
  )
}

export default AllBreakingNews
