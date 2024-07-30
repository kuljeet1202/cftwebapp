import photo from '../../../public/assets/images/Login.jpg'
import 'react-phone-number-input/style.css'
import PhoneInput from 'react-phone-number-input'
import React, { useState } from 'react'
import Modal from 'react-bootstrap/Modal'
import { translate } from '../../utils'
import { useSelector } from 'react-redux'
import { settingsData } from '../../store/reducers/settingsReducer'
import dynamic from 'next/dynamic'
import toast from 'react-hot-toast'
const OTPModalNoSSR = dynamic(() => import('./OTPModal'), { ssr: false })
const PhoneLoginTwo = props => {
  const [PhoneOTPModalShow, setPhoneOTPModalShow] = React.useState(false)
  const [phonenum, setPhonenum] = useState(null)

  // const navigate = useRouter();

  const [value, setValue] = useState()
  const [error, setError] = useState(
    '',
    setTimeout(() => {
      if (error !== '') setError('')
    }, 5000)
  )

  const settings = useSelector(settingsData)

  // Load the libphonenumber library
  const phoneUtil = require('google-libphonenumber').PhoneNumberUtil.getInstance()

  // Validate a phone number
  const validatePhoneNumber = phone_number => {
    try {
      const parsedNumber = phoneUtil.parse(phone_number)
      return phoneUtil.isValidNumber(parsedNumber)
    } catch (err) {
      return false
    }
  }

  const handleGetOtp = e => {
    e.preventDefault()
    if (value === undefined) {
      toast.error('Please enter phone number!')
    } else if (validatePhoneNumber(value)) {
      setPhonenum(value)
      setPhoneOTPModalShow(true)
    } else {
      toast.error('Enter a valid phone number')
    }
  }

  return (
    <>
      <Modal
        {...props}
        size='xl'
        aria-labelledby='contained-modal-title-vcenter'
        centered
        dialogClassName='border-radius-2'
      >
        <div className='ModalWrapper44' id='ModalWrapper44'>
          <div style={{ width: '100%', height: '100%', objectFit: 'cover', borderRadius: '20px' }} id='login_img4'>
            <img className='ModalImg4' src={photo.src} alt='mobile-login' />
            <div className='logo-img-overlay'>
              <img src={settings && settings?.web_setting?.web_header_logo} alt='logo' id='logo4' />
            </div>
          </div>

          <div id='modal-content'>
            <Modal.Header closeButton>
              <Modal.Title id='contained-modal-title-vcenter'>{translate('loginTxt')}</Modal.Title>
            </Modal.Header>
            <Modal.Body>
              <div className='AC'>
                <div className='welcom-back4'>
                  <h5>
                    <strong> {translate('enter-your-mobile-number')} </strong>
                  </h5>
                  <div id='Welcom' style={{ fontSize: '14px' }}>
                    {' '}
                    {translate('six-didgit-code')}
                  </div>
                  <div className='mt-3'>
                    <PhoneInput
                      className='phoneInput'
                      placeholder='Enter your phone number'
                      defaultCountry={process.env.NEXT_PUBLIC_DEFAULT_COUNTRY}
                      international
                      value={value}
                      onChange={setValue}
                    />
                  </div>
                </div>
                <form className='my-2' onClick={e => handleGetOtp(e)}>
                  <div className='py-3'>
                    <p className='error-msg'>{error}</p>
                    <button type='submit' className='btn   btn-lg  w-100' id='submitbutton'>
                      {translate('reqOtpLbl')}
                    </button>
                  </div>
                </form>
              </div>
            </Modal.Body>
          </div>
        </div>
      </Modal>

      {phonenum !== null ? (
        <OTPModalNoSSR
          setPhonenum={setPhonenum}
          setValue={setValue}
          setisloginloading={props.setisloginloading}
          setIsLogout={props.setIsLogout}
          phonenum={phonenum}
          onPhonenumHide={props.onHide()}
          show={PhoneOTPModalShow}
          onHide={() => setPhoneOTPModalShow(false)}
        />
      ) : null}
    </>
  )
}

export default PhoneLoginTwo
