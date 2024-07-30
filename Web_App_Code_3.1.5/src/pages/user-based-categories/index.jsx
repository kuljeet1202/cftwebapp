import dynamic from 'next/dynamic'
import Meta from 'src/components/seo/Meta'

const UserBasedCategoriesNoSSR = dynamic(() => import('src/components/categories/UserBasedCategories'), { ssr: false })

const index = () => {
  return (
    <>
      <Meta title='user based categories' description='' keywords='' ogImage='' pathName='' schema='' />
      <UserBasedCategoriesNoSSR />
    </>
  )
}

export default index
