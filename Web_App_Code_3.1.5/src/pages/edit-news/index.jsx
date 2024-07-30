import dynamic from 'next/dynamic'
import Meta from 'src/components/seo/Meta'
const EditNewsNoSSR = dynamic(() => import('src/components/role/EditNews'), { ssr: false })

const index = () => {
  return (
    <>
      <Meta title='edit news' description='' keywords='' ogImage='' pathName='' schema='' />
      <EditNewsNoSSR />
    </>
  )
}

export default index
