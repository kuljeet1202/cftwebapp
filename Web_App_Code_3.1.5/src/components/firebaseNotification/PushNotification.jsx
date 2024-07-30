'use client'
import React, { useEffect, useState } from 'react'
import 'firebase/messaging'
import { toast } from 'react-hot-toast'
import { useSelector } from 'react-redux'
import { locationData } from 'src/store/reducers/settingsReducer'
import FirebaseData from 'src/utils/Firebase'

const PushNotificationLayout = ({ children }) => {
  const [notification, setNotification] = useState(null)
  const [userToken, setUserToken] = useState(null)
  const [isTokenFound, setTokenFound] = useState(false)
  const [fcmToken, setFcmToken] = useState('')
  const { fetchToken, onMessageListener } = FirebaseData()

  const getfcmToken = useSelector(locationData)

  useEffect(() => {
    handleFetchToken()
  }, [])

  const handleFetchToken = async () => {
    await fetchToken(setTokenFound, setFcmToken)

  }

  useEffect(() => {
    if (typeof window !== undefined) {
      setUserToken(getfcmToken?.fcmtoken)
    }
  }, [userToken])

  useEffect(() => {
    onMessageListener()
      .then((payload) => {
        if (payload && payload.data) {
          setNotification(payload.data);
        }
      })
      .catch((err) => {
        console.log(err)
        console.error('Error handling foreground notification:', err);
        toast.error('Error handling notification.');
      });
  }, [notification]);

  // / service worker
  useEffect(() => {
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function () {
        navigator.serviceWorker.register('/firebase-messaging-sw.js').then(
          function (registration) {
            console.log('Service Worker registration successful with scope: ', registration.scope)
          },
          function (err) {
            console.log('Service Worker registration failed: ', err)
          }
        )
      })
    }
    // console.log(notification)
  }, [notification])
  return <div>{React.cloneElement(children)}</div>;
}

export default PushNotificationLayout
