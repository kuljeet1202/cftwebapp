'use client'
import React, { useState, useEffect } from 'react'
import Button from 'react-bootstrap/Button'
import Modal from 'react-bootstrap/Modal'
import ReactPlayer from 'react-player'
import { AiOutlineClose } from 'react-icons/ai'
import VideoPlayer from './HLSPlayer'
import { placeholderImage } from 'src/utils'

const VideoPlayerModal = props => {

  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    setIsLoading(true)

    const timer = setTimeout(() => {
      setIsLoading(false)
    }, 2000)

    return () => clearTimeout(timer)
  }, [props.show])

  // Function to check if the URL has an HLS or M3U8 extension
  const isHLSUrl = url => {
    return url?.endsWith('.m3u8')
  }



  return (
    <Modal {...props} className='video_modal' size='xl' aria-labelledby='contained-modal-title-vcenter' centered>
      <Modal.Body id='vps-modal-body'>
        {isLoading ? (
          <div className='loader-container'>
            <div className='loader'></div>
          </div>
        ) : (
          <>
            <Button id='vps-modal-btnclose' onClick={props.onHide}>
              <AiOutlineClose id='btnClose-logo' size={20} />
            </Button>
            {isHLSUrl(props.url) ? (
              <VideoPlayer url={props.url} />
            ) : props.type_url === 'video_other' || props.type_url === 'url_other' ? (
              <iframe
                className='video_other_url'
                allow='autoplay'
                frameborder='0'
                width='100%'
                allowFullScreen
                src={props.url}
                onError={placeholderImage}
              ></iframe>
            ) : (
              <ReactPlayer width='100%' height='500px' url={props.url && props.url} controls={true} />
            )}
          </>
        )}
      </Modal.Body>
    </Modal>
  )
}

export default VideoPlayerModal
