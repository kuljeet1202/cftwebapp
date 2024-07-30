'use client'
import Modal from 'react-bootstrap/Modal'
import photo from '../../../public/assets/images/Login.jpg'
import React, { useEffect, useState } from 'react'
//otp
import OTPInput from 'react-otp-input'
import { translate } from '../../utils'

//firebase
import FirebaseData from '../../utils/Firebase'
// import { RecaptchaVerifier, signInWithPhoneNumber } from "firebase/auth";
import { RecaptchaVerifier, signInWithPhoneNumber } from 'firebase/auth'
import { loadMobileType, register } from '../../store/reducers/userReducer'
import { useSelector } from 'react-redux'
import { settingsData } from '../../store/reducers/settingsReducer'
import { useRouter } from 'next/navigation'
import toast from 'react-hot-toast'
import { locationData } from 'src/store/reducers/settingsReducer'
import { registerFcmTokenApi } from 'src/store/actions/campaign'

const OTPModal = props => {
  const [OTP, setOTP] = useState('') // eslint-disable-next-line
  const { authentication } = FirebaseData()
  const location = useSelector(locationData)
  const storedLatitude = location && location.lat
  const storedLongitude = location && location.long

  const navigate = useRouter()

  const settings = useSelector(settingsData)

  const [resendTimer, setResendTimer] = useState(60);

  useEffect(() => {
    let intervalId;

    if (resendTimer > 0) {
      intervalId = setInterval(() => {
        setResendTimer((prevTimer) => prevTimer - 1);
      }, 1000);
    }

    return () => {
      clearInterval(intervalId);
    };
  }, [resendTimer]);


  const resendOTP = e => {
    e.preventDefault()
    // Reset the resendTimer to 60 seconds
    if (props.phonenum !== null) {
      generateOTP(props.phonenum)
      setResendTimer(60);
    }
  }

  const generateRecaptcha = () => {
    if (typeof window !== 'undefined') {
      if (!window.recaptchaVerifier) {
        window.recaptchaVerifier = new RecaptchaVerifier(
          'recaptcha-container',
          {
            size: 'invisible',
            callback: response => {
              // reCAPTCHA solved, allow signInWithPhoneNumber.
            }
          },
          authentication
        )
      }
    }
  }

  useEffect(() => {
    generateRecaptcha()
  }, [])

  const generateOTP = async phonenum => {
    // OTP Generation
    generateRecaptcha()
    let appVerifier = window.recaptchaVerifier
    await signInWithPhoneNumber(authentication, phonenum, appVerifier)
      .then(confirmationResult => {
        window.confirmationResult = confirmationResult
        toast.success("OTP Send Successfully");
        loadMobileType(true)
      })
      .catch(error => {
        let errorMessage = ''
        switch (error.code) {
          case 'auth/too-many-requests':
            errorMessage = 'Too many requests. Please try again later.'
            break
          case 'auth/invalid-phone-number':
            errorMessage = 'Invalid phone number. Please enter a valid phone number.'
            break
          default:
            errorMessage = 'An error occurred. Please try again.'
            break
        }
        // Display error message in a toast or alert
        toast.error(errorMessage)
      })
  }
  useEffect(() => {
    if (props.phonenum !== null) {
      generateOTP(props.phonenum)
    }
    // eslint-disable-next-line
  }, [props.phonenum])

  // handle otp error codes
  const handleAuthenticationError = errorCode => {
    switch (errorCode) {
      case 'auth/missing-verification-code':
        toast.error('Missing verification code. Please enter the code.')
        break

      case 'auth/code-expired':
        toast.error('The verification code has expired. Please generate a new one.')
        break

      case 'auth/invalid-verification-code':
        toast.error('Invalid verification code. Please enter a valid code.')
        break

      case 'auth/invalid-verification-id':
        toast.error('Invalid verification ID. Please try again with a valid ID.')
        break

      case 'auth/session-expired':
        toast.error('The session has expired. Please sign in again.')
        break

      case 'auth/quota-exceeded':
        toast.error('Quota exceeded. Please wait before sending a new verification code.')
        break

      default:
        toast.error('An unknown authentication error occurred.')
        break
    }
  }

  const submitOTP = async e => {
    e.preventDefault()

    let confirmationResult = window.confirmationResult

    try {
      const response = await confirmationResult.confirm(OTP)

      // User verified successfully.
      props.setIsLogout(true)
      props.onHide()

      register({
        firebase_id: response.user.uid,
        mobile: response.user.phoneNumber,
        type: 'mobile',
        status: '1',
        fcm_id: location.fcmtoken,
        onSuccess: response => {
          setTimeout(async () => {
            await registerFcmTokenApi({
              token: response.data.fcm_id,
              latitude: storedLatitude,
              longitude: storedLongitude,
              onSuccess: async res => { },
              onError: async err => {
                console.log(err)
              }
            })
          }, [1000])
          toast.success('Login Successfully')
          // console.log('phoneRes', response.data)
          if (response.data.is_login === '0') {
            // If new User then show the Update Profile Screen
            navigate.push('/profile-update')
          }
          props.setisloginloading(false)
        },
        onError: error => {
          toast.error(translate('deactiveMsg'))
        }
      })
    } catch (error) {
      handleAuthenticationError(error.code)

    }
  }

  const recaptchaClear = async () => {
    const recaptchaContainer = document.getElementById('recaptcha-container')
    if (recaptchaContainer) {
      recaptchaContainer.innerHTML = ''
    }

    if (window.recaptchaVerifier) {
      window?.recaptchaVerifier?.recaptcha?.reset()
    }
  }

  const onHandleClose = async () => {
    props.onHide()
    props.setisloginloading(false)
    await recaptchaClear()
    props.setPhonenum(null)
    props.setValue(null)
  }

  return (
    <>
      <div>
        <Modal
          {...props}
          size='xl'
          aria-labelledby='contained-modal-title-vcenter'
          centered
          dialogClassName='border-radius-2'
          onHide={onHandleClose}
        >
          <div className='ModalWrapper55' id='ModalWrapper'>
            <div style={{ width: '100%', height: '100%', objectFit: 'cover', borderRadius: '20px' }} id='login_img5'>
              <img className='ModalImg5' src={photo.src} alt='otp modal image' />
              <div className='logo-img-overlay'>
                <img src={settings && settings?.web_setting?.web_header_logo} alt='logo image' id='logo5' />
              </div>
            </div>

            <div id='modal-content'>
              <Modal.Header closeButton>
                <Modal.Title id='contained-modal-title-vcenter'>{translate('login')}</Modal.Title>
              </Modal.Header>
              <Modal.Body>
                <div className='AC'>
                  <div className=''>
                    <h5>
                      <strong>{translate('otp-sent')} </strong>
                    </h5>
                    <div id='Welcom' style={{ fontSize: '14px' }}>
                      {' '}
                      {props.phonenum}{' '}
                    </div>
                  </div>
                  <div className='mb-3 my-3 otp-content'>
                    <OTPInput
                      className='otp-container'
                      value={OTP}
                      onChange={setOTP}
                      autoFocus
                      numInputs={6}
                      disabled={false}
                      containerStyle={'otpbox'}
                      renderSeparator={<span className='space'></span>}
                      renderInput={props => <input {...props} className='custom-input-class'></input>}
                    />
                    {/* <div>
                      <button onClick={e => resendOTP(e)} id='resendbutton' className='btn'>
                        {translate('resendLbl')}
                      </button>
                    </div> */}
                    <div className="resend-code mt-4">
                      {resendTimer > 0 ? (
                        <div>
                          <span className="resend-text"> {translate("resendCodeIn")}</span>
                          <span className="resend-time" >
                            {" "}
                            {resendTimer} {translate("seconds")}
                          </span>
                        </div>
                      ) : (
                        <button onClick={e => resendOTP(e)} id='resendbutton' className='btn'>
                          {translate('resendLbl')}
                        </button>
                      )}
                    </div>
                  </div>
                  <form onClick={e => submitOTP(e)}>
                    <div className='py-3'>
                      <button type='submit' className='btn   btn-lg  w-100' id='submitbutton'>
                        {translate('submitBtn')}
                      </button>
                    </div>
                  </form>
                </div>
              </Modal.Body>
            </div>
          </div>
        </Modal>
        <div id='recaptcha-container' style={{ display: 'none' }}></div>
      </div>
    </>
  )
}

export default OTPModal