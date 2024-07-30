'use client'
import React, { useState } from 'react'
import ReactPaginate from 'react-paginate'
import { IoArrowForwardCircleSharp } from 'react-icons/io5'
import { selectCurrentLanguage } from '../../store/reducers/languageReducer'
import { useSelector } from 'react-redux'
import BreadcrumbNav from '../breadcrumb/BreadcrumbNav'
import Link from 'next/link'
import { settingsData } from '../../store/reducers/settingsReducer'
import { NoDataFound, placeholderImage, translate } from '../../utils'
import { CategoriesApi } from 'src/hooks/categoriesApi'
import { access_key } from 'src/utils/api'
import { useQuery } from '@tanstack/react-query'
import Layout from '../layout/Layout'
import Card from '../skeletons/Card'
// import NoDataFound from '../noDataFound/NoDataFound'
import { categoriesCacheData } from 'src/store/reducers/CatNavReducers'

const Categories = () => {
  const [currentPage, setCurrentPage] = useState(0)
  const dataPerPage = 9
  const currentLanguage = useSelector(selectCurrentLanguage)



  const categoiresOnOff = useSelector(settingsData)

  // handle page change
  const handlePageChange = ({ selected }) => {
    setCurrentPage(selected)
  }

  // api call
  const categoriesApi = async page => {
    try {
      const { data } = await CategoriesApi.getCategories({
        access_key,
        offset: page * dataPerPage,
        limit: dataPerPage,
        language_id: currentLanguage.id
      })
      return data
    } catch (error) {
      console.log(error)
    }
  }

  // react query
  const { isLoading, data: Data } = useQuery({
    queryKey: ['categories', currentPage, currentLanguage.id],
    queryFn: () => categoriesApi(currentPage),
    staleTime: 6000
  })

  // slice the array to get the current posts
  const currentData = Data && Data.data && Data.data.slice(0, dataPerPage)

  const lengthdata = (Data && Data.total) || 0

  return (
    <Layout>
      <BreadcrumbNav SecondElement='Categories' ThirdElement='0' />
      {categoiresOnOff && categoiresOnOff.category_mode === '1' ? (
        <div className='container my-5'>
          {isLoading ? (
            <div className='row'>
              {[...Array(3)].map((_, index) => (
                <div className='col-md-4 col-12' key={index}>
                  <Card isLoading={true} />
                </div>
              ))}
            </div>
          ) : (
            <div className='row'>
              {currentData && currentData?.length > 0 ? (
                currentData.map(element => (
                  <div className='col-md-4 col-12 mb-4'>
                    <Link
                      id='cat-section-card'
                      key={element.id}
                      className='card'
                      href={{
                        pathname: `/categories-news/${element.slug}`,
                        // query: {
                        //   category_slug: element.slug
                        // }
                      }}
                    >
                      <img
                        id='cat-section-card-image'
                        src={element.image}
                        className='categories card news image'
                        alt={element.category_name}
                        onError={placeholderImage}
                      />
                      <div id='cat-section-card-body' className='card-img-overlay'>
                        <h5 id='cat-card-text' className='card-text mb-0'>
                          {element.category_name}
                        </h5>
                        <button id='btn-cat-more' className='btn' type='button'>
                          <IoArrowForwardCircleSharp size={40} />
                        </button>
                      </div>
                    </Link>
                  </div>
                ))
              ) : (
                <>
                {NoDataFound()}
               
              </>
              )}
              {lengthdata > 10 ? (
                <ReactPaginate
                  initialPage={currentPage}
                  previousLabel={translate('previous')}
                  nextLabel={translate('next')}
                  pageCount={Math.ceil(lengthdata / dataPerPage)}
                  onPageChange={handlePageChange}
                  containerClassName={'pagination'}
                  previousLinkClassName={'pagination__link'}
                  nextLinkClassName={'pagination__link'}
                  disabledClassName={'pagination__link--disabled'}
                  activeClassName={'pagination__link--active'}
                />
              ) : null}
            </div>
          )}
        </div>
      ) : (
        <>
          {NoDataFound()}
         
        </>
      )}
    </Layout>
  )
}

export default Categories
