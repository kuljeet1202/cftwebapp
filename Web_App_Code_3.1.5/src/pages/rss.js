'use client'
import generateRssFeed from '../../scripts/generaterRssFeed.mjs'

export const getStaticProps = async () => {
  await generateRssFeed()
  return {
    props: {}
  }
}

const rss = () => {

  return <div>rss</div>
}

export default rss
