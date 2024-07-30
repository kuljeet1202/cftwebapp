'use client'
import React, { useEffect, useState } from 'react'
import { deleteNewsApi } from '../../store/actions/campaign'
import { useSelector } from 'react-redux'
import BreadcrumbNav from '../breadcrumb/BreadcrumbNav'
import { translate } from '../../utils'
import Skeleton from 'react-loading-skeleton'
import { useRouter } from 'next/navigation'
import { loadManageToEdit } from '../../store/reducers/createNewsReducer'
import { selectCurrentLanguage } from '../../store/reducers/languageReducer'
import { getNewsApi } from 'src/hooks/newsApi'
import { useQuery } from '@tanstack/react-query'
import { access_key, getLanguage } from 'src/utils/api'
import toast from 'react-hot-toast'
import Layout from '../layout/Layout'
import { Modal } from 'antd';
import NoDataFound from '../noDataFound/NoDataFound'
const { confirm } = Modal;

const ManageNews = () => {
  const navigate = useRouter()
  const [Data, setData] = useState([])
  const currentLanguage = useSelector(selectCurrentLanguage)
  let { id: language_id } = getLanguage()

  // api call
  const getNews = async () => {
    try {
      const { data } = await getNewsApi.getNews({
        access_key: access_key,
        get_user_news: 1,
        language_id: language_id,
        latitude: null,
        longitude: null
      })
      setData(data.data)
      // console.log('manage-news :',data)
      return data.data
    } catch (error) {
      console.log(error)
    }
  }

  // react query
  const { isLoading, isError, data, error, status } = useQuery({
    queryKey: ['getnews', currentLanguage],
    queryFn: getNews,

  })


  // type return
  const typeReturn = type => {
    if (type === 'video_upload') {
      return translate('videoUploadLbl')
    } else if (type === 'standard_post') {
      return translate('stdPostLbl')
    } else if (type === 'video_youtube') {
      return translate('videoYoutubeLbl')
    } else if (type === 'video_other') {
      return translate('videoOtherUrlLbl')
    }
  }

  const editNews = data => {
    loadManageToEdit(data)
    navigate.push(`/edit-news`)
  }

  const deleteNews = data => {
    confirm({
      title: 'Do you want to delete these news?',
      centered: true,
      async onOk() {
        try {
          await new Promise((resolve, reject) => {
            deleteNewsApi({
              news_id: data.id,
              onSuccess: res => {
                toast.success(res.message)
                const updatedData = Data.filter(item => item.id !== data.id)
                setData(updatedData)
              },
              onError: err => {
                toast.error(err.message)
              }
            })
            setTimeout(Math.random() > 0.5 ? resolve : reject, 1000);
          });
        } catch (e) {
          console.log('Oops errors!');
        }
      },
      onCancel() { },
    });

  }

  return (
    <Layout>
      <BreadcrumbNav SecondElement={translate('manageNewsLbl')} ThirdElement='0' />

      <div className='manage_news bg-white py-5'>
        <div className='container'>
          <div className='row'>
            {isLoading ? (
              <div>
                <Skeleton height={200} count={3} />
              </div>
            ) : (
              <>
                {Data && Data.length > 0 ? (
                  Data.map((element, id) => (
                    <div className=' col-xl-4 col-md-6 col-12' key={id}>
                      <div className='manage-data'>
                        <div className='manage-card'>
                          <div className='manage-img'>
                            <img
                              src={element.image}
                              alt='manage news'
                              onClick={() =>
                                navigate.push({
                                  pathname: `/news/${element.slug}`,

                                })
                              }
                            />
                          </div>
                          <div className='manage-title'>
                            <p
                              onClick={() =>
                                navigate.push({
                                  pathname: `/news/${element.slug}`,

                                })
                              }
                            >
                              {element.category_name}
                            </p>
                          </div>
                          <div className='manage-date'>
                            <p>
                              {new Date(element.date).toLocaleTimeString([], {
                                hour: 'numeric',
                                minute: 'numeric',
                                second: 'numeric',
                                hour12: true
                              })}
                            </p>
                          </div>
                        </div>
                        <div className='manage-right'>
                          <div className='manage-title'>
                            <p
                              className='mb-0'
                              onClick={() =>
                                navigate.push({
                                  pathname: `/news/${element.slug}`,

                                })
                              }
                            >
                              {element.title}
                            </p>
                          </div>
                          <div className='manage_type'>
                            <p className='mb-1'>
                              {translate('contentTypeLbl')} : <span>{typeReturn(element.content_type)}</span>
                            </p>
                          </div>
                          <div className='manage-tag'>
                            {element?.tag_name?.includes(',') ? (
                              element?.tag_name?.split(',').map((tagName, index) => (
                                <p key={index} onClick={() => navigate.push(`/tag/${element.tag_id}`)}>
                                  {tagName}
                                </p>
                              ))
                            ) : (
                              <p onClick={() => navigate.push(`/tag/${element.tag_id}`)}>{element.tag_name}</p>
                            )}
                          </div>
                          <div className='manage-buttons'>
                            <div className='manage-button-edit'>
                              <button className='btn btn-dark' onClick={e => editNews(element)}>
                                {translate('editLbl')}
                              </button>
                            </div>
                            <div className='manage-button-delete'>
                              <button className='btn btn-dark' onClick={e => deleteNews(element)}>
                                {translate('deleteTxt')}
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  ))
                ) : (
                  <>
                    {NoDataFound()}
                   
                  </>
                )}
              </>
            )}
          </div>
        </div>
      </div>
    </Layout>
  )
}

export default ManageNews
