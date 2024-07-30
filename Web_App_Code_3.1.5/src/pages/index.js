import dynamic from 'next/dynamic'
import Meta from 'src/components/seo/Meta'
const Home = dynamic(() => import('src/components/home/Home'), { ssr: false })
const Index = () => {
  return (
    <>
      <Meta />
      <Home />
    </>
  )
}

export default Index
