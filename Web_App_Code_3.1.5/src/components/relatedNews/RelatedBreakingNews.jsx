'use client'
import Link from 'next/link'
import { useSelector } from 'react-redux'
import { selectCurrentLanguage } from '../../store/reducers/languageReducer'
import { placeholderImage, translate } from '../../utils'
import Skeleton from 'react-loading-skeleton'
import { AllBreakingNewsApi } from 'src/hooks/allBreakingNewsApi'
import { access_key, getLanguage } from 'src/utils/api'
import { useQuery } from '@tanstack/react-query'

const RelatedBreakingNews = props => {
  let { id: language_id } = getLanguage()
  const currentLanguage = useSelector(selectCurrentLanguage)

  // api call
  const getBreakingNews = async () => {
    try {
      const { data } = await AllBreakingNewsApi.getBreakingNews({ language_id, access_key })
      const filteredData = data && data?.data.filter(element => element.id !== props.id)
      return filteredData
    } catch (error) {
      console.log(error)
    }
  }

  // react query
  const { isLoading, data: Data } = useQuery({
    queryKey: ['relatedBreakingNews', currentLanguage, props.id],
    queryFn: getBreakingNews
  })

  const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  return (
    <div>
      {isLoading ? (
        <div>
          <Skeleton height={200} count={3} />
        </div>
      ) : Data && Data.length > 0 ? (
        <div id='rbn-main'>
          <div id='rbn-cat-nav' className='navbar'>
            <h4 id='nav-logo' className='mb-0'>
              <b>{translate('related-news')}</b>
            </h4>
          </div>
          {Data &&
            Data.map(element => (
              <div key={element.id}>
                <Link id='Link-all' href={{pathname:`/breaking-news/${element.slug}`}} onClick={scrollToTop}>
                  <div id='rbn-card' className='card'>
                    <img id='rbn-image' src={element.image} className='card-img-top' alt={element.title} onError={placeholderImage}/>
                    <div id='rbn-card-body' className='rbn-card-body'>
                      <div id='btnrbnCatagory' className='btn btn-sm' type='button'>
                        {translate('breakingnews')}
                      </div>
                      <h6 id='rbn-card-text' className='card-text'>
                        {element.title.slice(0, 40)}...
                      </h6>
                    </div>
                  </div>
                </Link>
              </div>
            ))}
        </div>
      ) : null}
    </div>
  )
}

export default RelatedBreakingNews
