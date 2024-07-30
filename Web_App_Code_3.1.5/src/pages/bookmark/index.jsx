import dynamic from 'next/dynamic'
import Meta from 'src/components/seo/Meta'
const BookmarkSection = dynamic(() => import('src/components/bookmark/BookmarkSection'), { ssr: false })
const index = () => {
  return (
    <>
      <Meta title='bookmark' description='' keywords='' ogImage='' pathName='' schema='' />
      <BookmarkSection />
    </>
  )
}

export default index
