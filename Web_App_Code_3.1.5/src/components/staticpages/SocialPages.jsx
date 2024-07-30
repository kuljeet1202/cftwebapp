'use client'
import React, { use, useEffect, useState } from 'react'
import Layout from '../../components/layout/Layout.jsx'
import BreadcrumbNav from 'src/components/breadcrumb/BreadcrumbNav.jsx'
import { translate, NoDataFound } from '../../utils'
import { useRouter } from 'next/router.js'
import { getpagesApi } from 'src/hooks/getPagesApi'
import { useQuery } from '@tanstack/react-query'
import { access_key, getLanguage } from 'src/utils/api'
import { selectCurrentLanguage } from '../../store/reducers/languageReducer'
import { useSelector } from 'react-redux'
// import NoDataFound from 'src/components/noDataFound/NoDataFound.jsx'
import Skeleton from 'react-loading-skeleton'
import { getMorePagesData, loadMorePages } from 'src/store/reducers/MorePagesReducers.js'

const SocialPages = () => {
  const router = useRouter()

  const [isLoading, setIsLoading] = useState(true)
  const [Data, setData] = useState([])

  const MorePagesData = useSelector(getMorePagesData)
  let { id: language_id } = getLanguage()
  const currentLanguage = useSelector(selectCurrentLanguage)

  useEffect(() => {

    if (MorePagesData.length === 0 && currentLanguage.id) {
      loadMorePages({
        onSuccess: (res) => {
          setData(res.data)
          setIsLoading(false)
        },
        onError: (err) => {
          console.log(err)
          setIsLoading(false)

        }
      })
    }
    else {
      // Check if router.query.slug exists and MorePagesData is not empty
      if (router.query.slug && MorePagesData.length > 0) {
        // Find the page object with matching slug
        const page = MorePagesData.find(page => page.slug === router.query.slug)
        // If a matching page is found, set its data
        if (page) {
          setData([page])
          setIsLoading(false)
        } else {
          // If no matching page is found, handle accordingly (e.g., show a not found message)
          setIsLoading(false)
          setData([])
        }
      }

    }

  }, [router.query.slug, MorePagesData, currentLanguage])

  return (
    <Layout>
      <BreadcrumbNav SecondElement={translate('More Pages')} ThirdElement={router?.query?.slug} />
      <section className='morePagesSlugPage container'>
        <div className="row">
          <div className="col-12">
            <div className="contentWrapper">
              {isLoading ?
                <Skeleton height={400} />
                :
                <div>
                  {Data && Data[0]?.page_content ?
                    <div
                      id='pp-modal-body'
                      className='p-3 mb-0'
                      dangerouslySetInnerHTML={{ __html: Data && Data[0]?.page_content }}
                    ></div>
                    :
                    <>
                      {NoDataFound()}
                     
                    </>
                  }
                </div>
              }

            </div>
          </div>
        </div>
      </section>
    </Layout>
  )
}

export default SocialPages