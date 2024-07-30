import React from 'react'
import { placeholderImage } from 'src/utils'

const AdSpaces = ({ ad_url, ad_img,style_web, }) => {
    return (
        <>
            <div className='ad_spaces'>
                <div className='container'>
                    <div target='_blank' onClick={() => window.open(ad_url, '_blank')}>
                        {ad_img && (
                            <img
                                className='adimage'
                                src={ad_img}
                                alt={`style ${style_web} feature sponsored ads news image`}
                                onError={placeholderImage}
                            />
                        )}
                    </div>
                </div>
            </div>
        </>
    )
}

export default AdSpaces
