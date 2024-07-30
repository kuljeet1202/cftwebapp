import photo from '../../../public/assets/images/Login.jpg'
import React, { useState, useEffect } from 'react'
import { Icon } from 'react-icons-kit'
import { eye } from 'react-icons-kit/fa/eye'
import { eyeSlash } from 'react-icons-kit/fa/eyeSlash'
import Modal from 'react-bootstrap/Modal'
import { useRouter } from 'next/navigation'
import { getAuth, createUserWithEmailAndPassword, sendEmailVerification, signOut } from 'firebase/auth'
import { translate } from '../../utils'
import { useSelector } from 'react-redux'
import { settingsData } from '../../store/reducers/settingsReducer'
import toast from 'react-hot-toast'

const RagisterModalTwo = props => {
  const initialValues = {
    username: '',
    email: '',
    password: '',
    confirmpassword: ''
  }
  const [formValues, setFormValues] = useState(initialValues)
  const [formErrors, setFormErrors] = useState(
    '',
    setTimeout(() => {
      if (formErrors !== '') setFormErrors('')
    }, 5000)
  )
  const [isValidForm, setIsValidForm] = useState(false)
  const [isSubmit, setIsSubmit] = useState(false)
  const [type, setType] = useState('password')
  const [type2, setType2] = useState('password')
  const [icon, setIcon] = useState(eyeSlash)
  const [icon2, setIcon2] = useState(eyeSlash)
  const auth = getAuth()
  const settings = useSelector(settingsData)

  const handleChange = e => {
    const { name, value } = e.target

    setFormValues({ ...formValues, [name]: value })
  }
  const handleConfirmpassword = e => {
    const { name, value } = e.target

    setFormValues({ ...formValues, [name]: value })
  }

  const navigate = useRouter()

  const handleSignup = async (email, password) => {
    // Set form errors
    setFormErrors(validate(formValues))

    // Check if there are no form errors
    if (Object.keys(formErrors).length === 0) {
      try {
        // Perform signup
        const userCredential = await createUserWithEmailAndPassword(auth, email, password)
        const user = userCredential.user

        // Send email verification
        sendEmailVerification(user)

        // Sign out the user
        signOut(auth)

        // Display success message
        toast.success('Email sent! Please check Email')
        props.onHide()
        props.setLoginModalShow(true)

        // Redirect to home or any other page
        navigate.push('/')
      } catch (error) {
        // Handle signup error
        handleError(error)
      }
    }
  }

  const handleError = error => {
    var errorCode = error.code
    var errorMessage

    switch (errorCode) {
      case 'auth/email-already-in-use':
        errorMessage = 'Email already in use.'
        break
      case 'auth/invalid-email':
        errorMessage = 'Invalid email address.'
        break
      case 'auth/weak-password':
        errorMessage = 'Password is too weak.'
        break
      default:
        errorMessage = 'An error occurred:'
    }

    toast.error(errorMessage)
  }

  useEffect(() => {
    setIsSubmit(true)
  }, [isValidForm])

  const handleSubmit = async e => {
    e.preventDefault()
    if (formValues.password === formValues.confirmpassword) {
      await handleSignup(formValues.email, formValues.password)

    }
    else {
      toast.error('Password do not matched!')
    }
    // setFormErrors(validate(formValues));
  }

  const handletoggle = () => {
    if (type === 'password') {
      setIcon(eye)
      setType('text')
    } else {
      setIcon(eyeSlash)
      setType('password')
    }
  }

  const handleCtoggle = () => {
    if (type2 === 'password') {
      setIcon2(eye)
      setType2('text')
    } else {
      setIcon2(eyeSlash)
      setType2('password')
    }
  }

  useEffect(() => {
    if (Object.keys(formErrors).length === 0 && isSubmit); // eslint-disable-next-line
  }, [formErrors])

  const validate = values => {
    const errors = {} // eslint-disable-next-line

    // const password_pattern = /^(?=.*\d)(?=.*\[a-z])(?=.*\[A-Z])[a-zA-Z0-9]{8,}$/
    if (!values.username) {
      errors.username = ' User Name is reqired! '
    }
    if (!values.password) {
      errors.password = 'Password is required!'
    } else if (values.password.length < 6) {
      errors.password = 'Password must be more then 6 characters'
    } else if (values.password.length > 12) {
      errors.password = 'Password cannot exceed then 12 characters'
    }
    if (!values.confirmpassword) {
      errors.confirmPassword = ' Confirm Password is required!'
    } else if (values.confirmpassword === '' || values.confirmpassword !== values.password) {
      errors.confirmPassword = 'Password is not Matched!'
    } else {
      setIsValidForm(true)
      navigate.push('/')
    }

    return errors
  }

  const handleModalClose = () => {
    setFormValues(initialValues)
    setFormErrors('')
    setIsValidForm(false)
    setIsSubmit(false)
    setType('password')
    setType2('password')
    setIcon(eyeSlash)
    setIcon2(eyeSlash)
    props.onHide()
  }

  return (
    <>
      <Modal
        {...props}
        onHide={handleModalClose}
        size='xl'
        aria-labelledby='contained-modal-title-vcenter'
        centered
        dialogClassName='border-radius-2'
      >
        <div className='ModalWrapper' id='ModalWrapper22'>
          <div style={{ width: '100%', objectFit: 'cover', borderRadius: '20px' }} id='login_img3'>
            <img className='ModalImg3' src={photo.src} alt='register image' />
            <div className='logo-img-overlay'>
              <img src={settings && settings?.web_setting?.web_header_logo} alt='logo image' id='logo3' />
            </div>
          </div>

          <div id='modal-content'>
            <Modal.Header closeButton>
              <Modal.Title id='contained-modal-title-vcenter'>{translate('createAccLbl')}</Modal.Title>
            </Modal.Header>
            <Modal.Body>
              <div className=''>
                <div className='welcom-back3'>
                  <div>
                    <h5>
                      <strong>{translate('Welcome')}</strong>
                    </h5>
                    <div id='Welcom' style={{ fontSize: '14px' }}>
                      {' '}
                      {translate('register-daily-news')}
                    </div>
                  </div>
                </div>

                <form className='' onSubmit={handleSubmit}>
                  <div>
                    <div className='form-floating mb-3 mt-4'>
                      <input
                        type='text'
                        className='form-control'
                        name='username'
                        id='floatingInput'
                        placeholder='User Name'
                        aria-describedby='Username'
                        value={formValues.username}
                        onChange={handleChange}
                        required
                      />
                      <p className='error-msg'> {formErrors.username}</p>
                      <label htmlFor='floatingInput' name='name'>
                        {translate('username')}
                      </label>
                    </div>
                    <div className='form-floating mb-3'>
                      <input
                        type='email'
                        className='form-control'
                        name='email'
                        id='floatingInput'
                        placeholder='name@example.com'
                        aria-describedby='emailHelp'
                        value={formValues.email}
                        onChange={handleChange}
                        required
                      />
                      <p className='error-msg'> {formErrors.email}</p>
                      <label htmlFor='floatingInput' name='Email'>
                        {translate('emailaddress')}
                      </label>
                    </div>
                    <div className='form-floating mb-3'>
                      <input
                        type={type}
                        className='form-control'
                        id='floatingPassword'
                        placeholder='Password'
                        name='password'
                        value={formValues.password}
                        onChange={handleChange}
                        required
                      />
                      <label htmlFor='floatingPassword'>{translate('passLbl')}</label>
                      <span onClick={handletoggle} className='password-icon'>
                        <Icon icon={icon} size={19} />
                      </span>
                      <p className='error-msg'> {formErrors.password}</p>
                    </div>
                    <div className='form-floating mb-3'>
                      <input
                        type={type2}
                        className='form-control'
                        id='floatingPassword'
                        placeholder='Password'
                        name='confirmpassword'
                        value={formValues.confirmpassword}
                        onChange={handleConfirmpassword}
                        required
                      />
                      <label htmlFor='floatingConfirmPassword' className='confirmpss'>
                        {translate('confpassLbl')}
                      </label>
                      <span onClick={handleCtoggle} className='Cpassword-icon'>
                        <Icon icon={icon2} size={19} />
                      </span>
                      <p className='error-msg'> {formErrors.confirmPassword}</p>
                    </div>
                    <div className='py-3'>
                      <button type='submit' className='btn   btn-lg' id='loginbutton2'>
                        {translate('signupLbl')}
                      </button>
                    </div>
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

export default RagisterModalTwo
