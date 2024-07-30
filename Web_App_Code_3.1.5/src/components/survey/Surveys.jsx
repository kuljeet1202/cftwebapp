import { useQuery } from '@tanstack/react-query'
import React, { useEffect, useState } from 'react'
import toast from 'react-hot-toast'
import { getQuestionApi } from 'src/hooks/getQuestion'
import { getQuestionResultApi, setQuestionResultApi } from 'src/store/actions/campaign'
import { access_key, getLanguage, getUser } from 'src/utils/api'
import Card from '../skeletons/Card'
import { ProgressBar } from 'react-bootstrap'

const Surveys = () => {

    const [submited, setSubmited] = useState(false)
    const [selectedOption, setSelectedOption] = useState('')
    const [answers, setAnswers] = useState([])
    const [data, setData] = useState([])

    let { id: language_id } = getLanguage()
    let user = getUser();

    // api call
    const getQuestion = async () => {
        try {
            const { data } = await getQuestionApi.getQuestion({
                access_key: access_key,
                language_id: language_id,
                user_id: user,
            })
            setData(data.data)
            return data.data
        } catch (error) {
            console.log(error)
        }
    }

    // react query
    const { data: questionsData } = useQuery({
        queryKey: ['getQuestion',],
        queryFn: getQuestion
    })


    const handleOptionClick = (options) => {
        // console.log(options)
        setSelectedOption(options.id)
        setQuestionResultApi({
            access_key: access_key,
            language_id: language_id,
            question_id: options?.question_id,
            option_id: options?.id,
            onSuccess: async (response) => {

            },
            onError: error => {
                console.log(error);
                toast.error(error);

            }
        }
        );
    }

    // const getQuestionResuls = async (id) => {
    //     getQuestionResultApi({
    //         access_key: access_key,
    //         language_id: language_id,
    //         question_id: id,
    //         onSuccess: async (response) => {
    //             if (submited) {
    //                 setData(data.data)
    //             }
    //         },
    //         onError: error => {
    //             console.log(error);
    //             toast.error(error);

    //         }
    //     }
    //     );
    // }

    const getQuestionResultApi = async (id) => {
        try {
            const { data } = await getQuestionApi.getQuestionResult({
                access_key: access_key,
                language_id: language_id,
                question_id: id
            })
            // console.log(data.data)
            setAnswers(data.data)
            if (submited) {
                setData(data.data)
            }
            return data.data
        } catch (error) {
            console.log(error)
        }
    }

    // react query
    const { isLoading, data: Data } = useQuery({
        queryKey: ['getQuestionResult'],
        queryFn: getQuestionResultApi
    })

    useEffect(() => {
        // console.log('submited', submited)
    }, [submited])



    const handleSubmit = (id) => {
        getQuestionResultApi(id)
        setSubmited(true)
    }

    return (<>
        {questionsData && questionsData.map((survey) => {
            // console.log(survey.id)
            return (
                <div className='surveysSect'>
                    <div className="card">
                        <span className='question'>{survey?.question}</span>
                        {
                            survey?.survey_options.map((options) => {
                                return <span className={`options ${options.id === selectedOption ? 'selectedOption' : ''}`} onClick={() => handleOptionClick(options)}>{options?.options}</span>
                            })
                        }
                        <button className='submitBtn' onClick={() => handleSubmit(survey.id)}>Submit</button>
                    </div>
                </div>

            )
        })}
        {/* </> 
            // <> */}
        {isLoading ? <Card /> :
            answers && answers.map((survey) => {
                return (
                    <div className='surveysSect'>
                        <div className="card resultCard">
                            <span className='question'>{survey?.question}</span>
                            {
                                survey?.survey_options.map((options) => {
                                    return <>
                                        <span><ProgressBar now={options?.percentage} /></span>
                                        <span className='percentage'>{options?.percentage}%</span>
                                    </>
                                })
                            }
                        </div>
                    </div>

                )
            })}

        {/* {data && data.map((survey) => {
            return (
                <div className='surveysSect'>
                    <div className="card">
                        <span className='question'>{survey?.question}</span>
                        {
                            survey?.survey_options.map((options) => {
                                options?.percentage !== undefined ? <>
                                    <span><ProgressBar now={options?.percentage} /></span>
                                    <span className='percentage'>{options?.percentage}</span>
                                </> :
                                    <span className={`options ${options.id === selectedOption ? 'selectedOption' : ''}`} onClick={() => handleOptionClick(options)}>{options?.options}</span>
                            })
                        }

                        <button className='submitBtn' onClick={() => handleSubmit(survey.id)}>Submit</button>
                    </div>
                </div>

            )
        })} */}
    </>


    )
}

export default Surveys


