import dynamic from 'next/dynamic'
import Meta from 'src/components/seo/Meta'
const CreateNewsNoSSR = dynamic(() => import('src/components/role/CreateNews'), { ssr: false })

const index = () => {
  return (
    <>
      <Meta title='create news' description='' keywords='' ogImage='' pathName='' schema='' />
      <CreateNewsNoSSR />
    </>
  )
}

export default index
