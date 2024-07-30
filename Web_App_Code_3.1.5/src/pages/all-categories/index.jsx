import axios from 'axios'
import dynamic from 'next/dynamic'
import Meta from 'src/components/seo/Meta'
import { extractJSONFromMarkup } from 'src/utils'
import { GET_WEB_SEO_PAGES, access_key } from 'src/utils/api'

const Categories = dynamic(() => import('src/components/categories/Categories'), { ssr: false })

// This is seo api
const fetchDataFromSeo = async () => {
  try {
    const response = await axios.get(
      `${process.env.NEXT_PUBLIC_API_URL}/${process.env.NEXT_PUBLIC_END_POINT}/${GET_WEB_SEO_PAGES}?access_key=${access_key}&type=all_categories`
    )
    const data = response.data
    return data
  } catch (error) {
    console.error('Error fetching data:', error)
    return null
  }
}

const Index = ({ seoData, currentURL }) => {
  let schema = null

  if (seoData && seoData.data && seoData.data.length > 0 && seoData.data[0].schema_markup) {
    const schemaString = seoData.data[0].schema_markup
    schema = extractJSONFromMarkup(schemaString)
  }
  return (
    <>
      <Meta
        title={seoData?.data && seoData.data.length > 0 && seoData.data[0].meta_title}
        description={seoData?.data && seoData.data.length > 0 && seoData.data[0].meta_description}
        keywords={seoData?.data && seoData.data.length > 0 && seoData.data[0].meta_keyword}
        ogImage={seoData?.data && seoData.data.length > 0 && seoData.data[0].image}
        pathName={currentURL}
        schema={schema}
      />
      <Categories />
    </>
  )
}

// let serverSidePropsFunction = null
// if (process.env.NEXT_PUBLIC_SEO === 'true') {
//   serverSidePropsFunction = async context => {
//     // Retrieve the slug from the URL query parameters
//     const { req } = context // Extract query and request object from context
//     const currentURL = req[Symbol.for('NextInternalRequestMeta')].initURL

//     const seoData = await fetchDataFromSeo()

//     // Pass the fetched data as props to the page component
//     return {
//       props: {
//         seoData,
//         currentURL
//       }
//     }
//   }
// }

let serverSidePropsFunction = null;
if (process.env.NEXT_PUBLIC_SEO === "true") {
    serverSidePropsFunction = async (context) => {
        const { req } = context; // Extract query and request object from context

        // const currentURL = `${req.headers.host}${req.url}`;
        const currentURL = process.env.NEXT_PUBLIC_WEB_URL + '/all-categories/';
        const seoData = await fetchDataFromSeo(req.url);
        // Pass the fetched data as props to the Index component

        return {
            props: {
                seoData,
                currentURL,
            },
        };
    };
}


export const getServerSideProps = serverSidePropsFunction

export default Index
