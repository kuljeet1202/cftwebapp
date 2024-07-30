'use client'
import Link from 'next/link'
import { selectCurrentLanguage } from '../../store/reducers/languageReducer'
import { useSelector } from 'react-redux'
import { translate } from '../../utils'
import Skeleton from 'react-loading-skeleton'
import { getTagApi } from 'src/hooks/tagsApi'
import { access_key, getLanguage } from 'src/utils/api'
import { useQuery } from '@tanstack/react-query'

const TagsSection = () => {
  const currentLanguage = useSelector(selectCurrentLanguage)
  let { id: language_id } = getLanguage()

  // api call
  const getTag = async () => {
    try {
      const { data } = await getTagApi.getTag({ access_key: access_key, language_id: language_id })
      return data.data
    } catch (error) {
      console.log(error)
    }
  }

  // react query
  const { isLoading, data: Data } = useQuery({
    queryKey: ['getTagSection', currentLanguage],
    queryFn: getTag
  })

  return (
    <div>
      {isLoading ? (
        <div>
          <Skeleton height={200} count={3} />
        </div>
      ) : Data && Data.length > 0 ? (
        <div id='rns-tags-main' className='my-3'>
          <div id='tags-nav' className='navbar'>
            <h4 id='rns-nav-logo' className='mb-0'>
              <b>{translate('tagLbl')}</b>
            </h4>
          </div>
          <div id='tags-tag'>
            {Data &&
              Data?.map(element => (
                <Link id='btnTags' key={element.id} href={`/tag/${element.slug}`} className='btn btn-outline-dark'>
                  {/* {console.log(element.slug)} */}
                  {element.tag_name}
                </Link>
              ))}
          </div>
        </div>
      ) : null}
    </div>
  )
}

export default TagsSection
