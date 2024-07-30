'use client';
import { useState, useRef, useEffect } from 'react';
import Button from 'react-bootstrap/Button';
import OverlayTrigger from 'react-bootstrap/OverlayTrigger';
import Popover from 'react-bootstrap/Popover';
import {
  deletecommentApi,
  setCommentLikeDislikeApi,
  setFlagApi,
  setcommentApi
} from '../../store/actions/campaign';
import { useSelector } from 'react-redux';
import { selectUser } from '../../store/reducers/userReducer';
import { imgError, translate, isLogin } from '../../utils';
import no_image from '../../../public/assets/images/no_image.jpeg';
import { Modal } from 'antd';
import { BiDislike, BiDotsVerticalRounded, BiSolidDislike, BiSolidFlag, BiSolidLike, BiSolidTrash } from 'react-icons/bi';
import { getCommentByNewsApi } from 'src/hooks/commentsApi';
import { access_key, getUser } from 'src/utils/api';
import { useQuery } from '@tanstack/react-query';
import toast from 'react-hot-toast';
import Card from '../skeletons/Card';
import { AiOutlineLike } from 'react-icons/ai';

const CommentsView = props => {
  const [LoadComments, setLoadComments] = useState(false);
  // const [refreshKey, setRefreshKey] = useState(0);
  const [Comment, setComment] = useState('');
  const [replyComment, setReplyComment] = useState("");
  const Nid = props.Nid;
  let user = getUser();
  const replyRef = useRef();
  const userData = useSelector(selectUser);
  const [modalOpen, setModalOpen] = useState(false);
  const [dotModal, setDotModal] = useState(false);
  const [CommentID, setCommentID] = useState(null);
  const [message, setMessage] = useState(null);
  const [replied, setReplied] = useState(true)

  // api call

  const getCommentByNews = async () => {
    try {
      const { data } = await getCommentByNewsApi.getCommentByNews({
        access_key: access_key,
        news_id: Nid,
        offset: '0',
        limit: '10'
      });
      return data?.data ? data?.data : [];
    } catch (error) {
      console.log(error);
    }
  };

  // react query
  const { data: Data, isLoading, refetch } = useQuery({
    queryKey: ['getCommentByNews ', Nid],
    queryFn: getCommentByNews,

  });

  // set comment
  const setCommentData = (e, id) => {
    e.preventDefault();
    setcommentApi({
      parent_id: id,
      news_id: Nid,
      message: replyComment,
      onSuccess: async (response) => {
        // setLoadComments(true);
        // setTimeout(() => {
        //   setLoadComments(false);
        // }, 1000);
        setReplyComment("");
        setReplied(true)
        await refetch();
      },
      onError: error => {
        console.log(error);
        toast.error(error);

      }
    }
    );
  };

  // set replay comment
  const setreplyComment = (e, id) => {
    e.preventDefault();
    setcommentApi({
      parent_id: id,
      news_id: Nid,
      message: replyComment,
      onSuccess: async (response) => {
        // setLoadComments(true);
        // setTimeout(() => {
        //   setLoadComments(false);
        // }, 1000);
        await refetch();
        setReplied(true)
        setReplyComment("");
      },
      onError: error => {
        console.log(error);
        toast.error(error);
      }
    }
    );
  };

  useEffect(() => {
    // if (replied) {

    //   const timeout = setTimeout(() => {
    //     setReplied(false)
    //   }, 1500);
    //   return () => {
    //     clearTimeout(timeout)
    //   }
    // }
  //  console.log(replied,'replied')
  }, [replied])
  

  const LikeButton = (e, elem) => {
    e.preventDefault();
    setCommentLikeDislikeApi({
      comment_id: elem.id,
      status: elem.like === 1 ? 0 : 1,
      onSuccess: async (res) => {
        // const updatedData = Data.map(comment => {
        //   if (comment.id === elem.id) {
        //     return { ...comment, like: res.like, total_like: res.total_like }; // Update like and total_like
        //   }
        //   return comment;
        // });
        await refetch();
        // setLoadComments(true); // Trigger loading state to force re-render
        // setTimeout(() => {
        //   setLoadComments(false);
        // }, 1000);
        // setData(updatedData); // Update the state with the modified comment data
      },
      onError: (err) => {
        console.log(err);
      },
    });
  };

  const dislikebutton = (e, elem) => {
    e.preventDefault();
    setCommentLikeDislikeApi({
      comment_id: elem.id,
      status: elem.dislike === 1 ? 0 : 2,
      onSuccess: async (res) => {
        // const updatedData = Data.map(comment => {
        //   if (comment.id === elem.id) {
        //     return { ...comment, dislike: res.dislike, total_dislike: res.total_dislike }; // Update dislike and total_dislike
        //   }
        //   return comment;
        // });
        // setLoadComments(true); // Trigger loading state to force re-render
        // setTimeout(() => {
        //   setLoadComments(false);
        // }, 1000);
        // setData(updatedData); // Update the state with the modified comment data
        await refetch();
      },
      onError: (err) => {
        console.log(err);
      },
    });
  };


  // dots
  const popupDots = (e, elem) => {
    e.preventDefault();
    setModalOpen(true);
    if (userData.data.id === elem.user_id) {
      setDotModal(true);
    } else {
      setDotModal(false);
    }
  };

  const deleteComment = (e, commentId) => {
    e.preventDefault();
    deletecommentApi({
      comment_id: commentId,
      onSuccess: async (res) => {
        // setLoadComments(true);
        // setRefreshKey(prevKey => prevKey + 1)
        // setRefreshKey(refreshKey + 1);
        await refetch();
        setModalOpen(false);
        toast.success(translate('comDelSucc'));
      },
      onError: err => {
        console.log(err);
      }
    }
    );
  };

  const submitBtn = e => {
    e.preventDefault();
    setFlagApi({
      comment_id: CommentID,
      news_id: Nid,
      message: message,
      onSuccess: async (res) => {
        // setRefreshKey(prevKey => prevKey + 1)
        // setRefreshKey(refreshKey + 1);
        setModalOpen(false);
        // setLoadComments(true);
        setMessage('');
        await refetch();
        toast.success(translate('flag'));
      },
      onError: err => {
        console.log(err);
        toast.error(err);
      }
    }
    );
  };


  const setNewComment = async (e) => {
    e.preventDefault();
    if (!isLogin()) {
      toast.error('please login first to comment');
      return;
    }
    setcommentApi({
      parent_id: 0,
      news_id: Nid,
      message: Comment,
      onSuccess: async (response) => {
        setComment('');
        // queryClient.invalidateQueries(['getCommentsByNews']);
        await refetch();
        setReplied(true)

        // setLoadComments(true);
        // setTimeout(() => {
        //   setLoadComments(false);
        // }, 1000);
      },
      onError: error => {
        console.log(error);
        toast.error(error);
      }
    }
    );
  };

  // useEffect(() => {
  //   // if (replied) {

  //     // const timeout = setTimeout(() => {
  //     //   setReplied(false)
  //     // }, 1500);
  //     // return () => {
  //     //   clearTimeout(timeout)
  //     // }
  //   // }
  //   console.log(replied,'replied')

  // }, [replied])
  

  return (
    <>
      <div>
        <form id='cs-main' onSubmit={e => setNewComment(e)}>
          <h2>{translate('leaveacomments')}</h2>
          <textarea
            value={Comment}
            name=''
            id='cs-input'
            cols='30'
            rows='10'
            onChange={e => {
              setComment(e.target.value);
            }}
            placeholder={`${translate('shareThoghtLbl')}`}
          ></textarea>
          <div className='d-flex align-items-end justify-content-end'>

            <button id='cs-btnsub' type='submit' className='btn'>
              {translate('submitpost')}
            </button>
          </div>
        </form>
      </div>
      {userData && userData.data ? (
        <div>
          {Data?.length == 0 ? null : <h2>{translate('comment')}</h2>}
          {
            isLoading ? <Card /> :
              <>
                {Data &&
                  Data.map(element => (
                    <div key={element.id}>
                      <div id='cv-comment' onClick={() => setCommentID(element.id)}>
                        <img id='cs-profile' src={element?.user?.profile} onError={imgError} alt='comment user profile news image' />
                        <div id='cs-card' className='card'>
                          <b>
                            <h5>{element?.user?.name ? element?.user?.name : element?.user?.mobile}</h5>
                          </b>
                          {/* <Link id="cdbtnReport">Report</Link> */}
                          <p id='cs-card-text' className='card-text'>
                            {element.message}
                          </p>
                          {['bottom-end'].map(placement => (
                            <>
                              <div className='comment_data'>
                                <div className='comment_like'>
                                  {
                                    element.like === 1 ? <>
                                      <BiSolidLike size={22} onClick={e => LikeButton(e, element)} />
                                      {element.total_like > 0 ? element.total_like : null}
                                    </> : <>
                                      <AiOutlineLike size={23} onClick={e => LikeButton(e, element)} />
                                      {element.total_like > 0 ? element.total_like : null}
                                    </>
                                  }
                                  {/* <BiSolidLike size={22} onClick={e => LikeButton(e, element)} />
                                  {element.total_like > 0 ? element.total_like : null} */}
                                </div>
                                <div className='comment_dislike'>
                                  {
                                    element.dislike === 1 ? <>
                                      <BiSolidDislike size={22} onClick={e => dislikebutton(e, element)} />
                                      {element.total_dislike > 0 ? element.total_dislike : null}
                                    </> : <>
                                      <BiDislike size={22} onClick={e => dislikebutton(e, element)} />
                                      {element.total_dislike > 0 ? element.total_dislike : null}
                                    </>
                                  }
                                  {/* <BiSolidDislike size={22} onClick={e => dislikebutton(e, element)} />
                                  {element.total_dislike > 0 ? element.total_dislike : null} */}
                                </div>
                                <div className='comment_dots'>
                                  {
                                    userData.data.id === element.user_id ? <span className='comment_delete' onClick={e => deleteComment(e, element.id)}>
                                      <span className='mb-0'>{<BiSolidTrash size={18} />}</span>

                                    </span> :
                                      <BiDotsVerticalRounded size={22} onClick={e => popupDots(e, element)} />
                                  }


                                </div>
                              </div>
                              <OverlayTrigger
                                trigger='click'
                                key={placement}
                                placement={placement}
                                rootClose
                                overlay={
                                  <Popover id={`popover-positioned-${placement}`} className={`${replied ? 'replyModal' : ''}`}>
                                    <Popover.Header as='h3'>{translate('addreplyhere')}</Popover.Header>
                                    <Popover.Body id='cv-replay-propover'>
                                      <form id='cv-replay-form' method='post' onSubmit={e => setCommentData(e, element.id)}>
                                        <textarea
                                          name=''
                                          id='cs-reply-input'
                                          cols='30'
                                          rows='5'
                                          value={replyComment}
                                          onChange={e => {
                                            setReplyComment(e.target.value);
                                          }}
                                          placeholder='Share Your reply...'
                                        ></textarea>
                                        <button id='cdbtnsubReplay' type='submit' className='btn'>
                                          {translate('submitreply')}
                                        </button>
                                      </form>
                                    </Popover.Body>
                                  </Popover>
                                }
                              >
                                <Button id={`${element.id}`} className='cdbtnReplay' onClick={()=>setReplied(false)} variant='secondary' ref={replyRef}>
                                  {translate('reply')}
                                </Button>
                              </OverlayTrigger>
                            </>
                          ))}
                        </div>
                      </div>
                      {element.replay.map(ele => (
                        <div id='cv-Rcomment' key={ele.id} onClick={() => setCommentID(ele.id)}>
                          <img id='cs-profile' src={ele?.user?.profile} onError={imgError} alt='replay comment user news image' />
                          <div id='cs-Rcard' className='card'>
                            <b>
                              <h5>{ele?.user?.name ? ele?.user?.name : ele?.user?.mobile}</h5>
                            </b>
                            <p id='cs-card-text' className='card-text'>
                              {ele.message}
                            </p>
                            {['bottom-end'].map(placement => (
                              <>
                                <div className='comment_data'>
                                  <div className='comment_like'>
                                    {
                                      ele.like === 1 ? <>
                                        <BiSolidLike size={22} onClick={e => LikeButton(e, ele)} />
                                        {ele.total_like > 0 ? ele.total_like : null}
                                      </> : <>
                                        <AiOutlineLike size={23} onClick={e => LikeButton(e, ele)} />
                                        {ele.total_like > 0 ? ele.total_like : null}
                                      </>
                                    }
                                    {/* <BiSolidLike size={22} onClick={e => LikeButton(e, ele)} />
                                    {ele.total_like > 0 ? ele.total_like : null} */}
                                  </div>
                                  <div className='comment_dislike'>
                                    {
                                      ele.dislike === 1 ? <>
                                        <BiSolidDislike size={22} onClick={e => dislikebutton(e, ele)} />
                                        {ele.total_dislike > 0 ? ele.total_dislike : null}
                                      </> : <>
                                        <BiDislike size={22} onClick={e => dislikebutton(e, ele)} />
                                        {ele.total_dislike > 0 ? ele.total_dislike : null}
                                      </>
                                    }
                                    {/* <BiSolidDislike size={22} onClick={e => dislikebutton(e, ele)} />
                                    {ele.total_dislike > 0 ? ele.total_dislike : null} */}
                                  </div>
                                  <div className='comment_dots'>
                                    {
                                      userData.data.id === ele.user_id ? <span className='comment_delete' onClick={e => deleteComment(e, ele?.id)}>
                                        <span className='mb-0'>{<BiSolidTrash size={18} />}</span>

                                      </span> :
                                        <BiDotsVerticalRounded size={22} onClick={e => popupDots(e, ele)} />
                                    }
                                    {/* <BiDotsVerticalRounded size={22} onClick={e => popupDots(e, ele)} /> */}
                                  </div>
                                </div>

                                <OverlayTrigger
                                  trigger='click'
                                  key={placement}
                                  placement={placement}
                                  rootClose
                                  overlay={
                                    <Popover id={`popover-positioned-${placement}`} className={`${replied ? 'replyModal' : ''}`}>
                                      <Popover.Header as='h3'>{translate('addreplyhere')}</Popover.Header>
                                      <Popover.Body id='cv-replay-propover'>
                                        <form method='post' onSubmit={e => setreplyComment(e, ele.parent_id)}>
                                          <textarea
                                            name=''
                                            id='cs-input'
                                            cols='30'
                                            rows='5'
                                            value={replyComment}
                                            onChange={e => {
                                              setReplyComment(e.target.value);
                                            }}
                                            placeholder='Share Your reply...'
                                          ></textarea>
                                          <button id='cdbtnsubReplay' type='submit' className='btn'>
                                            {translate('submitreply')}
                                          </button>
                                        </form>
                                      </Popover.Body>
                                    </Popover>
                                  }
                                >
                                  <Button id={`${element.id}`} className='cdbtnReplay' variant='secondary' onClick={()=>setReplied(false)} ref={replyRef}>
                                    {translate('reply')}
                                  </Button>
                                </OverlayTrigger>
                              </>
                            ))}
                          </div>
                        </div>
                      ))}
                    </div>
                  ))}
              </>
          }

          <Modal
            centered
            className='comment_Modal'
            open={modalOpen}
            maskClosable={true}
            onOk={() => setModalOpen(false)}
            onCancel={() => setModalOpen(false)}
            footer={false}
          >
            {dotModal ? (
              <div className='comment_delete' onClick={e => deleteComment(e,)}>
                <p className='mb-0'>{translate('deleteTxt')}</p>
                <BiSolidTrash size={18} />
              </div>
            ) : (
              <div className='comment_report'>
                <div className='comment_title'>
                  <p className='mb-0'>{translate('reportTxt')}</p>
                  <BiSolidFlag size={18} />
                </div>
                <textarea value={message} name='' id='' cols='30' rows='5' onChange={e => setMessage(e.target.value)} />
                <div className='comment_bottom d-flex align-items-end justify-content-end'>
                  <button type='submit' className='btn btn-secondary' onClick={e => submitBtn(e)}>
                    {translate('submitBtn')}
                  </button>
                </div>
              </div>
            )}
          </Modal>
        </div>
      ) : null}
    </>
  );
};

export default CommentsView;

