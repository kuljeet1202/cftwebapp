'use client'
import { useRouter } from 'next/navigation'
import { useDispatch, useSelector } from 'react-redux'
import { selectCurrentLanguage, selectCurrentLanguageLabels } from '../../store/reducers/languageReducer'
import SwiperCore, { Navigation, Pagination } from 'swiper'
import { Swiper, SwiperSlide } from 'swiper/react'
import 'swiper/swiper-bundle.css'
import { formatDate, placeholderImage, translate } from '../../utils'
import Skeleton from 'react-loading-skeleton'
import { locationData, settingsData } from '../../store/reducers/settingsReducer'
import { access_key, getLanguage } from 'src/utils/api'
import { useEffect, useState } from 'react'
import { categoriesCacheData, loadSubCategoriesApi } from 'src/store/reducers/CatNavReducers'


SwiperCore.use([Navigation, Pagination])

const CatNav = () => {

  const navigate = useRouter()
  const currentLanguage = useSelector(selectCurrentLanguage)
  const categories = useSelector(categoriesCacheData)
  const categoiresOnOff = useSelector(settingsData)
  const [catId, setCatId] = useState('')
  const [subCatSlug, setSubCatSlug] = useState('')
  const [subCatData, setSubCatData] = useState([])
  const [isLoading, setIsLoading] = useState(false)
  const [subLoading, setSubLoading] = useState(true)

  const dispatch = useDispatch();

  const swiperOption = {
    loop: categories && categories.length > 10 ? true : false,
    speed: 3000,
    spaceBetween: 10,
    slidesPerView: 'auto',
    navigation: false,
    freeMode: true,
    observer: true,
    observeParents: true,
    parallax: true,
    breakpoints: {
      1200: {
        slidesPerView: 11
      }
    },
    autoplay: {
      delay: 0
    }
  }

  const handleCategoryChange = (categories) => {
    // console.log(categories.sub_categories)
    if (categories.slug) {
      // navigate.push(`/categories-news/${categories.slug}?category_id=${categories.id}`)
      navigate.push(`/categories-news/${categories.slug}`)
      setSubCatDrop(false)
    }
  }

  const handleSubCategoryChange = () => {
    // console.log(categories.sub_categories)
    if (subCatSlug) {
      navigate.push(`/categories-news/sub-category/${subCatSlug}`)
      setSubCatDrop(false)
    }
  }

  const [currentPage, setCurrentPage] = useState(0)
  const dataPerPage = 8 // number of posts per page

  const changelanguage = useSelector(selectCurrentLanguageLabels)
  const location = useSelector(locationData)

  let { id: language_id } = getLanguage()
  const storedLatitude = location && location.lat
  const storedLongitude = location && location.long

  // slice the array to get the current posts
  const currentData = subCatData && subCatData.data && subCatData.data.slice(0, dataPerPage)

  const lengthdata = (subCatData && subCatData.total) || 0


  const [subCatDrop, setSubCatDrop] = useState(false)
  const [currentCategory, setCurrentCategory] = useState([])

  const handleSubCatDropdown = (category) => {
    setCurrentCategory(category)
    setCatId(category.id)
    setSubCatDrop(true)
    setSubCatSlug('')
  }

  useEffect(() => {

    // console.log(currentCategory, 'currCat')
    // console.log(currentCategory.sub_categories?.map((e) => {
    //   return e.subcategory_name
    // }), "subbbbcaaa")

  }, [currentCategory])

  return (
    <>
      {categoiresOnOff && categoiresOnOff.category_mode === '1' ? (
        <>
          {categories && categories.length > 0 ? (
            <div id='cn-main' expand='lg'>
              <div className='container py-2'>
                {isLoading ? (
                  <div>
                    <Skeleton height={200} count={3} />
                  </div>
                ) : (
                  <div className={`cn-main-div ${categories && categories.length > 10 ? 'flex-display' : 'block-display'}`}>

                    <Swiper {...swiperOption}>
                      {categories.map((element, index) => (
                        // element.sub_categories.length > 0 ? (
                          
                          <SwiperSlide key={element.id} className='text-center'
                          onClick={() => handleCategoryChange(element)}
                          >
                            <span

                              className='catNav-links'

                            >
                              <b>{element.category_name}</b>
                            </span>
                          </SwiperSlide>
                        // ) : null
                      ))}
                    </Swiper>
                    {categories?.length > 10 ? (
                      <button
                        id='catNav-more'
                        onClick={() => {
                          navigate.push('/all-categories')
                        }}
                      >
                        {translate('More >>')}
                      </button>
                    ) : null}
                  </div>
                )}
              </div>
            </div>
          ) : null}
        </>
      ) : null}
    </>
  )
}

export default CatNav
