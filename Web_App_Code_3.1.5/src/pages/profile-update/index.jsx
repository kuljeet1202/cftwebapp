import dynamic from 'next/dynamic'
import Meta from 'src/components/seo/Meta'
const ProfileUpdate = dynamic(() => import('src/components/profile/ProfileUpdate'), { ssr: false })
const index = () => {
  return (
    <>
      <Meta title='profile update' description='' keywords='' ogImage='' pathName='' schema='' />
      <ProfileUpdate />
    </>
  )
}

export default index
