
import dynamic from 'next/dynamic'
import Meta from 'src/components/seo/Meta'
const CategoryNews = dynamic(() => import('src/components/newsType/News/CategoryNews'), { ssr: false })
const index = () => {
  return (
    <>
      <Meta title='categories news' description='' keywords='' ogImage='' pathName='' schema='' />
      <CategoryNews />
    </>
  )
}

export default index
