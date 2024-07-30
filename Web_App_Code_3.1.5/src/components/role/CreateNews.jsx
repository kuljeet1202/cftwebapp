'use client'
import React, { useEffect, useState } from 'react'
import BreadcrumbNav from '../breadcrumb/BreadcrumbNav'
import { translate } from '../../utils'
import { Button, Form } from 'react-bootstrap'
import DatePicker from 'react-datepicker'
import { AiFillPicture, AiOutlineUpload } from 'react-icons/ai'
import { SlCalender } from 'react-icons/sl'
import { getSubcategoryByCategoryApi, setNewsApi } from '../../store/actions/campaign'
import { selectLanguages } from '../../store/reducers/languageReducer'
import { useSelector } from 'react-redux'
import { selectcreateNewsCurrentLanguage, setCreateNewsCurrentLanguage } from '../../store/reducers/createNewsReducer'
import { Alert, Select, Space } from 'antd'
import Dropzone from 'react-dropzone'
import SwiperCore, { Navigation, Pagination } from 'swiper'
import { Swiper, SwiperSlide } from 'swiper/react'
import ReactQuill from 'react-quill'
import { useRouter } from 'next/navigation'
import { settingsData } from '../../store/reducers/settingsReducer'
import createnewsimage from '../../../public/assets/images/Create-news.svg'
import { getTagApi } from 'src/hooks/tagsApi'
import { access_key, getLanguage } from 'src/utils/api'
import { useQuery } from '@tanstack/react-query'
import { CategoriesApi } from 'src/hooks/categoriesApi'
import { getlocationapi } from 'src/hooks/getlocationApi'
import toast from 'react-hot-toast'
import Layout from '../layout/Layout'
import { Input } from 'antd'
const { TextArea } = Input

const { Option } = Select
SwiperCore.use([Navigation, Pagination])

const CreateNews = () => {
  const [showCategory, setShowCategory] = useState(false)
  const [subCategory, setSubCategory] = useState([])
  const [showsubCategory, setShowsubCategory] = useState(false)
  const [showUrl, setShowURl] = useState(false)
  const [videoUrl, setVideoUrl] = useState(false)
  const [images, setImages] = useState([])
  const [nextStepScreen, setNextStepScreen] = useState(false)
  const [content, setContent] = useState('')
  const [otherUrl, setOtherUrl] = useState(false)
  const languagesData = useSelector(selectLanguages)
  const createNewsLanguage = useSelector(selectcreateNewsCurrentLanguage)
  const navigate = useRouter()
  const getLocation = useSelector(settingsData)
  const [DefaultValue, setDefualtValue] = useState({
    defualtTitle: null,
    defaultMetatitle: null,
    defaultMetaDescription: null,
    defaultMetaKeyword: null,
    defaultSlug: null,
    defualtLanguage: null,
    defualtCategory: null,
    defualtCategoryID: null,
    defualtSubCategory: null,
    defualtSubCategoryID: null,
    defaultSelector: null,
    defaultType: null,
    defualtTag: null,
    defaultTagName: null,
    defualtContent: null,
    defualtStartDate: null,
    defualtUrl: null,
    defaultVideoData: null,
    defaultImageData: null,
    defaultImagefile: null,
    defualtLocation: null
  })

  const [videoSet, setVideoSet] = useState(false)
  // other multiple image
  const handleDrop = acceptedFiles => {
    const imageFiles = acceptedFiles.filter(file => file.type.startsWith('image/'))

    if (acceptedFiles.length !== imageFiles.length) {
      // Some files are not images, show the error toast
      toast.error('Only image files are allowed.')
      return // Do not proceed with adding non-image files
    }

    // All files are images, add them to the state
    setImages([...images, ...imageFiles])
  }

  // description content
  const handleChangeContent = value => {
    setContent(value)
  }
  // load language data to reducer
  const languageSelector = async value => {
    setShowCategory(true)
    const selectedData = JSON.parse(value)
    setDefualtValue(prevState => ({...prevState, defualtLanguage: selectedData.language }));
    setCreateNewsCurrentLanguage(selectedData.language, selectedData.code, selectedData.id)
    setDefualtValue(prevState => ({ ...prevState, defualtCategoryID: null, defualtCategory: null }));
    setShowsubCategory(false)
  }

  // select category
  const categorySelector = (value, option) => {
    const categoryID = JSON.parse(value)
    setDefualtValue({ ...DefaultValue, defualtCategoryID: categoryID, defualtCategory: option.label })
    setSubCategory([]);
    getSubcategoryByCategoryApi({
      category_id: categoryID,
      onSuccess: res => {
        if(res.data.length === 0){
          setSubCategory([]);
          setShowsubCategory(false)
          return
        }
        setSubCategory(res.data)
        setShowsubCategory(true)
      },
      onError: err => {
        if (err === 'No Data Found') {
          setShowsubCategory(false)
          setSubCategory("")
        }
      }
    })
  }

  const subcategorySelector = (value, option) => {
    const subcategoryID = JSON.parse(value)
    setDefualtValue({ ...DefaultValue, defualtSubCategoryID: subcategoryID, defualtSubCategory: option.label })
  }

  // video selector
  const handleVideo = e => {
    if (e.target.files[0] && !e.target.files[0].type.includes('video')) {
      toast.error('Please select a video format')
      return true
    }
    setDefualtValue({ ...DefaultValue, defaultVideoData: e.target.files[0] })
    setVideoSet(true)
    // setVideoData(e.target.files[0]);
  }

  // select post type
  const postSelector = value => {
    // Find the selected option in the standardPost array
    const contentType = standardPost.find(elem => elem.id === value)
    if (contentType.name === 'standard_post') {
      setDefualtValue({
        ...DefaultValue,
        defaultSelector: translate('stdPostLbl'),
        defaultType: 'standard_post',
        defualtUrl: null
      })
      // setContentTypeData(contentType.name);
      setShowURl(false)
      setVideoUrl(false)
      setOtherUrl(false)
      // setDefualtValue({...DefaultValue,defualtUrl:null})
    } else if (contentType.name === 'video_youtube') {
      setDefualtValue({ ...DefaultValue, defaultSelector: translate('videoYoutubeLbl'), defaultType: 'video_youtube' })
      // setContentTypeData(contentType.name);
      setShowURl(true)
      setOtherUrl(false)
      setVideoUrl(false)
    } else if (contentType.name === 'video_other') {
      setDefualtValue({ ...DefaultValue, defaultSelector: translate('videoOtherUrlLbl'), defaultType: 'video_other' })
      // setContentTypeData(contentType.name);
      setShowURl(false)
      setOtherUrl(true)
      setVideoUrl(false)
    } else if (contentType.name === 'video_upload') {
      setDefualtValue({ ...DefaultValue, defaultSelector: translate('videoUploadLbl'), defaultType: 'video_upload' })
      // setContentTypeData(contentType.name);
      setShowURl(false)
      setVideoUrl(true)
      setOtherUrl(false)
    } else {
      setShowURl(false)
      setVideoUrl(false)
      setOtherUrl(false)
    }
  }

  // validate url
  const validateVideoUrl = urlData => {
    // eslint-disable-next-line
    const videoUrlPattern =
      /^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/((?:watch)\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]{11})/
    const shortsUrlPattern = /^(https?:\/\/)?(www\.)?(youtube\.com)\/(shorts)/
    if (videoUrlPattern.test(urlData)) {
      // URL is a YouTube video
      return true
    } else if (shortsUrlPattern.test(urlData)) {
      // URL is a YouTube Shorts video
      toast.error('YouTube Shorts are not supported')
      return false
    }
  }

  // next screen step 2
  const nextStep = e => {
    e.preventDefault()

    if (!DefaultValue.defualtTitle) {
      toast.error(translate("titlerequired"))
      return
    }

    if (!DefaultValue.defaultMetatitle){
      toast.error(translate("metaTitlerequired"))
      return
    }

    if (!DefaultValue.defaultMetaDescription){
      toast.error(translate("metaDescriptionrequired"))
      return
    }

    if(!DefaultValue.defaultMetaKeyword){
      toast.error(translate("metaKeywordsrequired"))
      return
    }

    if (!DefaultValue.defaultSlug){
      toast.error(translate("slugrequired"))
      return
    }
    if (!DefaultValue.defualtLanguage){
      toast.error(translate("selectlanguage"))
      return
    }
    if (!DefaultValue.defualtCategory){
      toast.error(translate("selectcategory"))
      return
    }

    if(!DefaultValue.defaultType){
      toast.error(translate("contentTyperequired"))
      return
    }


    if (DefaultValue.defaultType === 'video_upload') {
      setDefualtValue({ ...DefaultValue, defualtUrl: DefaultValue.defaultVideoData })
    }
    //category selector validation
    if (DefaultValue.defualtCategoryID === '') {
      toast.error(translate('plzSelCatLbl'))
      return
    }

    //url selector validation
    if (DefaultValue.defaultType === 'video_youtube') {
      const isYouTubeVideo = validateVideoUrl(DefaultValue.defualtUrl)
      if (!isYouTubeVideo) {
        // URL is not a YouTube video
        toast.error('URL is not a YouTube video')
        return
      }
    } else if (DefaultValue.defaultType === 'video_other') {
      const isYouTubeVideo = validateVideoUrl(DefaultValue.defualtUrl)
      if (isYouTubeVideo) {
        // YouTube videos are not supported for "video_other" content type
        toast.error('YouTube videos are not supported for this content type')
        return
      }
    }

    if (DefaultValue.defaultType === 'video_upload' && !videoSet) {
      // toast.error(translate('uploadMainImageLbl'))
      toast.error(translate('plzUploadVideoLbl'))
      return
    }
    // main image validation
    if (DefaultValue.defaultImageData === null) {
      toast.error(translate('uploadMainImageLbl'))
      return
    }
    // main image validation

    setNextStepScreen(true)
  }

  // api call
  const getCategories = async () => {
    try {
      const { data } = await CategoriesApi.getCategories({
        access_key: access_key,
        offset: '',
        limit: '70',
        language_id: createNewsLanguage.id
      })
      return data.data
    } catch (error) {
      if (error === 'No Data Found') {
        <span>{translate('nodatafound')}</span>
      }
    }
  }

  // api call
  const getTag = async () => {
    try {
      const { data } = await getTagApi.getTag({ access_key: access_key, language_id: createNewsLanguage.id })
      return data.data
    } catch (error) {
      console.log(error)
    }
  }

  // api call
  const getLocationlatlong = async () => {
    try {
      const { data } = await getlocationapi.getlocation({ access_key: access_key,limit: 10000 })
      // console.log(data,"locationss")
      return data.data
    } catch (error) {
      console.log(error)
    }
  }

  // react query
  const { data: category } = useQuery({
    queryKey: ['getcategories', createNewsLanguage],
    queryFn: getCategories
  })

  // react query
  const { data: tagsData } = useQuery({
    queryKey: ['getTag', access_key, createNewsLanguage.id],
    queryFn: getTag
  })

  const { data: locationOptions } = useQuery({
    queryKey: ['getlocation', access_key],
    queryFn: getLocationlatlong
  })

  const getLocationData = getLocation?.location_news_mode

  // create standard post
  const standardPost = [
    {
      id: 1,
      type: translate('stdPostLbl'),
      name: 'standard_post',
      param: 'empty'
    },
    {
      id: 2,
      type: translate('videoYoutubeLbl'),
      name: 'video_youtube',
      param: 'url'
    },
    {
      id: 3,
      type: translate('videoOtherUrlLbl'),
      name: 'video_other',
      param: 'url'
    },
    {
      id: 4,
      type: translate('videoUploadLbl'),
      name: 'video_upload',
      param: 'file'
    }
  ]

  // tag
  const handleChange = values => {
    let tagIds = values.map(value => {
      const selectedTag = tagsData.find(elem => elem.tag_name === value)
      if (selectedTag) {
        return selectedTag.id
      }
      return null
    })

    tagIds = tagIds.filter(tagId => tagId !== null).join(',')
    setDefualtValue(prevValue => {
      return {
        ...prevValue,
        defualtTag: tagIds,
        defaultTagName: values.join(',')
      }
    })
    //   setTagID((prevState) => ({ ...prevState, tagIds }));
  }

  // swiper other images
  const swiperOption = {
    loop: false,
    speed: 750,
    spaceBetween: 10,
    slidesPerView: 3.5,
    navigation: false,
    autoplay: false,
    breakpoints: {
      0: {
        slidesPerView: 2.5
      },

      768: {
        slidesPerView: 2.5
      },

      992: {
        slidesPerView: 3
      },
      1200: {
        slidesPerView: 3.5
      }
    }
  }

  // main image
  const handleMainImage = e => {
    const selectedFile = e.target.files[0]
    // Check if a file is selected
    if (!selectedFile) {
      return
    }

    // Check if the selected file type is an image
    if (!selectedFile.type.startsWith('image/')) {
      toast.error('Please select an image file.')
      return
    }

    e.preventDefault()
    const file = e.target.files[0]
    // setMainImageFile(file);
    setDefualtValue({ ...DefaultValue, defaultImageData: URL.createObjectURL(file), defaultImagefile: file })
    // setMainImage(URL.createObjectURL(file));
  }

  // meta description condition
  const handleMetaDescriptionChange = e => {
    const maxLength = 160
    const minLength = 50 // Set your desired maximum length
    // Check if the input value exceeds the maxLength or is less than the minLength
    if (DefaultValue.defaultMetaDescription.length > maxLength) {
      toast.error('Meta Description length should not exceed 160 characters')
      return false
    } else if (DefaultValue.defaultMetaDescription.length < minLength) {
      toast.error('Meta Description length should be at least 50 characters')
      return false
    }
    return true // Return true if the validation passes
  }

  // slug
  const slugConverter = () => {
    let slug = DefaultValue.defaultSlug
    slug = slug.replace(/[^a-zA-Z0-9-]/g, '-')
    slug = slug.replace(/-+/g, '-')
    slug = slug.replace(/^-+/, '')
    slug = slug.replace(/-+$/, '')
    return slug
  }

  // meta title condition
  const handleMetaTitleChange = () => {
    const maxLength = 60
    // Check if the input value exceeds the maxLength or is less than the minLength
    if (DefaultValue.defaultMetatitle.length > maxLength) {
      toast.error('Title length should not exceed 60 characters')
      return false
    }
    return true // Return true if the validation passes
  }

  // final submit data
  const finalSubmit = async e => {
    e.preventDefault()
    // meta title validation
    // const isMetaTitleValid = handleMetaTitleChange()
    // if (!isMetaTitleValid) {
    //   return // Stop execution if meta title validation fails
    // }

    // meta description validation
    // const isMetaDescriptionValid = handleMetaDescriptionChange()
    // if (!isMetaDescriptionValid) {
    //   return // Stop execution if meta description validation fails
    // }

    if(!content){
      toast.error(translate('descriptionisrequire'))
      return
    }

    const slugValue = await slugConverter()

    // console.log(slugValue,'slugvalue')

    await setNewsApi({
      action_type: 1,
      category_id: DefaultValue.defualtCategoryID,
      subcategory_id: DefaultValue.defualtSubCategoryID,
      tag_id: DefaultValue.defualtTag,
      title: DefaultValue.defualtTitle,
      meta_title: DefaultValue.defaultMetatitle,
      meta_description: DefaultValue.defaultMetaDescription,
      meta_keyword: DefaultValue.defaultMetaKeyword,
      slug: slugValue,
      content_type: DefaultValue.defaultType,
      content_data: DefaultValue.defualtUrl,
      description: content,
      image: DefaultValue.defaultImagefile,
      ofile: images,
      show_till: DefaultValue.defualtStartDate?.toISOString().split('T')[0],
      language_id: createNewsLanguage.id,
      location_id: DefaultValue.defualtLocation ? DefaultValue.defualtLocation : null,
      onSuccess: response => {
        toast.success(response.message)
        navigate.push('/manage-news')
      },
      onError: error => {
        toast.error(error)
      }
    })
  }

  // back button
  const Back = () => {
    setNextStepScreen(false)
  }

  return (
    <Layout>
      <BreadcrumbNav SecondElement={translate('createNewsLbl')} ThirdElement='0' />
      <div className='create_news py-5 bg-white'>
        <div className='container'>
          <div className='row'>
            <div className='col-md-7 col-12'>
              <img className='create-img' src={createnewsimage.src} alt='create news' />
            </div>

            <div className='col-md-5 col-12'>
              {!nextStepScreen ? (
                <Form onSubmit={e => nextStep(e)}>
                  <div className='form_title'>
                    <p className='mb-2'>{translate('createNewsLbl')}</p>
                    <span className='mb-2'>{translate('step1Of2Lbl')}</span>
                  </div>

                  <div className='form_details'>
                    <div className='input_form mb-2'>
                      <input
                        type='text'
                        placeholder={translate('titleLbl')}
                        defaultValue={DefaultValue.defualtTitle}
                        onChange={e => setDefualtValue({ ...DefaultValue, defualtTitle: e.target.value })}
                      />
                    </div>
                    <div className='input_form textarea mb-2'>
                      <TextArea
                        rows={2}
                        placeholder={translate('meta-title')}
                        // maxLength={2}
                        defaultValue={DefaultValue.defaultMetatitle}
                        onChange={e => setDefualtValue({ ...DefaultValue, defaultMetatitle: e.target.value })}
                      />
                      <Alert
                        closable
                        showIcon
                        className='mt-2'
                        message={translate('metaTitleWarningLbl')}
                        type='warning'
                      />
                    </div>
                    <div className='input_form mb-2'>
                      <TextArea
                        rows={2}
                        // maxLength={2}
                        placeholder={translate('meta-description')}
                        defaultValue={DefaultValue.defaultMetaDescription}
                        onChange={e => setDefualtValue({ ...DefaultValue, defaultMetaDescription: e.target.value })}
                      />
                      <Alert
                        closable
                        showIcon
                        className='mt-2'
                        message={translate('metaDescriptionWarningLbl')}
                        type='warning'
                      />
                    </div>
                    <div className='input_form mb-2'>
                      <TextArea
                        rows={2}
                        // maxLength={2}
                        placeholder={translate('meta-keywords')}
                        defaultValue={DefaultValue.defaultMetaKeyword}
                        onChange={e => setDefualtValue({ ...DefaultValue, defaultMetaKeyword: e.target.value })}
                      />
                      <Alert
                        closable
                        showIcon
                        className='mt-2'
                        message={translate('metaKeywordWarningLbl')}
                        type='warning'
                      />
                    </div>
                    <div className='input_form mb-2'>
                      <input
                        placeholder={translate('slug')}
                        defaultValue={DefaultValue.defaultSlug}
                        onChange={e => setDefualtValue({ ...DefaultValue, defaultSlug: e.target.value })}
                      />
                      <Alert closable showIcon className='mt-2' message={translate('slugWarningLbl')} type='warning' />
                    </div>
                    <div className='dropdown_form mb-2'>
                      <Select
                        style={{
                          width: '100%'
                        }}
                        defaultValue={DefaultValue.defualtLanguage}
                        placeholder={translate('chooseLanLbl')}
                        onChange={values => languageSelector(values)}
                        optionLabelProp='label'
                      >
                        {languagesData &&
                          languagesData.map((elem, id) => (
                            <Option value={JSON.stringify(elem)} key={id} label={elem.language}>
                              <Space> {elem.language}</Space>
                            </Option>
                          ))}
                      </Select>
                    </div>

                    {showCategory ? (
                      <div className='dropdown_form mb-2'>
                        <Select
                          style={{
                            width: '100%'
                          }}
                          defaultValue={DefaultValue.defualtCategory}
                          placeholder={translate('catLbl')}
                          onChange={(value, option) => categorySelector(value, option)}
                          optionLabelProp='label'
                        >
                          {category &&
                            category.map((elem, id) => (
                              <Option value={elem.id} key={id} label={elem.category_name}>
                                <Space> {elem.category_name}</Space>
                              </Option>
                            ))}
                        </Select>
                      </div>
                    ) : null}
                    {showsubCategory ? (
                      <div className='dropdown_form mb-2'>
                        <Select
                          style={{
                            width: '100%'
                          }}
                          defaultValue={DefaultValue.defualtSubCategory}
                          placeholder={translate('subcatLbl')}
                          onChange={(value, option) => subcategorySelector(value, option)}
                          optionLabelProp='label'
                        >
                          {subCategory &&
                            subCategory.map((elem, id) => (
                              <Option value={elem.id} key={id} label={elem.subcategory_name}>
                                <Space> {elem.subcategory_name}</Space>
                              </Option>
                            ))}
                        </Select>
                      </div>
                    ) : null}

                    {getLocationData === '1' ? (
                      <div className='dropdown_form mb-2'>
                        <Select
                          style={{
                            width: '100%'
                          }}
                          placeholder='Select Location'
                          onChange={value => setDefualtValue({ ...DefaultValue, defualtLocation: value })}
                        >
                          {locationOptions &&
                            locationOptions.map(location => (
                              <Select.Option key={location.id} value={location.id}>
                                {location.location_name}
                              </Select.Option>
                            ))}
                        </Select>
                      </div>
                    ) : null}

                    <div className='dropdown_form mb-2'>
                      <Select
                        style={{
                          width: '100%'
                        }}
                        defaultValue={DefaultValue.defaultSelector}
                        placeholder={translate('stdPostLbl')}
                        onChange={(value, option) => postSelector(value, option)}
                        optionLabelProp='label'
                      >
                        {standardPost &&
                          standardPost.map((elem, id) => (
                            <Option value={elem.id} key={id} label={elem.type}>
                              {elem.type}
                            </Option>
                          ))}
                      </Select>
                    </div>
                    {showUrl ? (
                      <div className='input_form mb-2'>
                        <input
                          type='text'
                          className='inputurl'
                          placeholder={translate('youtubeUrlLbl')}
                          defaultValue={DefaultValue.defualtUrl}
                          onChange={e => setDefualtValue({ ...DefaultValue, defualtUrl: e.target.value })}
                          required
                        />
                      </div>
                    ) : null}
                    {otherUrl ? (
                      <div className='input_form mb-2'>
                        <input
                          type='text'
                          className='inputurl'
                          placeholder={translate('otherUrlLbl')}
                          defaultValue={DefaultValue.defualtUrl}
                          onChange={e => setDefualtValue({ ...DefaultValue, defualtUrl: e.target.value })}
                          required
                        />
                      </div>
                    ) : null}
                    {videoUrl ? (
                      <div className='input_form mb-2 video_url'>
                        <input
                          type='file'
                          id='videoInput'
                          name='video'
                          accept='video/*'
                          onChange={e => handleVideo(e)}
                          // required
                        />
                        <label htmlFor='videoInput'>
                          {' '}
                          {DefaultValue.defaultVideoData?.name
                            ? DefaultValue.defaultVideoData?.name
                            : translate('uploadVideoLbl')}
                          <AiOutlineUpload className='uploadVideo' />
                        </label>
                        <input
                          type='file'
                          className='form-control'
                          id='videoInput'
                          name='video'
                          accept='video/*'
                          placeholder='Upload File'
                          disabled
                          hidden
                        />
                      </div>
                    ) : null}

                    <div className='dropdown_form mb-2'>
                      <Select
                        mode='multiple'
                        style={{
                          width: '100%'
                        }}
                        defaultValue={DefaultValue?.defaultTagName?.split(',')}
                        placeholder={translate('tagLbl')}
                        onChange={values => handleChange(values)}
                        optionLabelProp='label'
                      >
                        {tagsData &&
                          tagsData.map((elem, id) => (
                            <Option key={id} value={elem.tag_name} label={elem.tag_name}>
                              <Space>{elem.tag_name}</Space>
                            </Option>
                          ))}
                      </Select>
                    </div>
                    <div className='show_date mb-2'>
                      <DatePicker
                        dateFormat='yyyy-MM-dd'
                        selected={DefaultValue.defualtStartDate}
                        placeholderText={translate('showTilledDate')}
                        clearButtonTitle
                        todayButton={'Today'}
                        minDate={new Date()}
                        onChange={date => setDefualtValue({ ...DefaultValue, defualtStartDate: date })}
                      />
                      <SlCalender className='form-calender' />
                    </div>
                    <div className='main_image mb-2'>
                      <input
                        type='file'
                        name='image'
                        id='file'
                        accept='image/*'
                        className='file_upload'
                        onChange={e => handleMainImage(e)}
                      />

                      <label htmlFor='file'>
                        {' '}
                        <em>
                          {DefaultValue.defaultImageData ? null : <AiFillPicture />}{' '}
                          {DefaultValue.defaultImageData ? null : translate('uploadMainImageLbl')}
                        </em>
                      </label>
                      {DefaultValue.defaultImageData && (
                        <div className='mainimage'>
                          <img src={DefaultValue.defaultImageData} alt='mainimage' />
                        </div>
                      )}
                      <input
                        type='text'
                        className='form-control'
                        accept='image/*'
                        placeholder='Upload File'
                        id='file1'
                        name='myfile'
                        disabled
                        hidden
                      />
                    </div>
                    <div className='other_image mb-2'>
                      <Dropzone onDrop={handleDrop} multiple={true}>
                        {({ getRootProps, getInputProps }) => (
                          <div {...getRootProps()} className='dropzone'>
                            <input {...getInputProps()} className='' accept='image/*' />
                            <AiFillPicture className='me-1' />
                            {translate('uploadOtherImageLbl')}
                          </div>
                        )}
                      </Dropzone>
                    </div>
                    <div className='image_slider'>
                      <Swiper {...swiperOption}>
                        {images.map((file, index) => (
                          <SwiperSlide key={index}>
                            <img src={URL.createObjectURL(file)} alt={`Uploaded ${index}`} />
                          </SwiperSlide>
                        ))}
                      </Swiper>
                    </div>
                    <Button type='submit' className='btn btn-secondary next-btn'>
                      {translate('nxt')}
                    </Button>
                  </div>
                </Form>
              ) : (
                <Form onSubmit={e => finalSubmit(e)}>
                  <div className='form_title'>
                    <p className='mb-2'>{translate('createNewsLbl')}</p>
                    <span className='mb-2'>{translate('step2of2Lbl')}</span>
                  </div>
                  <div className='editor'>
                    <ReactQuill value={content} onChange={handleChangeContent} />
                  </div>
                  <div className='row'>
                    <div className='col-md-6'>
                      <Button type='button' className='btn btn-secondary backbtn' onClick={Back}>
                        {translate('back')}
                      </Button>
                    </div>
                    <div className='col-md-6'>
                      <Button type='submit' className=' btn btn-secondary subbtn'>
                        {translate('submitBtn')}
                      </Button>
                    </div>
                  </div>
                </Form>
              )}
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}

export default CreateNews