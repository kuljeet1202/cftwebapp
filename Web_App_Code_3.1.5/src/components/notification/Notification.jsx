import { useEffect, useState } from 'react';
import Link from 'next/link';
import BreadcrumbNav from '../breadcrumb/BreadcrumbNav';
import { deleteusernotificationApi } from '../../store/actions/campaign';
import { translate,NoDataFound } from '../../utils';
import Skeleton from 'react-loading-skeleton';
import { MdMessage } from 'react-icons/md';
import { IoMdThumbsUp } from 'react-icons/io';
import { loaduserNotification } from '../../store/reducers/notificationbadgeReducer';
import { selectCurrentLanguage } from '../../store/reducers/languageReducer';
import { useSelector } from 'react-redux';
import ReactPaginate from 'react-paginate';
import { useQuery } from '@tanstack/react-query';
import { getNotificationsApi } from 'src/hooks/getNotificationApi';
import { access_key, getUser } from 'src/utils/api';
import toast from 'react-hot-toast';
import Layout from '../layout/Layout';
// import NoDataFound from '../noDataFound/NoDataFound';
import moment from 'moment-timezone';
import { settingsData } from 'src/store/reducers/settingsReducer';

const Notification = () => {
  const [Data, setData] = useState([]);
  const [convertedData, setConvertedData] = useState([]); // Store converted dates separately
  let user = getUser();
  const currentLanguage = useSelector(selectCurrentLanguage);
  const [totalLength, setTotalLength] = useState(0);
  const [offsetdata, setOffsetdata] = useState(0);
  const limit = 6;

  const settings = useSelector(settingsData)
  const systemTimezoneData = settings.system_timezone
  // api call
  const getUserNotification = async () => {
    try {
      const { data } = await getNotificationsApi.getUserNotification({
        access_key: access_key,
        offset: offsetdata.toString(),
        limit: limit.toString(),
        user_id: user
      });
      setData(data.data);
      setTotalLength(data.total);
      return data.data;
    } catch (error) {
      console.log(error);
    }
  };

  // react query
  const { isLoading } = useQuery({
    queryKey: ['getuserNotification', currentLanguage, offsetdata],
    queryFn: getUserNotification,

  });

  const handlePageChange = selectedPage => {
    const newOffset = selectedPage.selected * limit;
    setOffsetdata(newOffset);
  };

  const handleDeleteComment = (e, id) => {
    e.preventDefault();
    deleteusernotificationApi({
      id: id,
      onSuccess: response => {
        // Remove the deleted notification from the state
        setData(prevData => prevData.filter(notification => notification.id !== id));
        toast.success(response.message);
        loaduserNotification({
          offset: '0',
          limit: '10',
          onSuccess: () => { },
          onError: () => { }
        });
      },
      onError: error => {
        if (error === 'No Data Found') {
          setData('');
        }
      }
    });
  };

  // Function to format the date as "DD/MM/YYYY"
  const formatDate = dateString => {
    const date = new Date(dateString);
    const formattedDate = new Date(date.getTime() - (date.getTimezoneOffset() * 60000)).toISOString(); // Adjusting the date to UTC before formatting
    return formattedDate;
  };

  useEffect(() => {
    if (Data.length > 0) {
      // Iterate through each element in Data array and convert date to Asia/Kolkata timezone
      const convertedData = Data.map(element => {
        const timestampUtc = formatDate(element.date); // Using the formatDate function to format the date
        const dtKolkata = moment(timestampUtc).tz(systemTimezoneData);
        const convertedTime = dtKolkata.format("DD-MM-YYYY hh:mm:ss A");
        return {
          ...element,
          convertedTime
        };
      });
      setConvertedData(convertedData); // Store converted dates in a separate state variable
    }
  }, [Data]);

  return (
    <Layout>
      <BreadcrumbNav SecondElement={translate('notificationLbl')} ThirdElement='0' />

      <div className='personal_Sec bg-white'>
        <div id='main-Noticard' className='container '>
          <div className='d-flex bd-highlight mb-3'>
            <Link href='/personal-notification' id='btnNotification1' className='btn mx-1 bd-highlight'>
              {' '}
              {/* {translate('personalLbl')}{' '} */}
              {translate('personalNotifi')}{' '}:-
            </Link>
            {/* <Link href='/news-notification' id='btnNewsnoti' className='btn mx-1 bd-highlight'>
              {' '}
              {translate('news')}
            </Link> */}
            {/* <button id='btnNotification1' className="btn  btn mx-1 ms-auto bd-highlight" onClick={handleDeleteAll} > Delete All</button> */}
          </div>
          <div className='my-3'>
            {isLoading ? (
              <div className='col-12 loading_data'>
                <Skeleton height={20} count={22} />
              </div>
            ) : convertedData && convertedData.length > 0 ? ( // Use convertedData instead of Data
              convertedData.slice(offsetdata, offsetdata + limit).map((element, index) => (
                <div className='card my-3' key={index}>
                  <div className='card-body bd-highlight' id='card-noti'>
                    {element.type === 'comment_like' ? (
                      <IoMdThumbsUp className='message_icon' />
                    ) : (
                      <MdMessage className='message_icon' />
                    )}
                    <div className='Noti-text'>
                      <p className='bd-highlight bd-title'> {element.message}</p>
                      <p className='bd-highlight mb-0'>{element.convertedTime}</p>
                    </div>

                    <div className='iconTrash ms-auto bd-highlight'>
                      <button className='btn mt-2' id='btntrash' onClick={e => handleDeleteComment(e, element.id)}>
                        <p className='mb-0'>{translate('deleteTxt')}</p>
                      </button>
                    </div>
                  </div>
                </div>
              ))
            ) : (
              <div className='col-12 no_data mt-5'>
                <>
                  {NoDataFound()}
                 
                </>
              </div>
            )}
          </div>

          {totalLength > 7 ? (
            <ReactPaginate
              previousLabel={translate('previous')}
              nextLabel={translate('next')}
              breakLabel='...'
              breakClassName='break-me'
              pageCount={Math.ceil(totalLength / limit)}
              marginPagesDisplayed={2}
              pageRangeDisplayed={5}
              onPageChange={handlePageChange}
              containerClassName={'pagination'}
              previousLinkClassName={'pagination__link'}
              nextLinkClassName={'pagination__link'}
              disabledClassName={'pagination__link--disabled'}
              activeClassName={'pagination__link--active'}
            />
          ) : null}
        </div>
      </div>
    </Layout>
  );
}

export default Notification;
