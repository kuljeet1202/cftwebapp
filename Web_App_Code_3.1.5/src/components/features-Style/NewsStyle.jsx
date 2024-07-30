import Link from 'next/link'
import { HiOutlineArrowLongRight } from 'react-icons/hi2'
import { placeholderImage, translate, truncateText } from '../../utils'

const NewsStyle = ({ Data }) => {

    return (
        <>
            {/* news */}
            {Data ? (
                <div id='rns-main' className='news_style_four'>
                    <div className='container'>
                        <div className='row'>
                            <div id='rns-cards-main' className=''>
                                {/* <div id='rns-head-main' className=''>
                                    <div className='left-sec'>
                                        <h4 id='rns-main-logo' className='mb-0'>
                                            {Data.title}
                                        </h4>
                                        <div className='short_desc'>{Data && Data.short_description}</div>
                                    </div>  

                                    <Link id='rns-Viewmore' href={`/view-all/${Data.slug}`} onClick={() => scrollToTop()}>
                                        {translate('viewMore')}
                                    </Link>
                                </div> */}

                                <div className='row mt-5 mb-5'>
                                    {Data && Data?.map((value, index) => {
                                        return (
                                            <div className='col-xxl-3 col-lg-4 col-md-4 col-sm-6 col-12' key={value.id}>
                                                <Link id='rns-card' className='card card_hover_two' href={{ pathname: `/news/${value.slug}`, }}>
                                                    <div className='banner_thumb'>
                                                        <img
                                                            id='rns-image'
                                                            src={value.image}
                                                            className='card-img-top'
                                                            alt={value.title}
                                                            onError={placeholderImage}
                                                        />
                                                    </div>
                                                    <div id='rns-img-overlay' className=' card-inverse'>
                                                        <div id='btnrnsCatagory' className='btn btn-sm' type='button'>
                                                            {truncateText(value?.category?.category_name, 10)}
                                                        </div>
                                                    </div>
                                                    <div id='rns-card-body' className='card-block'>
                                                        <p className='card-title newsStyleTitle mt-3'>{value.title.length > 20 ? <>{value.title.slice(0, 20)}...</> : value.title}</p>
                                                        <p id='btnrnsRead' className='btn overlay mt-2' type='button'>
                                                            {translate('readmore')}
                                                            <HiOutlineArrowLongRight id='rns-arrow' size={20} />
                                                        </p>
                                                    </div>
                                                </Link>
                                            </div>
                                        )
                                    })}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ) : null}
        </>
    )
}

export default NewsStyle
