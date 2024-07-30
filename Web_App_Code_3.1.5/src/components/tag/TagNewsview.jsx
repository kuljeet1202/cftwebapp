'use client'

import { FiCalendar } from 'react-icons/fi'
import Link from 'next/link'
import { formatDate, placeholderImage, translate,NoDataFound } from '../../utils'
import { useRouter } from 'next/router'
import { useQuery } from '@tanstack/react-query'
import { access_key, getLanguage } from 'src/utils/api'
import Layout from '../layout/Layout'
import Card from '../skeletons/Card'
import { getNewsApi } from 'src/hooks/newsApi'
// import NoDataFound from '../noDataFound/NoDataFound'

const TagNewsview = () => {
  const router = useRouter()
  const query = router.query
  const Tid = query.slug
  let { id: language_id } = getLanguage()
  // api call
  const getNewsByTag = async () => {
    try {
      const { data } = await getNewsApi.getNews({
        access_key: access_key,
        tag_slug: Tid,
        // tag_id: Tid,
        language_id: language_id,
      })
      return data
    } catch (error) {
      console.log(error)
    }
  }

  // react query
  const { isLoading, data: Data } = useQuery({
    queryKey: ['getNewsByTag', Tid, query],
    queryFn: getNewsByTag
  })

  // tags
  const tagSplit = tag => {
    if (tag && typeof tag === 'string') {
      return tag.split(',');
    } else {
      // Handle the case where tag is not a string or is undefined
      return [];
    }
  }

  return (
    <Layout>
      <div className='py-5 tagview bg-white'>
        <div className='container'>
          <div className='row'>
            {isLoading ? (
              <div className='row'>
                {[...Array(3)].map((_, index) => (
                  <div className='col-md-4 col-12' key={index}>
                    <Card isLoading={true} />
                  </div>
                ))}
              </div>
            ) : Data && Data.error !== true ? (
              <>
                {Data &&
                  Data?.data?.map(element => (
                    <div className='col-md-4 col-12' key={element.id}>
                      <Link id='Link-all' href={{pathname:`/news/${element.slug}`}}>
                        <div id='ts-card' className='card'>
                          <img id='ts-card-image' src={element.image} className='card-img' alt={element.title} onError={placeholderImage}/>

                          <div id='ts-card-body' className='card-body'>
                            <div className='tag_button'>
                              {tagSplit(element.tag_name).map((tag, index) => (
                                <button className='btn btn-sm tag-button' type='button' key={index}>
                                  {tag}
                                </button>
                              ))}
                            </div>

                            <h5 id='ts-card-title' className='card-title'>
                              {element.title.slice(0, 150)}...
                            </h5>
                            <p id='ts-card-date'>
                              <FiCalendar size={18} id='ts-logoCalendar' />
                              {formatDate(element.date.slice(0, 10))}
                            </p>
                          </div>
                        </div>
                      </Link>
                    </div>
                  ))}
              </>
            ) : (
              <>
              {NoDataFound()}
             
            </>
            )}
          </div>
        </div>
      </div>
    </Layout>
  )
}

export default TagNewsview
