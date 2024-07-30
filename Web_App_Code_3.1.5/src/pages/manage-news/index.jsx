import dynamic from 'next/dynamic'
import Meta from 'src/components/seo/Meta'
const ManageNews = dynamic(() => import('src/components/role/ManageNews'), { ssr: false })
const index = () => {
  return (
    <>
      <Meta title='manage news' description='' keywords='' ogImage='' pathName='' schema='' />
      <ManageNews />
    </>
  )
}

export default index
