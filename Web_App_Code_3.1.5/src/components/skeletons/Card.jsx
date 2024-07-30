import React from 'react'
import Skeleton from 'react-loading-skeleton'

const Card = () => {
  return (
    <div className='card is-loading mb-4 border-0'>
      <div className='image'>
        <Skeleton height={200} />
      </div>
      <div className='content'>
        <h2>
          <Skeleton height={20} count={1} />
        </h2>
        <p>
          <Skeleton height={15} count={3} />
        </p>
      </div>
    </div>
  )
}

export default Card
