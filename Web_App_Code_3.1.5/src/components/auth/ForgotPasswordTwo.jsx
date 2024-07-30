import photo from '../../../public/assets/images/Login.jpg'
import React, { useState, useEffect } from 'react'
import Modal from 'react-bootstrap/Modal'
import { getAuth, sendPasswordResetEmail } from 'firebase/auth'
import { translate } from '../../utils'
import { useSelector } from 'react-redux'
import { settingsData } from '../../store/reducers/settingsReducer'
import toast from 'react-hot-toast'

const ForgotPasswordTwo = props => {
  const initialValues = { email: '', password: '' }
  const [formValues, setFormValues] = useState(initialValues)
  const [formErrors, setFormErrors] = useState(
    '',
    setTimeout(() => {
      if (formErrors !== '') setFormErrors('')
    }, 5000)
  )
  const [isSubmit, setIsSubmit] = useState(false)

  const settings = useSelector(settingsData)

  const handleChange = e => {
    const { name, value } = e.target
    setFormValues({ ...formValues, [name]: value })
  }

  const handleSubmit = async e => {
    e.preventDefault()
    // setFormErrors(validate(formValues))
    setFormErrors(formValues)
    setIsSubmit(true)
    const auth = getAuth()
    await sendPasswordResetEmail(auth, formValues.email)
      .then(userCredential => {
        toast.success('Email sent Succesfully')
        // ...
        props.onHide()
        props.setLoginModalShow(true)
      })
      .catch(error => {
        switch (error.code) {
          case 'auth/invalid-email':
            toast.error('Invalid email address. Please enter a valid email.');
            break;
          case 'auth/user-not-found':
            toast.error('User not found. Please check the entered email address.');
            break;
          case 'auth/missing-email':
            toast.error('Email address is missing. Please enter your email.');
            break;
          default:
            toast.error('An error occurred. Please try again later.');
            break;
        }
      })
  }
  useEffect(() => {
    if (Object.keys(formErrors).length === 0 && isSubmit); // eslint-disable-next-line
  }, [formErrors]) // eslint-disable-next-line

  const validate = values => {
    const errors = {}
    const regex = /^[^\s@]+@[^s]+\.[^\s@]{2,}$/i
    if (!values.email) {
      errors.email = 'Email is required!'
    } else if (!regex.test(values.email)) {
      errors.email = 'Enter a Valid EMail'
    }

    return errors
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
        <div className='ModalWrapper' id='ModalWrapper11'>
          <div
            className='forgot-password'
            style={{ width: '100%', height: '100%', objectFit: 'cover', borderRadius: '20px' }}
            id='login_img2'
          >
            <img className='ModalImg' id='ModalImg2' src={photo.src} alt='forgot password images news' />
            <div className='logo-img-overlay'>
              <img id='NewsLogo' src={settings && settings?.web_setting?.web_header_logo} alt='news logo' />
            </div>
          </div>

          <div id='modal-content2'>
            <Modal.Header closeButton>
              <Modal.Title id='contained-modal-title-vcenter'> {translate('forgotPassLbl')}</Modal.Title>
            </Modal.Header>
            <Modal.Body style={{ marginTop: '12%' }}>
              <div className='AC'>
                <div className='welcom-back2'>
                  <div id='Welcom' className='mb-2'>
                    {translate('enteremail')}
                  </div>
                </div>
                <form onSubmit={handleSubmit}>
                  <div className='mb-3'>
                    <input
                      type='text'
                      className='form-control email-input'
                      name='email'
                      id='exampleInputEmail1'
                      aria-describedby='emailHelp'
                      placeholder='Email Address'
                      value={formValues.email}
                      onChange={handleChange}
                    />
                    {/* <p className='error-msg'> {formErrors.email}</p> */}
                  </div>

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
    </>
  )
}

export default ForgotPasswordTwo
