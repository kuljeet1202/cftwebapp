import Head from 'next/head'

const Meta = ({ title, description, keywords, ogImage, pathName, schema }) => {
  return (
    <>
      <Head>
        {/* title */}
        <title>{title ? title : process.env.NEXT_PUBLIC_TITLE}</title>

        {/*<!-- Google / Search Engine Tags -->*/}
        <meta name='name' content={process.env.NEXT_PUBLIC_WEB_URL} />
        <meta name='description' content={description ? description : process.env.NEXT_PUBLIC_DESCRIPTION} />
        <meta name='keywords' content={keywords ? keywords : process.env.NEXT_PUBLIC_kEYWORDS} />
        <meta name='image' content={ogImage ? ogImage : null} />

        {/*<!-- Facebook Meta Tags -->*/}
        <meta property='og:title' content={title ? title : process.env.NEXT_PUBLIC_TITLE} />
        <meta property='og:description' content={description ? description : process.env.NEXT_PUBLIC_DESCRIPTION} />
        <meta property='og:image' content={ogImage ? ogImage : null} />
        <meta property='og:image:type' content='image/jpg' />
        <meta property='og:image:width' content='1080' />
        <meta property='og:image:height' content='608' />
        <meta property='og:url' content={pathName ? pathName : process.env.NEXT_PUBLIC_WEB_URL} />
        <meta property='og:type' content='website' />
        <meta property='og:locale' content='en_US' />
        <meta property='og:site_name' content={process.env.NEXT_PUBLIC_WEB_URL} />

        {/*<!-- Twitter Meta Tags -->*/}
        <meta name='twitter:title' content={title ? title : process.env.NEXT_PUBLIC_TITLE} />
        <meta name='twitter:description' content={description ? description : process.env.NEXT_PUBLIC_DESCRIPTION} />
        <meta name='twitter:image' content={ogImage ? ogImage : null} />
        <meta name='twitter:card' content='summary_large_image' />

        {/* robot and cononical */}
        <link rel='canonical' href={`${process.env.NEXT_PUBLIC_WEB_URL}`} />
        <meta name='viewport' content='width=device-width, initial-scale=1.0' />
        <meta name='robots' content='index, follow,max-snippet:-1,max-video-preview:-1,max-image-preview:large' />

        {/* schemas */}
        <script
          key='structured-data'
          type='application/ld+json'
          dangerouslySetInnerHTML={{ __html: JSON.stringify(schema ? schema : null) }}
        />
      </Head>
      <h1 className='d-none'>{title ? title : process.env.NEXT_PUBLIC_TITLE}</h1>
    </>
  )
}

export default Meta
