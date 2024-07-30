import dynamic from 'next/dynamic'
import Meta from 'src/components/seo/Meta'
const VideoNewsview = dynamic(() => import('src/components/newsType/VideoNews/VideoNewsview'), { ssr: false })
const index = () => {
  return (
    <>
      <Meta title='video news' description='' keywords='' ogImage='' pathName='' schema='' />
      <VideoNewsview />
    </>
  )
}

export default index
