import React, { useState, useEffect } from 'react'
import Modal from 'react-bootstrap/Modal'
import Button from 'react-bootstrap/Button'
import photo from '../../../public/assets/images/Login.jpg'
import { Icon } from 'react-icons-kit'
import { eye } from 'react-icons-kit/fa/eye'
import { eyeSlash } from 'react-icons-kit/fa/eyeSlash'
import { FaGoogle, FaMobileAlt } from 'react-icons/fa'
import {
  signInWithPopup,
  GoogleAuthProvider,
  signInWithEmailAndPassword,
  getAuth,
  sendEmailVerification
} from 'firebase/auth'
import { loadMobileType, register } from '../../store/reducers/userReducer'
import PhoneLoginTwo from './PhoneLoginTwo'
import RagisterModalTwo from './RegisterModalTwo'
import ForgotPasswordTwo from './ForgotPasswordTwo'
import { placeholderImage, translate } from '../../utils'
import { settingsData } from '../../store/reducers/settingsReducer'
import { useSelector } from 'react-redux'
import { useRouter } from 'next/navigation'
import FirebaseData from 'src/utils/Firebase'
import toast from 'react-hot-toast'
import { locationData } from 'src/store/reducers/settingsReducer'
import { registerFcmTokenApi } from 'src/store/actions/campaign'

const SignInModal = props => {
  const { authentication, messaging } = FirebaseData()
  const [modalShow, setModalShow] = React.useState(false)
  const [ForgotModalShow, setForgotModalShow] = React.useState(false)
  const [PhoneModalShow, setPhoneModalShow] = React.useState(false)
  // const handleClose = () => props.setPrivacy(false);
  const initialValues = { email: '', password: '' }
  const [formValues, setFormValues] = useState(initialValues)
  const [formErrors, setFormErrors] = useState(
    '',
    setTimeout(() => {
      if (formErrors !== '') setFormErrors('')
    }, 5000)
  )
  const [isSubmit, setIsSubmit] = useState(false)
  const [type, setType] = useState('password')
  const [icon, setIcon] = useState(eyeSlash)
  const location = useSelector(locationData)
  const storedLatitude = location && location.lat
  const storedLongitude = location && location.long

  const settings = useSelector(settingsData)

  const handleChange = e => {
    const { name, value } = e.target
    setFormValues({ ...formValues, [name]: value })
  }
  const navigate = useRouter()

  // form submit
  const handleSubmit = e => {
    e.preventDefault()
    setFormErrors(validate(formValues))
    setIsSubmit(true)
    // navigate.push('/')
  }

  useEffect(() => {
    if (Object.keys(formErrors).length === 0 && isSubmit);
  }, [formErrors, isSubmit])

  // validate email
  const validate = values => {
    const errors = {}
    if (!values.password) {
      errors.password = 'Password is required!'
    } else if (values.password.length < 6) {
      errors.password = 'Password must be more than 6 characters'
    } else if (values.password.length > 12) {
      errors.password = 'Password cannot exceed than 12 characters'
    }
    return errors
  }

  // password
  const handletoggle = () => {
    if (type === 'password') {
      setIcon(eye)
      setType('text')
    } else {
      setIcon(eyeSlash)
      setType('password')
    }
  }

  // sign in google
  const signInWithGoogle = async () => {
    const provider = new GoogleAuthProvider()
    await signInWithPopup(authentication, provider)
      .then(async response => {
        props.onHide()
        props.setIsLogout(true)
        await register({
          firebase_id: response.user.uid,
          name: response.user.displayName,
          email: response.user.email,
          type: 'gmail',
          profile: response.user.photoURL,
          status: '1',
          fcm_id: location.fcmtoken,
          onSuccess: async res => {
            toast.success(translate('loginMsg'))
            setTimeout(async () => {
              await registerFcmTokenApi({
                token: res.data.fcm_id,
                latitude: storedLatitude,
                longitude: storedLongitude,
                onSuccess: async res => { },
                onError: async err => {
                  console.log(err)
                }
              })
            }, [1000])

            // console.log('resLog',res)

            if (res.data.is_login === '0') {
              //If new User then show the Update Profile Screen
              navigate.push('/profile-update')
            }
            props.setisloginloading(false)
          },
          onError: error => {
            toast.error(translate('deactiveMsg'))
          }
        })
      })
      .catch(err => {
        console.log(err.message)
      })
  }
  // eslint-disable-next-line
  const [phonenum, setPhonenum] = useState(null)
  const auth = getAuth()

  // sign in with email and password
  const Signin = async () => {
    await signInWithEmailAndPassword(auth, formValues.email, formValues.password)
      .then(async userCredential => {
        // Signed in
        const user = userCredential.user
        if (user.emailVerified) {
          register({
            firebase_id: user.uid,
            email: formValues.email,
            type: 'email',
            status: '1',
            fcm_id: location.fcmtoken,
            onSuccess: success => {
              setTimeout(async () => {
                await registerFcmTokenApi({
                  token: success.data.fcm_id,
                  latitude: storedLatitude,
                  longitude: storedLongitude,
                  onSuccess: async res => { },
                  onError: async err => {
                    console.log(err)
                  }
                })  
              }, [1000])
              if (success.data.is_login === '0') {
                //If new User then show the Update Profile Screen
                navigate.push('/profile-update')
              }
              loadMobileType(false)
              props.setisloginloading(false)
              setFormValues(initialValues)
              toast.success('Login Successfully')
            },
            onError: error => {
              toast.error(translate('deactiveMsg'))
            }
          })
          props.setIsLogout(true)
        } else {
          toast.error('please first verify your email address!!!')
          sendEmailVerification(auth.currentUser)
        }
        // props.setIsLogout(true)
        props.onHide()
      })
      .catch(function (error) {
        var errorCode = error.code
        var errorMessage
        switch (errorCode) {
          case 'auth/invalid-email':
            errorMessage = 'Invalid email. Please enter a valid email and try again.'
            break
          case 'auth/wrong-password':
            errorMessage = 'Wrong password. Please enter the correct password and try again.'
            break
          case 'auth/user-not-found':
            errorMessage = 'User not found. Please check your email and try again.'
            break
          case 'auth/user-disabled':
            errorMessage = 'This account has been disabled. Please contact support for assistance.'
            break
          // handle other error codes as needed
          default:
            errorMessage = 'An error occurred. Please try again later.'
        }
        // display error message in a toast or alert
        toast.error(errorMessage)
      })
  }

  const signInwithPhone = e => {
    e.preventDefault()
    props.onHide()
    setPhoneModalShow(true)
    setModalShow(false)
    setForgotModalShow(false)
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
        <div className='ModalWrapper' id='ModalWrapper'>
          <div style={{ width: '100%', height: '100%', objectFit: 'cover', borderRadius: '20px' }} id='login_img1'>
            <img className='ModalImg' src={photo.src} alt='login image' />
            <div className='logo-img-overlay'>
              <img src={settings && settings?.web_setting?.web_header_logo} alt='logo image' id='logo1' onError={placeholderImage} />
            </div>
          </div>
          <div id='modal-content'>
            <Modal.Header closeButton>
              <Modal.Title id='contained-modal-title-vcenter'>{translate('loginTxt')}</Modal.Title>
            </Modal.Header>
            <Modal.Body>
              <div className=''>
                <div>
                  <div className='welcom-back'>
                    <h5>
                      <strong>{translate('welcomeback')}</strong>
                    </h5>
                    <div id='Welcom' style={{ fontSize: '14px' }}>
                      {' '}
                      {translate('enter-email-password')}
                    </div>
                  </div>
                  <form className='my-2' onSubmit={handleSubmit}>
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
                      <label htmlFor='floatingInput'>{translate('emailaddress')}</label>
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
                      />
                      <label htmlFor='floatingPassword'>{translate('passLbl')}</label>
                      <span onClick={handletoggle} className='password-icon'>
                        <Icon icon={icon} size={20} />
                      </span>
                      <div className='error-msg'>
                        {' '}
                        {formErrors.password}
                        <p
                          onClick={() => {
                            props.onHide()
                            setModalShow(false)
                            setPhoneModalShow(false)
                            setForgotModalShow(true)
                          }}
                          id='forgot'
                        >
                          {' '}
                          {translate('forgotPassLbl')}{' '}
                        </p>
                      </div>
                    </div>
                    <div className='py-3' id='login'>
                      <button type='submit' className='btn   btn-lg  w-100' id='loginbutton' onClick={Signin}>
                        {translate('loginTxt')}
                      </button>
                    </div>
                  </form>
                  <div className='bordert mx-3 my-3 py-2'></div>
                </div>
                <div className='container px-0' id='social_buttons'>
                  <div className='row'>
                    <div className='col-lg-6 col-12'>
                      <button id='login-social2' type='button' className=' btn ' onClick={signInWithGoogle}>
                        <FaGoogle />
                        <p>{translate('signin-with-google')}</p>
                      </button>
                    </div>
                    <div className='col-lg-6 col-12'>
                      <Button id='login-social3' type='button' onClick={e => signInwithPhone(e)}>
                        <FaMobileAlt />
                        <p>{translate('signin-with-phone')}</p>
                      </Button>
                    </div>
                  </div>
                </div>
              </div>
            </Modal.Body>

            <div className='footer'>
              <h6 className=''>
                {translate('donthaveacc_lbl')}
                <p
                  className='mt-1'
                  style={{ cursor: 'pointer', fontSize: '18px', fontWeight: 'bold', color: '#3B5998' }}
                  onClick={() => {
                    props.onHide()
                    setPhoneModalShow(false)
                    setForgotModalShow(false)
                    setModalShow(true)
                  }}
                >
                  {' '}
                  {translate('register')}{' '}
                </p>
              </h6>
            </div>
          </div>
        </div>
      </Modal>

      <ForgotPasswordTwo
        setLoginModalShow={props.setLoginModalShow}
        show={ForgotModalShow}
        onHide={() => setForgotModalShow(false)}
      />
      <RagisterModalTwo
        setIsLogout={props.setIsLogout}
        setLoginModalShow={props.setLoginModalShow}
        show={modalShow}
        onHide={() => setModalShow(false)}
      />
      <PhoneLoginTwo
        setisloginloading={props.setisloginloading}
        setIsLogout={props.setIsLogout}
        setLoginModalShow={props.setLoginModalShow}
        setPhonenum={setPhonenum}
        show={PhoneModalShow}
        onHide={() => setPhoneModalShow(false)}
      />
    </>
  )
}

export default SignInModal
