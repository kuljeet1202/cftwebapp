import dynamic from 'next/dynamic'
import Meta from 'src/components/seo/Meta'
const TagNewsview = dynamic(() => import('src/components/tag/TagNewsview'), { ssr: false })
const index = () => {
  return (
    <>
      <Meta title='tag' description='' keywords='' ogImage='' pathName='' schema='' />
      <TagNewsview />
    </>
  )
}

export default index
