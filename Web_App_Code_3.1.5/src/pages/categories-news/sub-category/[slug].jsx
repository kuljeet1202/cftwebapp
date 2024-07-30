
import dynamic from 'next/dynamic'
import Meta from 'src/components/seo/Meta'
const SubCategory = dynamic(() => import('src/components/newsType/News/SubCategoryNews'), { ssr: false })
const index = () => {
  return (
    <>
      <Meta title='sub categories news' description='' keywords='' ogImage='' pathName='' schema='' />
      <SubCategory />
    </>
  )
}

export default index
