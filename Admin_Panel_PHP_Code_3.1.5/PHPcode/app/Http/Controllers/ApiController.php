<?php

namespace App\Http\Controllers;

use App\Models\AdSpaces;
use App\Models\Bookmark;
use App\Models\BreakingNews;
use App\Models\BreakingNewsView;
use App\Models\Category;
use App\Models\CommentNotification;
use App\Models\Comments;
use App\Models\CommentsFlag;
use App\Models\CommentsLike;
use App\Models\FeaturedSections;
use App\Models\Language;
use App\Models\LiveStreaming;
use App\Models\Location;
use App\Models\News;
use App\Models\News_image;
use App\Models\News_like;
use App\Models\News_view;
use App\Models\Pages;
use App\Models\SendNotification;
use App\Models\Settings;
use App\Models\SubCategory;
use App\Models\SurveyOption;
use App\Models\SurveyQuestion;
use App\Models\SurveyResult;
use App\Models\Tag;
use App\Models\Token;
use App\Models\User;
use App\Models\UserCategory;
use App\Models\WebSeoPages;
use App\Models\WebSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    private $toDate;
    private $nearest_location_measure;
    // private $lang;

    public function __construct()
    {
        $nearest_location_measure = Settings::where('type', 'nearest_location_measure')->first();
        $this->nearest_location_measure = $nearest_location_measure->message;
        $this->toDate = date('Y-m-d');
        // $this->lang = 'en';
    }

    public function setUserCategory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $user_id = Auth::user()->id;
            $category_id = $request->category_id;
            if ($category_id == '0') {
                UserCategory::where('user_id', $user_id)->delete();
            } else {
                $user_category = UserCategory::where('user_id', $user_id)->first();
                if ($user_category) {
                    $user_category->category_id = $category_id;
                    $user_category->save();
                } else {
                    $user_category = new UserCategory();
                    $user_category->user_id = $user_id;
                    $user_category->category_id = $category_id;
                    $user_category->save();
                }
            }
            $response = [
                'error' => false,
                'message' => 'Updated successfully',
            ];
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getQuestionResult(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $offset = $request->offset ?? 0;
            $limit = $request->limit ?? 10;
            $user_id = Auth::user()->id;
            $answeredQuestionIds = SurveyResult::where('user_id', $user_id)->pluck('question_id')->toArray();

            $where = [
                'status' => '1',
                'language_id' => $request->language_id,
            ];
            if ($request->has('question_id')) {
                $where['question_id'] = $request->question_id;
            }

            if (!empty($answeredQuestionIds)) {
                $where[] = ['id', 'NOT IN', $answeredQuestionIds];
            }
            $res = SurveyQuestion::with('surveyOptions')->where(function ($q) use ($where) {
                $q->where('status', $where['status'])->where('language_id', $where['language_id']);
                if (!empty($where['id'])) {
                    $q->whereNotIn('id', $where['id'][2]);
                }
                if (isset($where['question_id'])) {
                    $q->where('id', $where['question_id']);
                }
            });

            $total = $res->clone()->count();
            if ($total) {
                $questions = $res->clone()->orderByDesc('id')->limit($limit)->offset($offset)->get();
                foreach ($questions as $question) {
                    $userResponses = SurveyResult::where('question_id', $question->id)->get();
                    $totalUserResponses = $userResponses->count();
                    // Ensure the surveyOptions relationship is loaded
                    $question->load('surveyOptions');
                    // Calculate and set the percentage on each survey option
                    $question->surveyOptions->each(function ($option) use ($totalUserResponses) {
                        $option->percentage = $totalUserResponses != 0 ? ($option->counter * 100) / $totalUserResponses : 0;
                    });
                }

                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $questions,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function setQuestionResult(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'question_id' => 'required',
                'option_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $survey_result = new SurveyResult();
            $survey_result->user_id = Auth::user()->id;
            $survey_result->question_id = $request->question_id;
            $survey_result->option_id = $request->option_id;
            $survey_result->save();
            $get_val = SurveyOption::find($request->option_id);
            $counter = $get_val->counter + 1;
            $get_val->counter = $counter;
            $get_val->save();
            $response = [
                'error' => false,
                'message' => 'Data inserted successfully',
            ];
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getQuestion(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $user_id = Auth::user()->id;
            $answeredQuestionIds = SurveyResult::where('user_id', $user_id)->pluck('question_id')->toArray();

            $data = SurveyQuestion::select('id', 'question', 'status', 'language_id')->with('surveyOptions:id,options,counter,question_id')->where(['status' => 1, 'language_id' => $request->language_id]);
            if (!empty($answeredQuestionIds)) {
                $data = $data->whereNotIn('id', $answeredQuestionIds);
            }
            $total = $data->clone()->count('id');
            if ($total) {
                $offset = $request->offset ?? 0;
                $limit = $request->limit ?? 10;
                $res = $data->clone()->orderByDesc('id')->limit($limit)->offset($offset)->get();
                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $res,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function deleteNewsImages(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            if ($request->id) {
                $id = $request->id;
                $data_image = News_image::find($id);
                Storage::disk('public')->delete($data_image->getRawOriginal('other_image'));
                $data_image->delete();
                $response = [
                    'error' => false,
                    'message' => 'Image deleted!',
                ];
                $response = [
                    'error' => true,
                    'message' => 'Please fill all the data and submit!',
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'Please fill all the data and submit!',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function deleteNews(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $id = $request->id;
            $news = News::find($id);
            Storage::disk('public')->delete($news->getRawOriginal('image'));
            $data_image = News_image::where('news_id', $id)->get();
            foreach ($data_image as $row) {
                Storage::disk('public')->delete($row->getRawOriginal('other_image'));
                $row->delete();
            }
            $news->delete();
            $response = [
                'error' => false,
                'message' => 'News deleted!',
            ];
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function setNews(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action_type' => 'required',
                'news_id' => $request->input('action_type') == 'true' ? 'required' : 'nullable',
                'meta_title' => 'required',
                'meta_description' => 'required',
                'slug' => 'required',
                'meta_keyword' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            if ($request->action_type && $request->action_type == '2') {
                $news = News::find($request->news_id);

                $slug = News::where('slug', $request->slug)
                    ->where('id', '!=', $request->news_id)
                    ->first();
                if (!empty($slug)) {
                    $response = [
                        'error' => true,
                        'message' => trans('The slug is already in use. Please choose another. '),
                    ];
                    return response()->json($response);
                }
                $data = [];
                $data['user_id'] = Auth::user()->id;
                if ($request->category_id) {
                    $category_id = $request->category_id;
                    $data['category_id'] = $category_id;
                }
                if ($request->subcategory_id) {
                    $subcategory_id = $request->subcategory_id;
                    $data['subcategory_id'] = $subcategory_id ?? 0;
                } else {
                    $data['subcategory_id'] = 0;
                }
                if ($request->tag_id) {
                    $tag_id = $request->tag_id;
                    $data['tag_id'] = $tag_id;
                }
                if ($request->title) {
                    $title = $request->title;
                    $data['title'] = $title;
                }
                $data['date'] = now();
                if ($request->description) {
                    $description = $request->description;
                    $data['description'] = $description;
                }

                if ($request->meta_description) {
                    $meta_description = $request->meta_description;
                    $data['meta_description'] = $meta_description;
                }

                if ($request->meta_title) {
                    $meta_title = $request->meta_title;
                    $data['meta_title'] = $meta_title;
                }

                if ($request->meta_keyword) {
                    $meta_keyword = $request->meta_keyword;
                    $data['meta_keyword'] = $meta_keyword;
                }

                if ($request->slug) {
                    $slug = $request->slug;
                    $data['slug'] = $slug;
                }

                if ($request->show_till) {
                    $show_till = $request->show_till;
                    $data['show_till'] = $show_till;
                }
                if ($request->language_id) {
                    $language_id = $request->language_id;
                    $data['language_id'] = $language_id;
                }
                if ($request->location_id) {
                    $location_id = $request->location_id;
                    $data['location_id'] = $location_id;
                }

                $content_type = $request->content_type;

                if ($content_type == 'standard_post') {
                    $content_value = '';
                } elseif ($content_type == 'video_youtube') {
                    $content_value = $request->input('content_data');
                } elseif ($content_type == 'video_other') {
                    $content_value = $request->input('content_data');
                } elseif ($content_type == 'video_upload') {
                    $file = $request->file('content_data');
                    if ($request->hasFile('content_data')) {
                        $content_value = $request->file('content_data')->store('news_video', 'public');
                    } else {
                        $content_value = $news->content_value;
                    }
                }

                $news->content_type = $content_type;
                $news->content_value = $content_value;
                if ($request->hasFile('image')) {
                    Storage::disk('public')->delete($news->getRawOriginal('image'));
                    $news->image = $request->file('image')->store('news', 'public');
                }

                $news->update($data);
                if ($request->file('ofile')) {
                    foreach ($request->file('ofile') as $file) {
                        $newFile = new News_image();
                        $newFile->news_id = $news->id;
                        $newFile->other_image = $file->store('news', 'public');
                        $file->store('news', 'public');
                        $newFile->save();
                    }
                }
                $response = [
                    'error' => false,
                    'message' => 'News Updated Successfully',
                ];
            } else {
                $news = new News();
                $content_type = $request->content_type;
                if ($content_type == 'standard_post') {
                    $content_value = '';
                } elseif ($content_type == 'video_youtube') {
                    $content_value = $request->input('content_data');
                } elseif ($content_type == 'video_other') {
                    $content_value = $request->input('content_data');
                } elseif ($content_type == 'video_upload') {
                    $file = $request->file('content_data');
                    if ($file->isValid()) {
                        $content_value = $request->file('content_data')->store('news_video', 'public');
                    }
                }
                if ($request->hasFile('image')) {
                    $news->image = $request->file('image')->store('news', 'public');
                }

                if ($request->meta_keyword) {
                    $meta_keyword = $request->meta_keyword;
                    $data['meta_keyword'] = $meta_keyword;
                }
                $slug = News::where('slug', $request->slug)->first();
                if (!empty($slug)) {
                    $response = [
                        'error' => true,
                        'message' => trans('The slug is already in use. Please choose another. '),
                    ];
                    return response()->json($response);
                }
                $tag_id = $request->tag_id ?? '';
                $news->category_id = $request->category_id;
                $news->subcategory_id = $request->subcategory_id ?? 0;
                $news->tag_id = $tag_id;
                $news->title = $request->title;
                $news->date = now(); // Use Laravel's helper for the current timestamp
                $news->description = $request->description ?? '';
                $news->status = $request->status;
                $news->content_type = $content_type;
                $news->content_value = $content_value;
                $news->user_id = Auth::user()->id;
                $news->show_till = $request->show_till ?? '';
                $news->language_id = $request->language_id;
                $news->location_id = $request->location_id ?? 0;
                $news->meta_keyword = $meta_keyword;
                $news->meta_description = $request->meta_description;
                $news->slug = $request->slug;

                $news->meta_title = $request->meta_title;

                $news->admin_id = '0';
                $news->status = '0';
                $news->save();
                $id = $news->id;
                if ($request->file('ofile')) {
                    foreach ($request->file('ofile') as $file) {
                        $newFile = new News_image();
                        $newFile->news_id = $id;
                        $newFile->other_image = $file->store('news', 'public');
                        $newFile->save();
                    }
                }
                $response = [
                    'error' => false,
                    'message' => 'News added Successfully',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function deleteUserNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $comment_notification_id = $request->id;
            CommentNotification::whereIn('id', explode(',', $comment_notification_id))->delete();
            $response = [
                'error' => false,
                'message' => 'Notification deleted!',
            ];
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getUserNotification(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $data = CommentNotification::where('user_id', $user_id)->get();
            if ($data) {
                $total = count($data);
                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $data,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function setNewsView(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'news_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $news_id = $request->news_id;
            $data = News_view::where('user_id', Auth::user()->id)->where('news_id', $news_id)->get();
            if ($data->isEmpty()) {
                $news_view = new News_view();
                $news_view->user_id = Auth::user()->id;
                $news_view->news_id = $news_id;
                $news_view->save();
                $response = [
                    'error' => false,
                    'message' => 'News View added successfully',
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'News already viewed by this user.',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function setBreakingNewsView(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'breaking_news_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $breaking_news_id = $request->breaking_news_id;
            $data = BreakingNewsView::where('user_id', Auth::user()->id)
                ->where('breaking_news_id', $breaking_news_id)
                ->get();
            if ($data->isEmpty()) {
                $breacking_news_view = new BreakingNewsView();
                $breacking_news_view->user_id = Auth::user()->id;
                $breacking_news_view->breaking_news_id = $breaking_news_id;
                $breacking_news_view->save();
                $response = [
                    'error' => false,
                    'message' => 'Breaking News View added successfully',
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'Breaking News already Viewed by this user',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function setCommentLikeDislike(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
                'comment_id' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $user_id = Auth::user()->id;
            $language_id = $request->language_id;
            $comment_id = $request->comment_id;
            $status = $request->status;
            if ($status != '0') {
                $comment_like = CommentsLike::where('comment_id', $comment_id)->where('user_id', $user_id)->first();
                if (!empty($comment_like)) {
                    $comment_like->status = $status;
                    $comment_like->save();
                } else {
                    $comment_like = new CommentsLike();
                    $comment_like->user_id = $user_id;
                    $comment_like->comment_id = $comment_id;
                    $comment_like->status = $status;
                    $comment_like->save();
                }
                $insert_id = $comment_like->id;
                if ($status == '1') {
                    $res_comment = CommentsLike::find($insert_id);
                    if (!empty($res_comment)) {
                        $comment_id = $res_comment->comment_id;
                        $res_comment1 = Comments::find($comment_id);
                        if ($comment_id) {
                            $old_user_id = $res_comment1->user_id;
                            $res1 = User::find($old_user_id);
                            if (!empty($res1)) {
                                $get_name = $res1->name;
                                $fcmMsg = [
                                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                    'type' => 'comment_like',
                                    'language_id' => $language_id,
                                    'message' => 'Like in your comment ' . $res_comment1->message . ' by ' . $get_name,
                                    'body' => 'Like in your comment ' . $res_comment1->message . ' by ' . $get_name,
                                    'sound' => 'default',
                                ];
                                $fcm_id = $res1->fcm_id ? $res1->fcm_id : '';
                                $fcmFields = [
                                    'to' => $fcm_id,
                                    'priority' => 'high',
                                    'notification' => $fcmMsg,
                                    'data' => $fcmMsg,
                                ];
                                $setting = Settings::where('type', 'fcm_sever_key')->first();
                                $API_ACCESS_KEY = $setting ? $setting->message : 0;
                                $headers = ['Authorization: key=' . $API_ACCESS_KEY, 'Content-Type: application/json'];
                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
                                $result = curl_exec($ch);
                                if ($result === false) {
                                    die('Curl failed: ' . curl_error($ch));
                                }
                                //Now close the connection
                                curl_close($ch);
                                $comment_notification = new CommentNotification();
                                $comment_notification->master_id = $insert_id;
                                $comment_notification->user_id = $old_user_id;
                                $comment_notification->sender_id = $user_id;
                                $comment_notification->type = 'comment_like';
                                $comment_notification->message = 'Like in your comment ' . $res_comment1->message . ' by ' . $get_name;
                                $comment_notification->date = now();
                                $comment_notification->save();
                            }
                        }
                    }
                }
            } else {
                CommentsLike::where('comment_id', $comment_id)->where('user_id', $user_id)->delete();
            }
            $response = [
                'error' => false,
                'message' => 'updated Successfully',
            ];
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function deleteComment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }
            $id = $request->comment_id;
            $comment = Comments::find($id);
            if ($comment->user_id == Auth::user()->id) {
                // for remove sub comment data
                $sub_comment = Comments::select('id')->where('parent_id', $id)->get();
                if (!$sub_comment->isEmpty()) {
                    foreach ($sub_comment as $row) {
                        Comments::find($row->id)->delete();
                    }
                }
            }
            $comment->delete();
            $response = [
                'error' => false,
                'message' => 'comment deleted!',
            ];
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function setFlag(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment_id' => 'required',
                'news_id' => 'required',
                'message' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $user_id = Auth::user()->id;
            $news_id = $request->news_id;
            $comment_id = $request->comment_id;
            $message = $request->message;
            $status = '1';
            $commnt_flag = new CommentsFlag();
            $commnt_flag->comment_id = $comment_id;
            $commnt_flag->user_id = $user_id;
            $commnt_flag->news_id = $news_id;
            $commnt_flag->message = $message;
            $commnt_flag->status = $status;
            $commnt_flag->date = now();
            $commnt_flag->save();
            $response = [
                'error' => false,
                'message' => 'flag successfully',
            ];
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function setComment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'news_id' => 'required',
                'message' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }
            $user_id = Auth::user()->id;
            $parent_id = $request->parent_id ?? 0;
            $news_id = $request->news_id;
            $message = $request->message;

            $comment = Comments::create([
                'user_id' => $user_id,
                'parent_id' => $parent_id,
                'news_id' => $news_id,
                'message' => $message,
                'status' => 1,
                'date' => now(),
            ]);
            $insert_id = $comment->id;
            if ($parent_id) {
                $res = Comments::find($parent_id);
                if (!empty($res)) {
                    $old_user_id = $res->user_id;
                    $user = User::find($old_user_id);
                    if (!empty($user)) {
                        $fcmMsg = [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'type' => 'comment',
                            'news_id' => $news_id,
                            'message' => 'Reply in your comment ' . $res->message . ' by ' . $user->name,
                            'body' => 'Reply in your comment ' . $res->message . ' by ' . $user->name,
                            'sound' => 'default',
                        ];
                        $fcm_id = $user->fcm_id ? $user->fcm_id : '';
                        $fcmFields = [
                            'to' => $fcm_id,
                            'priority' => 'high',
                            'notification' => $fcmMsg,
                            'data' => $fcmMsg,
                        ];
                        $setting = Settings::where('type', 'fcm_sever_key')->first();
                        $API_ACCESS_KEY = $setting ? $setting->message : 0;
                        $headers = ['Authorization: key=' . $API_ACCESS_KEY, 'Content-Type: application/json'];
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
                        $result = curl_exec($ch);
                        if ($result === false) {
                            die('Curl failed: ' . curl_error($ch));
                        }
                        curl_close($ch);
                        CommentNotification::create([
                            'master_id' => $insert_id,
                            'user_id' => $old_user_id,
                            'sender_id' => $user_id,
                            'type' => 'comment',
                            'message' => 'Reply in your comment ' . $res->message . ' by ' . $user->name,
                            'date' => now(),
                        ]);
                    }
                }
            }
            $res = Comments::with('user')->where('news_id', $news_id)->where('parent_id', 0)->where('status', 1);
            $total = $res->clone()->count('id');
            if ($total) {
                $offset = $request->offset ?? 0;
                $limit = $request->limit ?? 10;
                $data = $res->clone()->orderBy('id', 'DESC')->skip($offset)->take($limit)->get();
                for ($i = 0; $i < count($data); $i++) {
                    $comment_like = CommentsLike::where('comment_id', $data[$i]->id);
                    $data[$i]->total_like = $comment_like->clone()->where('status', 1)->count('id');
                    $data[$i]->total_dislike = $comment_like->clone()->where('status', 2)->count('id');
                    $data[$i]->like = $comment_like->clone()->where('status', 1)->where('user_id', $user_id)->count('id');
                    $data[$i]->dislike = $comment_like->clone()->where('status', 2)->where('user_id', $user_id)->count('id');

                    $data[$i]->replay = $data3 = [];
                    $data3 = Comments::with('user')->where('news_id', $news_id)->where('parent_id', $data[$i]->id)->where('status', 1)->orderBy('id', 'ASC')->get();
                    for ($j = 0; $j < count($data3); $j++) {
                        $comment_like1 = CommentsLike::where('comment_id', $data3[$j]->id);
                        $data3[$j]->total_like = $comment_like1->clone()->where('status', 1)->count('id');
                        $data3[$j]->total_dislike = $comment_like1->clone()->where('status', 2)->count('id');
                        $data3[$j]->like = $comment_like1->clone()->where('status', 1)->where('user_id', $user_id)->count('id');
                        $data3[$j]->dislike = $comment_like1->clone()->where('status', 2)->where('user_id', $user_id)->count('id');
                    }
                    $data[$i]->replay = $data3;
                }
                $response = [
                    'error' => false,
                    'message' => 'Comment successfully',
                    'total' => $total,
                    'data' => $data,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getBookmark(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $user_id = Auth::user()->id;
            $language_id = $request->language_id;
            $offset = $request->offset ?? 0;
            $limit = $request->limit ?? 10;

            $news = DB::table('tbl_bookmark as b')
                ->select('b.*', 'n.category_id', 'c.category_name', 'n.subcategory_id', 'n.language_id', 'n.title', 'n.slug', 'n.date', 'n.tag_id', 'n.content_type', 'n.content_value', 'n.image', 'n.description')
                ->join('tbl_news as n', 'b.news_id', '=', 'n.id')->join('tbl_category as c', 'c.id', '=', 'n.category_id')
                ->where(function ($query) {
                    $query->where('n.show_till', '>=', $this->toDate)->orWhere('n.show_till', '0000-00-00');
                })->where('b.user_id', $user_id)->where('n.status', 1)->where('n.language_id', $language_id);

            $total = $news->clone()->count();

            if ($total) {
                $data = $news->clone()->limit($limit)->offset($offset)->orderBy('id', 'DESC')->get();
                foreach ($data as $item) {
                    if (($item->image) && strpos($item->image, 'news/') === false) {
                        $image = 'news/' . $item->image;
                    } else {
                        $image = $item->image;
                    }
                    $item->image = ($item->image) && Storage::disk('public')->exists($image) ? url(Storage::url($image)) : '';

                    if ($item->content_type == 'video_upload') {
                        $item->content_type = Storage::url('public/images/news/' . $item->content_value);
                    }
                    $item->image_data = News_image::where('news_id', $item->news_id)->get();
                    $news_like = News_like::where('news_id', $item->news_id);
                    $item->total_like = $news_like->clone()->where('status', 1)->count();
                    $item->total_dislike = $news_like->clone()->where('status', 2)->count();
                    $item->like = $news_like->clone()->where('status', 1)->where('user_id', $user_id)->exists();
                    $item->dislike = $news_like->clone()->where('status', 2)->where('user_id', $user_id)->exists();
                    $item->total_views = News_view::where('news_id', $item->news_id)->count();
                    $item->tag_name = '';
                    if (isset($item->tag_id) && $item->tag_id != '') {
                        $item->tag_name = Tag::whereIn('id', explode(',', $item->tag_id))->distinct()->pluck('tag_name')->implode(',');
                    }
                }
                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $data,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function setBookmark(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'news_id' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }
            $user_id = Auth::user()->id;
            $news_id = $request->news_id;
            $status = $request->status;
            if ($status == '1') {
                $data = Bookmark::where('user_id', $user_id)->where('news_id', $news_id)->count('id');
                if ($data) {
                    $response = [
                        'error' => true,
                        'message' => 'already bookmark',
                    ];
                } else {
                    Bookmark::create([
                        'user_id' => $user_id,
                        'news_id' => $news_id,
                    ]);
                    $response = [
                        'error' => false,
                        'message' => 'bookmark successfully',
                    ];
                }
            } elseif ($status == '0') {
                Bookmark::where('user_id', $user_id)->where('news_id', $news_id)->delete();
                $response = [
                    'error' => false,
                    'message' => 'bookmark removed successfully',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getLike(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $user_id = Auth::user()->id;
            $language_id = $request->language_id;
            $offset = $request->offset ?? 0;
            $limit = $request->limit ?? 10;

            $data = DB::table('tbl_news_like as l')->select('l.*', 'n.category_id', 'c.category_name', 'n.title', 'n.slug', 'n.date', 'n.tag_id', 'n.content_type', 'n.content_value', 'n.image', 'n.description')->join('tbl_news as n', 'n.id', '=', 'l.news_id')->join('tbl_category as c', 'c.id', '=', 'n.category_id')->where('l.user_id', $user_id)->where('l.status', 1)->where('n.language_id', $language_id)->limit($limit)->offset($offset)->orderBy('l.id', 'DESC')->get();

            foreach ($data as $item) {
                $news_like = News_like::where('news_id', $item->id);
                $item->total_like = $news_like->clone()->where('status', 1)->count('id');
                $item->total_dislike = $news_like->clone()->where('status', 2)->count('id');
                $item->like = $news_like->clone()->where('status', 1)->where('user_id', $user_id)->count('id');
                $item->dislike = $news_like->clone()->where('status', 2)->where('user_id', $user_id)->count('id');
                $item->tag_name = '';
                if (isset($item->tag_id) && !empty($item->tag_id)) {
                    $tagNames = Tag::whereIn('id', explode(',', $item->tag_id))
                        ->distinct()
                        ->pluck('tag_name')
                        ->implode(',');
                    $item->tag_name = $tagNames;
                }
                $item->image_data = News_image::where('news_id', $item->news_id)->get();
                if (($item->image) && strpos($item->image, 'news/') === false) {
                    $image = 'news/' . $item->image;
                } else {
                    $image = $item->image;
                }
                $item->image = ($item->image) && Storage::disk('public')->exists($image) ? url(Storage::url($image)) : '';
            }
            $total = DB::table('tbl_news_like as l')->select('l.*', 'n.category_id', 'c.category_name', 'n.title', 'n.slug', 'n.date', 'n.tag_id', 'n.content_type', 'n.content_value', 'n.image', 'n.description')->join('tbl_news as n', 'n.id', '=', 'l.news_id')->join('tbl_category as c', 'c.id', '=', 'n.category_id')->where('l.user_id', $user_id)->where('l.status', 1)->where('n.language_id', $language_id)->orderBy('l.id', 'DESC')->count();
            if ($data) {
                $response['error'] = false;
                $response['total'] = $total;
                $response['data'] = $data;
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function setLikeDislike(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'news_id' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $user_id = Auth::user()->id;
            $news_id = $request->news_id;
            $status = $request->status;
            if ($status != '0') {
                $news_like = News_like::where('news_id', $news_id)->where('user_id', $user_id)->first();
                if ($news_like) {
                    $news_like->status = $status;
                    $news_like->save();
                } else {
                    $news_like = new News_like();
                    $news_like->status = $status;
                    $news_like->user_id = $user_id;
                    $news_like->news_id = $news_id;
                    $news_like->save();
                }
            } else {
                News_like::where('news_id', $news_id)->where('user_id', $user_id)->delete();
            }
            $response = [
                'error' => false,
                'message' => 'updated successfully!',
            ];
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getCommentByNews(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'news_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }
            $user_id = (Auth::check()) ? Auth::user()->id : 0;
            $news_id = $request->news_id;
            $res = Comments::with('user')->where('news_id', $news_id)->where('parent_id', 0)->where('status', 1);
            $total = $res->clone()->count('id');
            if ($total) {
                $offset = $request->offset ?? 0;
                $limit = $request->limit ?? 10;
                $data = $res->clone()->orderBy('id', 'DESC')->skip($offset)->take($limit)->get();
                for ($i = 0; $i < count($data); $i++) {
                    $comment_like = CommentsLike::where('comment_id', $data[$i]->id);
                    $data[$i]->total_like = $comment_like->clone()->where('status', 1)->count('id');
                    $data[$i]->total_dislike = $comment_like->clone()->where('status', 2)->count('id');
                    $data[$i]->like = $comment_like->clone()->where('status', 1)->where('user_id', $user_id)->count('id');
                    $data[$i]->dislike = $comment_like->clone()->where('status', 2)->where('user_id', $user_id)->count('id');

                    $data[$i]->replay = $data3 = [];
                    $data3 = Comments::with('user')->where('news_id', $news_id)->where('parent_id', $data[$i]->id)->where('status', 1)->orderBy('id', 'ASC')->get();
                    for ($j = 0; $j < count($data3); $j++) {
                        $comment_like1 = CommentsLike::where('comment_id', $data3[$j]->id);
                        $data3[$j]->total_like = $comment_like1->clone()->where('status', 1)->count('id');
                        $data3[$j]->total_dislike = $comment_like1->clone()->where('status', 2)->count('id');
                        $data3[$j]->like = $comment_like1->clone()->where('status', 1)->where('user_id', $user_id)->count('id');
                        $data3[$j]->dislike = $comment_like1->clone()->where('status', 2)->where('user_id', $user_id)->count('id');
                    }
                    $data[$i]->replay = $data3;
                }
                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $data,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getFeaturedSectionById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            
            $language_id = $request->language_id;
            $news_type = $request->news_type ?? '';
            $style_web = $request->style_web ?? '';
            $user_id = Auth::check() ? Auth::user()->id : 0;
            $latitude = $request->latitude ?? 0;
            $longitude = $request->longitude ?? 0;

            if ($request->section_id) {
                $data = FeaturedSections::where('id', $request->section_id)->get();
            } elseif ($request->slug) {
                $data = FeaturedSections::where('slug', $request->slug)->get();
            } else {
                if (!empty($news_type) && !empty($style_web)) {
                    $data = FeaturedSections::where('news_type', $news_type)->where('status', 1)->where('style_web', $style_web)->orderBy('row_order', 'ASC')->get();
                } else {
                    $data = FeaturedSections::where('language_id', $language_id)->where('status', 1)->orderBy('row_order', 'ASC')->get();
                }
            }
            foreach ($data as $key => $row) {
                if ($row->news_type == 'news' || $row->news_type == 'videos') {
                    if ($row->filter_type == 'most_commented') {
                        $results = DB::table('tbl_news')
                            ->select('tbl_news.*', 'tbl_category.category_name', 'tbl_subcategory.subcategory_name', 'tbl_comment.newscount', DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                            ->leftJoin('tbl_category', 'tbl_category.id', '=', 'tbl_news.category_id')
                            ->leftJoin('tbl_subcategory', 'tbl_subcategory.id', '=', 'tbl_news.subcategory_id')
                            ->leftJoin('tbl_location', 'tbl_location.id', '=', 'tbl_news.location_id')
                            ->where('tbl_news.status', 1)
                            ->where('tbl_news.language_id', $language_id)
                            ->where(function ($query) {
                                $query->where('tbl_news.show_till', '>=', $this->toDate);
                                $query->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                            })
                            ->join(DB::raw('(SELECT news_id, COUNT(*) AS newscount FROM tbl_comment GROUP BY news_id) AS tbl_comment'), function ($join) {
                                $join->on('tbl_news.id', '=', 'tbl_comment.news_id');
                            });
                        if ($row->category_ids != null && $row->subcategory_ids != null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.category_id', explode(',', $row->category_ids))->orWhereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                            });
                        } elseif ($row->category_ids != null && $row->subcategory_ids == null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.category_id', explode(',', $row->category_ids));
                            });
                        } elseif ($row->category_ids == null && $row->subcategory_ids != null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                            });
                        }
                        if ($row->news_type == 'videos') {
                            if ($row->videos_type == 'news') {
                                $results->where('tbl_news.description', '!=', '');
                                $results->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other']);
                            }
                        }
                        $nearest_location_measure = $this->nearest_location_measure;
                        // Append condition based on latitude and longitude
                        if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                            $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                        }
                        $orderby = 'tbl_comment.newscount';
                        $result_count = $results->count();
                        $results = $results->orderBy($orderby, 'DESC')->get();
                    } elseif ($row->filter_type == 'recently_added') {
                        $results = DB::table('tbl_news')
                            ->select('tbl_news.*', DB::raw('IFNULL(tbl_category.category_name, "") AS category_name'), DB::raw('IFNULL(tbl_subcategory.subcategory_name, "") AS subcategory_name'), DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                            ->leftJoin('tbl_category', 'tbl_category.id', '=', 'tbl_news.category_id')
                            ->leftJoin('tbl_subcategory', 'tbl_subcategory.id', '=', 'tbl_news.subcategory_id')
                            ->leftJoin('tbl_location', 'tbl_location.id', '=', 'tbl_news.location_id')
                            ->where('tbl_news.status', 1)
                            ->where('tbl_news.language_id', $language_id)
                            ->where(function ($query) {
                                $query->where('tbl_news.show_till', '>=', $this->toDate);
                                $query->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                            });
                        if ($row->category_ids != null && $row->subcategory_ids != null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.category_id', explode(',', $row->category_ids))->WhereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                            });
                        } elseif ($row->category_ids != null && $row->subcategory_ids == null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.category_id', explode(',', $row->category_ids));
                            });
                        } elseif ($row->category_ids == null && $row->subcategory_ids != null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                            });
                        }
                        $nearest_location_measure = $this->nearest_location_measure;
                        // Append condition based on latitude and longitude
                        if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                            $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                        }
                        if ($row->news_type == 'news') {
                            $results->where('tbl_news.description', '!=', '');
                            $result_count = $results->count();
                            $results = $results->orderBy('tbl_news.id', 'DESC')->get();
                        } elseif ($row->news_type == 'videos') {
                            if ($row->videos_type == 'news') {
                                $results->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other']);
                                $result_count = $results->count();
                                $results = $results->orderBy('tbl_news.id', 'DESC')->get();
                            } elseif ($row->videos_type == 'breaking_news') {
                                //1.5 recently_added breaking_news video
                                $result_count = DB::table('tbl_breaking_news')
                                    ->select('tbl_breaking_news.*')
                                    ->where('tbl_breaking_news.language_id', $language_id)
                                    ->whereIn('tbl_breaking_news.content_type', ['video_upload', 'video_youtube', 'video_other'])
                                    ->count();
                                $results = DB::table('tbl_breaking_news')
                                    ->select('tbl_breaking_news.*')
                                    ->where('tbl_breaking_news.language_id', $language_id)
                                    ->whereIn('tbl_breaking_news.content_type', ['video_upload', 'video_youtube', 'video_other'])
                                    ->orderBy('tbl_breaking_news.id', 'DESC')
                                    ->get();
                            }
                        }
                    } elseif ($row->filter_type == 'most_viewed') {
                        $results = DB::table('tbl_news')
                            ->select('tbl_news.*', 'tbl_category.category_name', 'tbl_subcategory.subcategory_name', 'tbl_news_view.viewcount', DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                            ->leftJoin('tbl_category', 'tbl_category.id', '=', 'tbl_news.category_id')
                            ->leftJoin('tbl_subcategory', 'tbl_subcategory.id', '=', 'tbl_news.subcategory_id')
                            ->leftJoin('tbl_location', 'tbl_location.id', '=', 'tbl_news.location_id')
                            ->join(DB::raw('(SELECT news_id, COUNT(*) AS viewcount FROM tbl_news_view GROUP BY news_id) AS tbl_news_view'), function ($join) {
                                $join->on('tbl_news.id', '=', 'tbl_news_view.news_id');
                            })
                            ->where('tbl_news.status', 1)
                            ->where('tbl_news.language_id', $language_id)
                            ->where(function ($query) {
                                $query->where('tbl_news.show_till', '>=', $this->toDate);
                                $query->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                            });
                        if ($row->category_ids != null && $row->subcategory_ids != null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.category_id', explode(',', $row->category_ids))->orWhereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                            });
                        } elseif ($row->category_ids != null && $row->subcategory_ids == null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.category_id', explode(',', $row->category_ids));
                            });
                        } elseif ($row->category_ids == null && $row->subcategory_ids != null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                            });
                        }
                        $nearest_location_measure = $this->nearest_location_measure;
                        if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                            $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                        }
                        if ($row->news_type == 'news') {
                            $results->where('tbl_news.description', '!=', '');
                            $result_count = $results->count();
                            $results = $results->orderBy('tbl_news_view.viewcount', 'DESC')->get();
                        } elseif ($row->news_type == 'videos') {
                            if ($row->videos_type == 'news') {
                                $results->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other']);
                                $result_count = $results->count();
                                $results = $results->orderBy('tbl_news_view.viewcount', 'DESC')->get();
                            } elseif ($row->videos_type == 'breaking_news') {
                                $result_count = DB::table('tbl_breaking_news')
                                    ->select('tbl_breaking_news.*', 'tbl_breaking_news_view.viewcount')
                                    ->where('tbl_breaking_news.language_id', $language_id)
                                    ->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other'])
                                    ->join(DB::raw('(SELECT breaking_news_id, COUNT(*) AS viewcount FROM tbl_breaking_news_view GROUP BY breaking_news_id) AS tbl_breaking_news_view'), function ($join) {
                                        $join->on('tbl_breaking_news.id', '=', 'tbl_breaking_news_view.breaking_news_id');
                                    })
                                    ->count();
                                $results = DB::table('tbl_breaking_news')
                                    ->select('tbl_breaking_news.*', 'tbl_breaking_news_view.viewcount')
                                    ->where('tbl_breaking_news.language_id', $language_id)
                                    ->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other'])
                                    ->join(DB::raw('(SELECT breaking_news_id, COUNT(*) AS viewcount FROM tbl_breaking_news_view GROUP BY breaking_news_id) AS tbl_breaking_news_view'), function ($join) {
                                        $join->on('tbl_breaking_news.id', '=', 'tbl_breaking_news_view.breaking_news_id');
                                    });
                                $results = $results->orderBy('tbl_breaking_news_view.viewcount', 'DESC')->get();
                            }
                        }
                    } elseif ($row->filter_type == 'most_favorite') {
                        //1.9 most_favorite news, video
                        $results = DB::table('tbl_news')
                            ->select('tbl_news.*', 'tbl_category.category_name', 'tbl_subcategory.subcategory_name', 'tbl_bookmark.newscount', DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                            ->leftJoin('tbl_category', 'tbl_category.id', '=', 'tbl_news.category_id')
                            ->leftJoin('tbl_subcategory', 'tbl_subcategory.id', '=', 'tbl_news.subcategory_id')
                            ->leftJoin('tbl_location', 'tbl_location.id', '=', 'tbl_news.location_id')
                            ->where('tbl_news.language_id', $language_id)
                            ->where('tbl_news.status', 1)
                            ->where('tbl_news.description', '!=', '')
                            ->where(function ($query) {
                                $query->where('tbl_news.show_till', '>=', $this->toDate);
                                $query->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                            })
                            ->join(DB::raw('(SELECT news_id, COUNT(*) AS newscount FROM tbl_bookmark GROUP BY news_id) AS tbl_bookmark'), function ($join) {
                                $join->on('tbl_news.id', '=', 'tbl_bookmark.news_id');
                            });
                        if ($row->news_type == 'videos') {
                            $results->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other']);
                        }
                        if ($row->category_ids != null && $row->subcategory_ids != null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.category_id', explode(',', $row->category_ids))->orWhereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                            });
                        } elseif ($row->category_ids != null && $row->subcategory_ids == null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.category_id', explode(',', $row->category_ids));
                            });
                        } elseif ($row->category_ids == null && $row->subcategory_ids != null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                            });
                        } elseif ($row->category_ids == null && $row == null) {
                        }
                        $nearest_location_measure = $this->nearest_location_measure;
                        // Append condition based on latitude and longitude
                        if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                            $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                        }
                        $result_count = $results->count();
                        $results = $results->orderBy('tbl_bookmark.newscount', 'DESC')->get();
                    } elseif ($row->filter_type == 'most_like') {
                        //1.9 most_favorite like, video
                        $results = DB::table('tbl_news')
                            ->select('tbl_news.*', 'tbl_category.category_name', 'tbl_subcategory.subcategory_name', 'tbl_news_like.likecount', DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                            ->leftJoin('tbl_category', 'tbl_category.id', '=', 'tbl_news.category_id')
                            ->leftJoin('tbl_subcategory', 'tbl_subcategory.id', '=', 'tbl_news.subcategory_id')
                            ->leftJoin('tbl_location', 'tbl_location.id', '=', 'tbl_news.location_id')
                            ->where('tbl_news.language_id', $language_id)
                            ->where('tbl_news.status', 1)
                            ->where('tbl_news.description', '!=', '')
                            ->where(function ($query) {
                                $query->where('tbl_news.show_till', '>=', $this->toDate);
                                $query->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                            })
                            ->join(DB::raw('(SELECT news_id, COUNT(*) AS likecount FROM tbl_news_like WHERE status="1" GROUP BY news_id) AS tbl_news_like'), function ($join) {
                                $join->on('tbl_news.id', '=', 'tbl_news_like.news_id');
                            });
                        if ($row->category_ids != null && $row->subcategory_ids != null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.category_id', explode(',', $row->category_ids))->orWhereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                            });
                        } elseif ($row->category_ids != null && $row->subcategory_ids == null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.category_id', explode(',', $row->category_ids));
                            });
                        } elseif ($row->category_ids == null && $row->subcategory_ids != null) {
                            $results->where(function ($query) use ($row) {
                                $query->whereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                            });
                        } elseif ($row->category_ids == null && $row == null) {
                        }
                        if ($row->news_type == 'videos') {
                            $results->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other']);
                        }
                        $nearest_location_measure = $this->nearest_location_measure;
                        // Append condition based on latitude and longitude
                        if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                            $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                        }
                        $result_count = $results->count();
                        $results = $results->orderBy('tbl_news_like.likecount', 'DESC')->get();
                    } elseif ($row->filter_type == 'custom') {
                        $results = DB::table('tbl_news')
                            ->select('tbl_news.*', 'tbl_category.category_name', 'tbl_subcategory.subcategory_name', DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                            ->leftJoin('tbl_category', 'tbl_category.id', '=', 'tbl_news.category_id')
                            ->leftJoin('tbl_subcategory', 'tbl_subcategory.id', '=', 'tbl_news.subcategory_id')
                            ->leftJoin('tbl_location', 'tbl_location.id', '=', 'tbl_news.location_id')
                            ->where('tbl_news.status', 1)
                            ->whereIn('tbl_news.id', explode(',', $row->news_ids))
                            ->where('tbl_news.language_id', $language_id)
                            ->where(function ($query) {
                                $query->where('tbl_news.show_till', '>=', $this->toDate);
                                $query->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                            });
                        $nearest_location_measure = $this->nearest_location_measure;
                        // Append condition based on latitude and longitude
                        if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                            $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                        }
                        //1.10 custom (based on selected category, subcategory)
                        if ($row->news_type == 'news') {
                            $result_count = $results->count();
                            $results = $results->orderBy('tbl_news.id', 'DESC')->get();
                        } elseif ($row->news_type == 'videos') {
                            if ($row->videos_type == 'news') {
                                $results->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other']);
                                $result_count = $results->count();
                                $results = $results->orderBy('tbl_news.id', 'DESC')->get();
                            } elseif ($row->videos_type == 'breaking_news') {
                                //1.10.1 custom breaking_news video
                                $results = DB::table('tbl_breaking_news')
                                    ->select('tbl_breaking_news.*')
                                    ->whereIn('tbl_breaking_news.content_type', ['video_upload', 'video_youtube', 'video_other'])
                                    ->whereIn('tbl_breaking_news.id', explode(',', $row->news_ids))
                                    ->where('tbl_breaking_news.language_id', $language_id)
                                    ->orderBy('tbl_breaking_news.id', 'DESC')
                                    ->get();
                                $result_count = $results->count();
                            }
                        }
                    }
                } elseif ($row->news_type == 'breaking_news') {
                    //2. Breaking News
                    if ($row->filter_type == 'recently_added') {
                        $results = DB::table('tbl_breaking_news')->select('tbl_breaking_news.*')->where('tbl_breaking_news.language_id', $language_id)->orderBy('tbl_breaking_news.id', 'DESC')->get();
                        $result_for_count = DB::table('tbl_breaking_news')->select('tbl_breaking_news.*')->where('tbl_breaking_news.language_id', $language_id)->orderBy('tbl_breaking_news.id', 'DESC')->get();
                        $result_count = $result_for_count->count();
                    } elseif ($row->filter_type == 'most_viewed') {
                        //2.2 Breaking News most_viewed
                        $results = DB::table('tbl_breaking_news')
                            ->select('tbl_breaking_news.*')
                            ->join(DB::raw('(SELECT breaking_news_id, COUNT(*) AS viewcount FROM tbl_breaking_news_view GROUP BY breaking_news_id) AS tbl_breaking_news_view'), function ($join) {
                                $join->on('tbl_breaking_news_view.breaking_news_id', '=', 'tbl_breaking_news.id');
                            })
                            ->where('tbl_breaking_news.language_id', $language_id)
                            ->orderBy('tbl_breaking_news_view.viewcount', 'DESC')
                            ->get();
                        $result_count = $results->count();
                    } elseif ($row->filter_type == 'custom') {
                        $results = DB::table('tbl_breaking_news')
                            ->select('tbl_breaking_news.*')
                            ->whereIn('tbl_breaking_news.id', explode(',', $row->news_ids))
                            ->where('tbl_breaking_news.language_id', $language_id)
                            ->orderBy('tbl_breaking_news.id', 'DESC')
                            ->get();
                        $result_count = $results->count();
                    }
                } elseif ($row->is_based_on_user_choice == '1') {
                    // based_on_user's_choice_section code ** different from above all section //
                    if (Auth::check()) {
                        $user_category = UserCategory::select('id', 'category_id')
                            ->where('user_id', Auth::user()->id)
                            ->first();
                    } else {
                        $user_category = null;
                    }
                    if ($user_category != null) {
                        $results = DB::table('tbl_news')
                            ->select('tbl_news.*', 'tbl_category.category_name', 'tbl_subcategory.subcategory_name', DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                            ->leftJoin('tbl_category', 'tbl_category.id', '=', 'tbl_news.category_id')
                            ->leftJoin('tbl_subcategory', 'tbl_subcategory.id', '=', 'tbl_news.subcategory_id')
                            ->leftJoin('tbl_location', 'tbl_location.id', '=', 'tbl_news.location_id')
                            ->where('tbl_news.language_id', $language_id)
                            ->whereIn('tbl_news.category_id', explode(',', $user_category->category_id))
                            ->where(function ($query) {
                                $query->where('tbl_news.show_till', '>=', $this->toDate);
                                $query->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                            });
                        $nearest_location_measure = $this->nearest_location_measure;
                        // Append condition based on latitude and longitude
                        if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                            $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                        }
                        $result_count = $results->count();
                        $results = $results->orderBy('tbl_news.id', 'DESC')->get();
                    } else {
                        $result_count = 0;
                        $results = collect();
                    }
                }
                if ($results) {
                    foreach ($results as $row2) {
                        if ($row->news_type == 'news' || $row->is_based_on_user_choice == '1') {
                            if ($row2->content_type == 'video_upload') {
                                if (!empty($row2->content_value) && strpos($row2->content_value, 'news_video/') === false) {
                                    $content_value = 'news_video/' . $row2->content_value;
                                } else {
                                    $content_value = $row2->content_value;
                                }
                                $row2->content_value = url(Storage::url($content_value));
                            }
                            if (!empty($row2->image) && strpos($row2->image, 'news/') === false) {
                                $image = 'news/' . $row2->image;
                            } else {
                                $image = $row2->image;
                            }
                            $row2->image = url(Storage::url($image));
                            $img = [];
                            $images = News_image::with('news')
                                ->where('news_id', $row2->id)
                                ->get();

                            $imageArray = $images
                                ->map(function ($image) {
                                    return [
                                        'other_image' => url(Storage::url($image->getOtherImagePathAttribute())),
                                    ];
                                })
                                ->toArray();

                            $row2->images = $imageArray;

                            $news_like = News_like::where('news_id', $row2->id);
                            $row2->total_like = $news_like->clone()->where('status', 1)->count('id');
                            $row2->total_dislike = $news_like->clone()->where('status', 2)->count('id');
                            $news_bookmark = Bookmark::where('news_id', $row2->id);
                            $row2->total_bookmark = $news_bookmark->clone()->count('id');
                            $row2->bookmark = $news_bookmark->clone()->where('user_id', $user_id)->count('id');
                            $row2->total_views = News_view::where('news_id', $row2->id)->count('id');

                            if (isset($row2->tag_id) && !empty($row2->tag_id)) {
                                $row2->tag_id = '';
                                if (isset($row2->tag_id) && !empty($row2->tag_id)) {
                                    $tagNames = Tag::whereIn('id', explode(',', $row2->tag_id))
                                        ->distinct()
                                        ->pluck('tag_name')
                                        ->implode(',');
                                    $row2->tag_name = $tagNames;
                                    $row2->tag_id = !empty($res2) ? $res2[0]->tag_id : $row2->tag_id;
                                }
                            }
                        } elseif ($row->news_type == 'breaking_news') {
                            if (!empty($row2->image) && strpos($row2->image, 'breaking_news/') === false) {
                                $image = 'breaking_news/' . $row2->image;
                            } else {
                                $image = $row2->image;
                            }
                            $row2->image = url(Storage::url($image));
                            if ($row2->content_type == 'video_upload') {
                                if (!empty($row2->content_value) && strpos($row2->content_value, 'breaking_news_video/') === false) {
                                    $content_value = 'breaking_news_video/' . $row2->content_value;
                                } else {
                                    $content_value = $row2->content_value;
                                }
                                $row2->content_value = url(Storage::url($content_value));
                            }
                            $row2->total_views = BreakingNewsView::where('breaking_news_id', $row2->id)->count('id');
                        } elseif ($row->news_type == 'videos') {
                            if ($row->videos_type == 'news') {
                                if (!empty($row2->image) && strpos($row2->image, 'news/') === false) {
                                    $image = 'news/' . $row2->image;
                                } else {
                                    $image = $row2->image;
                                }
                                $row2->image = url(Storage::url($image));
                                if ($row2->content_type == 'video_upload') {
                                    if (!empty($row2->content_value) && strpos($row2->content_value, 'news_video/') === false) {
                                        $content_value = 'news_video/' . $row2->content_value;
                                    } else {
                                        $content_value = $row2->content_value;
                                    }
                                    $row2->content_value = url(Storage::url($content_value));
                                }
                                $img = [];
                                $img = News_image::select('other_image')
                                    ->select('id')
                                    ->where('news_id', $row2->id)
                                    ->get();
                                for ($k = 0; $k < count($img); $k++) {
                                    $img[$k]->other_image = $img[$k]->other_image ? $img[$k]->other_image : '';
                                    $img[$k]->id = $img[$k]->id;
                                }
                                $row2->images = $img;

                                $news_like = News_like::where('news_id', $row2->id);
                                $row2->total_like = $news_like->clone()->where('status', 1)->count('id');
                                $row2->total_dislike = $news_like->clone()->where('status', 2)->count('id');
                                $news_bookmark = Bookmark::where('news_id', $row2->id);
                                $row2->total_bookmark = $news_bookmark->clone()->count('id');
                                $row2->bookmark = $news_bookmark->clone()->where('user_id', $user_id)->count('id');
                                $row2->total_views = News_view::where('news_id', $row2->id)->count('id');
                                if (isset($row2->tag_id) && !empty($row2->tag_id)) {
                                    $row2->tag_id = '';
                                    if (isset($row2->tag_id) && !empty($row2->tag_id)) {
                                        $tagNames = Tag::whereIn('id', explode(',', $row2->tag_id))
                                            ->distinct()
                                            ->pluck('tag_name')
                                            ->implode(',');
                                        $row2->tag_name = $tagNames;
                                        $row2->tag_id = !empty($res2) ? $res2[0]->tag_id : $row2->tag_id;
                                    }
                                }
                            } elseif ($row->videos_type == 'breaking_news') {
                                if (!empty($row2->image) && strpos($row2->image, 'breaking_news/') === false) {
                                    $image = 'breaking_news/' . $row2->image;
                                } else {
                                    $image = $row2->image;
                                }
                                $row2->image = url(Storage::url($image));
                                if ($row2->content_type == 'video_upload') {
                                    if (!empty($row2->content_value) && strpos($row2->content_value, 'breaking_news_video/') === false) {
                                        $content_value = 'breaking_news_video/' . $row2->content_value;
                                    } else {
                                        $content_value = $row2->content_value;
                                    }
                                    $row2->content_value = url(Storage::url($content_value));
                                }
                                $row2->total_views = BreakingNewsView::where('breaking_news_id', $row2->id)->count('id');
                            }
                        }
                    }
                    $total1 = $result_count;
                    $data[$key]->news_type = $data[$key]->is_based_on_user_choice == '1' ? 'user_choice' : $data[$key]->news_type;
                    $content = $data[$key]->is_based_on_user_choice == '1' ? 'news' : $data[$key]->news_type;
                    $content_total = $data[$key]->is_based_on_user_choice == '1' ? 'news_total' : $data[$key]->news_type . '_total';
                    $data[$key]->$content_total = $total1;
                    $data[$key]->$content = $results;
                    $section_id = $data[$key]->id;
                    $ad_space = AdSpaces::where('ad_featured_section_id', $section_id)->where('status', 1)->first();
                    if (!empty($ad_space)) {
                        $row->ad_spaces = $ad_space;
                    }
                } else {
                    $content = $data[$key]->news_type;
                    $content_total = $data[$key]->news_type . '_total';
                    $data[$key]->$content_total = 0;
                    $data[$key]->$content = $results;
                }
            }
            $response = [
                'error' => false,
                // 'total' => count($data1),
                'data' => $data,
            ];
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getFeaturedSections(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $user_id = Auth::check() ? Auth::user()->id : 0;
            $language_id = $request->language_id;
            $news_type = $request->news_type ?? '';
            $style_web = $request->style_web ?? '';
            $latitude = $request->latitude ?? 0;
            $longitude = $request->longitude ?? 0;

            $res = FeaturedSections::where('language_id', $language_id)->where('status', 1);
            if ($request->section_id) {
                $res = $res->where('id', $request->section_id);
            } elseif ($request->slug) {
                $res = $res->where('slug', $request->slug);
            } else if (!empty($news_type) && !empty($style_web)) {
                $res = $res->where('news_type', $news_type)->where('style_web', $style_web);
            }
            $total = $res->clone()->count('id');
            if ($total) {
                $data = $res->clone()->orderBy('row_order', 'ASC')->get();
                foreach ($data as $key => $row) {
                    if ($row->news_type == 'news' || $row->news_type == 'videos') {
                        if ($row->filter_type == 'most_commented') {
                            $results = DB::table('tbl_news')
                                ->select('tbl_news.*', 'tbl_category.category_name', DB::raw('IFNULL(tbl_subcategory.subcategory_name, "") AS subcategory_name'), 'tbl_comment.newscount', DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                                ->join('tbl_category', 'tbl_news.category_id', '=', 'tbl_category.id')
                                ->leftJoin('tbl_subcategory', 'tbl_news.subcategory_id', '=', 'tbl_subcategory.id')
                                ->leftJoin('tbl_location', 'tbl_news.location_id', '=', 'tbl_location.id')
                                ->where('tbl_news.status', 1)->where('tbl_news.language_id', $language_id)
                                ->where(function ($q) {
                                    $q->where('tbl_news.show_till', '>=', $this->toDate)->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                                })
                                ->join(DB::raw('(SELECT news_id, COUNT(*) AS newscount FROM tbl_comment GROUP BY news_id) AS tbl_comment'), function ($join) {
                                    $join->on('tbl_news.id', '=', 'tbl_comment.news_id');
                                });
                            if ($row->category_ids != null && $row->subcategory_ids != null) {
                                $results->where(function ($q1) use ($row) {
                                    $q1->whereIn('tbl_news.category_id', explode(',', $row->category_ids))->orWhereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                                });
                            } elseif ($row->category_ids != null && $row->subcategory_ids == null) {
                                $results->whereIn('tbl_news.category_id', explode(',', $row->category_ids));
                            } elseif ($row->category_ids == null && $row->subcategory_ids != null) {
                                $results->whereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                            }
                            if ($row->news_type == 'videos' && $row->videos_type == 'news') {
                                $results->where('tbl_news.description', '!=', '')->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other']);
                            }
                            $nearest_location_measure = $this->nearest_location_measure;
                            if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                                $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                            }
                            $orderby = 'tbl_comment.newscount';
                            $result_count = $results->count();
                            if ($request->limit) {
                                $offset = $request->offset ?? 0;
                                $limit = $request->limit;
                                $results = $results->skip($offset)->take($limit);
                            }
                            $results = $results->orderBy($orderby, 'DESC')->get();
                            // $query = str_replace(array('?'), array('\'%s\''), $results->toSql());
                            // return vsprintf($query, $results->getBindings());
                        } elseif ($row->filter_type == 'recently_added') {
                            $results = DB::table('tbl_news')
                                ->select('tbl_news.*', 'tbl_category.category_name', DB::raw('IFNULL(tbl_subcategory.subcategory_name, "") AS subcategory_name'), DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                                ->join('tbl_category', 'tbl_news.category_id', '=', 'tbl_category.id')
                                ->leftJoin('tbl_subcategory', 'tbl_news.subcategory_id', '=', 'tbl_subcategory.id')
                                ->leftJoin('tbl_location', 'tbl_news.location_id', '=', 'tbl_location.id')
                                ->where('tbl_news.status', 1)->where('tbl_news.language_id', $language_id)
                                ->where(function ($q) {
                                    $q->where('tbl_news.show_till', '>=', $this->toDate)->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                                });
                            if ($row->category_ids != null && $row->subcategory_ids != null) {
                                $results->where(function ($q1) use ($row) {
                                    $q1->whereIn('tbl_news.category_id', explode(',', $row->category_ids))->WhereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                                });
                            } elseif ($row->category_ids != null && $row->subcategory_ids == null) {
                                $results->where(function ($q1) use ($row) {
                                    $q1->whereIn('tbl_news.category_id', explode(',', $row->category_ids));
                                });
                            } elseif ($row->category_ids == null && $row->subcategory_ids != null) {
                                $results->where(function ($q1) use ($row) {
                                    $q1->whereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                                });
                            }
                            $nearest_location_measure = $this->nearest_location_measure;
                            // Append condition based on latitude and longitude
                            if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                                $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                            }
                            if ($row->news_type == 'news') {
                                $results->where('tbl_news.description', '!=', '');
                                $result_count = $results->count();
                                if ($request->limit) {
                                    $offset = $request->offset ?? 0;
                                    $limit = $request->limit;
                                    $results = $results->skip($offset)->take($limit);
                                }
                                $results = $results->orderBy('tbl_news.id', 'DESC')->get();
                            } elseif ($row->news_type == 'videos') {
                                if ($row->videos_type == 'news') {
                                    $results->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other']);
                                    $result_count = $results->count();
                                    if ($request->limit) {
                                        $offset = $request->offset ?? 0;
                                        $limit = $request->limit;
                                        $results = $results->skip($offset)->take($limit);
                                    }
                                    $results = $results->orderBy('tbl_news.id', 'DESC')->get();
                                } elseif ($row->videos_type == 'breaking_news') {
                                    //1.5 recently_added breaking_news video
                                    $breaking_news = DB::table('tbl_breaking_news')->select('tbl_breaking_news.*')->where('tbl_breaking_news.language_id', $language_id)->whereIn('tbl_breaking_news.content_type', ['video_upload', 'video_youtube', 'video_other']);
                                    $result_count = $breaking_news->clone()->count();
                                    $results = $breaking_news->clone();
                                    if ($request->limit) {
                                        $offset = $request->offset ?? 0;
                                        $limit = $request->limit;
                                        $results = $results->skip($offset)->take($limit);
                                    }
                                    $results = $results->orderBy('tbl_breaking_news.id', 'DESC')->get();
                                }
                            }
                        } elseif ($row->filter_type == 'most_viewed') {
                            $results = DB::table('tbl_news')
                                ->select('tbl_news.*', 'tbl_category.category_name', DB::raw('IFNULL(tbl_subcategory.subcategory_name, "") AS subcategory_name'), 'tbl_news_view.viewcount', DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                                ->join('tbl_category', 'tbl_news.category_id', '=', 'tbl_category.id')
                                ->leftJoin('tbl_subcategory', 'tbl_news.subcategory_id', '=', 'tbl_subcategory.id')
                                ->leftJoin('tbl_location', 'tbl_news.location_id', '=', 'tbl_location.id')
                                ->join(DB::raw('(SELECT news_id, COUNT(*) AS viewcount FROM tbl_news_view GROUP BY news_id) AS tbl_news_view'), function ($join) {
                                    $join->on('tbl_news.id', '=', 'tbl_news_view.news_id');
                                })
                                ->where('tbl_news.status', 1)->where('tbl_news.language_id', $language_id)
                                ->where(function ($q) {
                                    $q->where('tbl_news.show_till', '>=', $this->toDate)->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                                });
                            if ($row->category_ids != null && $row->subcategory_ids != null) {
                                $results->where(function ($q1) use ($row) {
                                    $q1->whereIn('tbl_news.category_id', explode(',', $row->category_ids))->orWhereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                                });
                            } elseif ($row->category_ids != null && $row->subcategory_ids == null) {
                                $results->where(function ($q1) use ($row) {
                                    $q1->whereIn('tbl_news.category_id', explode(',', $row->category_ids));
                                });
                            } elseif ($row->category_ids == null && $row->subcategory_ids != null) {
                                $results->where(function ($q1) use ($row) {
                                    $q1->whereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                                });
                            }
                            $nearest_location_measure = $this->nearest_location_measure;
                            if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                                $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                            }
                            if ($row->news_type == 'news') {
                                $results->where('tbl_news.description', '!=', '');
                                $result_count = $results->count();
                                if ($request->limit) {
                                    $offset = $request->offset ?? 0;
                                    $limit = $request->limit;
                                    $results = $results->skip($offset)->take($limit);
                                }
                                $results = $results->orderBy('tbl_news_view.viewcount', 'DESC')->get();
                            } elseif ($row->news_type == 'videos') {
                                if ($row->videos_type == 'news') {
                                    $results->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other']);
                                    $result_count = $results->count();
                                    if ($request->limit) {
                                        $offset = $request->offset ?? 0;
                                        $limit = $request->limit;
                                        $results = $results->skip($offset)->take($limit);
                                    }
                                    $results = $results->orderBy('tbl_news_view.viewcount', 'DESC')->get();
                                } elseif ($row->videos_type == 'breaking_news') {
                                    $breaking_news = DB::table('tbl_breaking_news')
                                        ->select('tbl_breaking_news.*', 'tbl_breaking_news_view.viewcount')
                                        ->where('tbl_breaking_news.language_id', $language_id)
                                        ->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other'])
                                        ->join(DB::raw('(SELECT breaking_news_id, COUNT(*) AS viewcount FROM tbl_breaking_news_view GROUP BY breaking_news_id) AS tbl_breaking_news_view'), function ($join) {
                                            $join->on('tbl_breaking_news.id', '=', 'tbl_breaking_news_view.breaking_news_id');
                                        });
                                    $result_count = $breaking_news->clone()->count();
                                    $results = $breaking_news->clone();
                                    if ($request->limit) {
                                        $offset = $request->offset ?? 0;
                                        $limit = $request->limit;
                                        $results = $results->skip($offset)->take($limit);
                                    }
                                    $results = $results->orderBy('tbl_breaking_news_view.viewcount', 'DESC')->get();
                                }
                            }
                        } elseif ($row->filter_type == 'most_favorite') {
                            //1.9 most_favorite news, video
                            $results = DB::table('tbl_news')
                                ->select('tbl_news.*', 'tbl_category.category_name', DB::raw('IFNULL(tbl_subcategory.subcategory_name, "") AS subcategory_name'), 'tbl_bookmark.newscount', DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                                ->join('tbl_category', 'tbl_news.category_id', '=', 'tbl_category.id')
                                ->leftJoin('tbl_subcategory', 'tbl_news.subcategory_id', '=', 'tbl_subcategory.id')
                                ->leftJoin('tbl_location', 'tbl_news.location_id', '=', 'tbl_location.id')
                                ->where('tbl_news.status', 1)->where('tbl_news.language_id', $language_id)->where('tbl_news.description', '!=', '')
                                ->where(function ($q) {
                                    $q->where('tbl_news.show_till', '>=', $this->toDate)->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                                })
                                ->join(DB::raw('(SELECT news_id, COUNT(*) AS newscount FROM tbl_bookmark GROUP BY news_id) AS tbl_bookmark'), function ($join) {
                                    $join->on('tbl_news.id', '=', 'tbl_bookmark.news_id');
                                });
                            if ($row->news_type == 'videos') {
                                $results->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other']);
                            }
                            if ($row->category_ids != null && $row->subcategory_ids != null) {
                                $results->where(function ($q1) use ($row) {
                                    $q1->whereIn('tbl_news.category_id', explode(',', $row->category_ids))->orWhereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                                });
                            } elseif ($row->category_ids != null && $row->subcategory_ids == null) {
                                $results->where(function ($q1) use ($row) {
                                    $q1->whereIn('tbl_news.category_id', explode(',', $row->category_ids));
                                });
                            } elseif ($row->category_ids == null && $row->subcategory_ids != null) {
                                $results->where(function ($q1) use ($row) {
                                    $q1->whereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                                });
                            } elseif ($row->category_ids == null && $row == null) {
                            }
                            $nearest_location_measure = $this->nearest_location_measure;
                            // Append condition based on latitude and longitude
                            if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                                $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                            }
                            $result_count = $results->count();
                            if ($request->limit) {
                                $offset = $request->offset ?? 0;
                                $limit = $request->limit;
                                $results = $results->skip($offset)->take($limit);
                            }
                            $results = $results->orderBy('tbl_bookmark.newscount', 'DESC')->get();
                        } elseif ($row->filter_type == 'most_like') {
                            //1.9 most_favorite like, video
                            $results = DB::table('tbl_news')
                                ->select('tbl_news.*', 'tbl_category.category_name', DB::raw('IFNULL(tbl_subcategory.subcategory_name, "") AS subcategory_name'), 'tbl_news_like.likecount', DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                                ->join('tbl_category', 'tbl_news.category_id', '=', 'tbl_category.id')
                                ->leftJoin('tbl_subcategory', 'tbl_news.subcategory_id', '=', 'tbl_subcategory.id')
                                ->leftJoin('tbl_location', 'tbl_news.location_id', '=', 'tbl_location.id')
                                ->where('tbl_news.status', 1)->where('tbl_news.language_id', $language_id)->where('tbl_news.description', '!=', '')
                                ->where(function ($q) {
                                    $q->where('tbl_news.show_till', '>=', $this->toDate)->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                                })
                                ->join(DB::raw('(SELECT news_id, COUNT(*) AS likecount FROM tbl_news_like WHERE status="1" GROUP BY news_id) AS tbl_news_like'), function ($join) {
                                    $join->on('tbl_news.id', '=', 'tbl_news_like.news_id');
                                });
                            if ($row->category_ids != null && $row->subcategory_ids != null) {
                                $results->where(function ($q1) use ($row) {
                                    $q1->whereIn('tbl_news.category_id', explode(',', $row->category_ids))->orWhereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                                });
                            } elseif ($row->category_ids != null && $row->subcategory_ids == null) {
                                $results->where(function ($q1) use ($row) {
                                    $q1->whereIn('tbl_news.category_id', explode(',', $row->category_ids));
                                });
                            } elseif ($row->category_ids == null && $row->subcategory_ids != null) {
                                $results->where(function ($q1) use ($row) {
                                    $q1->whereIn('tbl_news.subcategory_id', explode(',', $row->subcategory_ids));
                                });
                            }
                            if ($row->news_type == 'videos') {
                                $results->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other']);
                            }
                            $nearest_location_measure = $this->nearest_location_measure;
                            // Append condition based on latitude and longitude
                            if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                                $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                            }
                            $result_count = $results->count();
                            if ($request->limit) {
                                $offset = $request->offset ?? 0;
                                $limit = $request->limit;
                                $results = $results->skip($offset)->take($limit);
                            }
                            $results = $results->orderBy('tbl_news_like.likecount', 'DESC')->get();
                        } elseif ($row->filter_type == 'custom') {
                            $results = DB::table('tbl_news')
                                ->select('tbl_news.*', 'tbl_category.category_name', DB::raw('IFNULL(tbl_subcategory.subcategory_name, "") AS subcategory_name'), DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                                ->join('tbl_category', 'tbl_news.category_id', '=', 'tbl_category.id')
                                ->leftJoin('tbl_subcategory', 'tbl_news.subcategory_id', '=', 'tbl_subcategory.id')
                                ->leftJoin('tbl_location', 'tbl_news.location_id', '=', 'tbl_location.id')
                                ->where('tbl_news.status', 1)->whereIn('tbl_news.id', explode(',', $row->news_ids))->where('tbl_news.language_id', $language_id)
                                ->where(function ($q) {
                                    $q->where('tbl_news.show_till', '>=', $this->toDate)->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                                });
                            $nearest_location_measure = $this->nearest_location_measure;
                            // Append condition based on latitude and longitude
                            if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                                $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                            }
                            //1.10 custom (based on selected category, subcategory)
                            if ($row->news_type == 'news') {
                                $result_count = $results->count('tbl_news.id');
                                if ($request->limit) {
                                    $offset = $request->offset ?? 0;
                                    $limit = $request->limit;
                                    $results = $results->skip($offset)->take($limit);
                                }
                                $results = $results->orderBy('tbl_news.id', 'DESC')->get();
                            } elseif ($row->news_type == 'videos') {
                                if ($row->videos_type == 'news') {
                                    $results->whereIn('tbl_news.content_type', ['video_upload', 'video_youtube', 'video_other']);
                                    $result_count = $results->count();
                                    if ($request->limit) {
                                        $offset = $request->offset ?? 0;
                                        $limit = $request->limit;
                                        $results = $results->skip($offset)->take($limit);
                                    }
                                    $results = $results->orderBy('tbl_news.id', 'DESC')->get();
                                } elseif ($row->videos_type == 'breaking_news') {
                                    //1.10.1 custom breaking_news video
                                    $breaking_news = DB::table('tbl_breaking_news')->select('tbl_breaking_news.*')
                                        ->whereIn('tbl_breaking_news.content_type', ['video_upload', 'video_youtube', 'video_other'])
                                        ->whereIn('tbl_breaking_news.id', explode(',', $row->news_ids))->where('tbl_breaking_news.language_id', $language_id);
                                    $result_count = $breaking_news->clone()->count();
                                    $results = $breaking_news->clone();
                                    if ($request->limit) {
                                        $offset = $request->offset ?? 0;
                                        $limit = $request->limit;
                                        $results = $results->skip($offset)->take($limit);
                                    }
                                    $results = $results->orderBy('tbl_breaking_news.id', 'DESC')->get();
                                }
                            }
                        }
                    } elseif ($row->news_type == 'breaking_news') {
                        //2. Breaking News
                        if ($row->filter_type == 'recently_added') {
                            $breaking_news = DB::table('tbl_breaking_news')->select('tbl_breaking_news.*')->where('tbl_breaking_news.language_id', $language_id);
                            $result_count = $breaking_news->clone()->count();
                            $results = $breaking_news->clone();
                            if ($request->limit) {
                                $offset = $request->offset ?? 0;
                                $limit = $request->limit;
                                $results = $results->skip($offset)->take($limit);
                            }
                            $results = $results->orderBy('tbl_breaking_news.id', 'DESC')->get();
                        } elseif ($row->filter_type == 'most_viewed') {
                            //2.2 Breaking News most_viewed
                            $results = DB::table('tbl_breaking_news')
                                ->select('tbl_breaking_news.*')
                                ->join(DB::raw('(SELECT breaking_news_id, COUNT(*) AS viewcount FROM tbl_breaking_news_view GROUP BY breaking_news_id) AS tbl_breaking_news_view'), function ($join) {
                                    $join->on('tbl_breaking_news_view.breaking_news_id', '=', 'tbl_breaking_news.id');
                                })
                                ->where('tbl_breaking_news.language_id', $language_id);
                            $result_count = $results->count();
                            if ($request->limit) {
                                $offset = $request->offset ?? 0;
                                $limit = $request->limit;
                                $results = $results->skip($offset)->take($limit);
                            }
                            $result_count = $results->orderBy('tbl_breaking_news_view.viewcount', 'DESC')->get();
                        } elseif ($row->filter_type == 'custom') {
                            $results = DB::table('tbl_breaking_news')
                                ->select('tbl_breaking_news.*')
                                ->whereIn('tbl_breaking_news.id', explode(',', $row->news_ids))
                                ->where('tbl_breaking_news.language_id', $language_id);
                            $result_count = $results->count();
                            if ($request->limit) {
                                $offset = $request->offset ?? 0;
                                $limit = $request->limit;
                                $results = $results->skip($offset)->take($limit);
                            }
                            $results = $results->orderBy('tbl_breaking_news.id', 'DESC')->get();
                        }
                    } elseif ($row->is_based_on_user_choice == '1') {
                        // based_on_user's_choice_section code ** different from above all section //
                        if (Auth::check()) {
                            $user_category = UserCategory::select('id', 'category_id')
                                ->where('user_id', Auth::user()->id)
                                ->first();
                        } else {
                            $user_category = null;
                        }
                        if ($user_category != null) {
                            $results = DB::table('tbl_news')
                                ->select('tbl_news.*', 'tbl_category.category_name', 'tbl_subcategory.subcategory_name', DB::raw('SQRT(POW(111.2 * (tbl_location.latitude - ' . $latitude . '), 2) + POW(111.2 * (' . $longitude . ' - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance'))
                                ->join('tbl_category', 'tbl_news.category_id', '=', 'tbl_category.id')
                                ->leftJoin('tbl_subcategory', 'tbl_news.subcategory_id', '=', 'tbl_subcategory.id')
                                ->leftJoin('tbl_location', 'tbl_news.location_id', '=', 'tbl_location.id')
                                ->where('tbl_news.language_id', $language_id)
                                ->whereIn('tbl_news.category_id', explode(',', $user_category->category_id))
                                ->where(function ($q) {
                                    $q->where('tbl_news.show_till', '>=', $this->toDate)->orWhereRaw("CAST(tbl_news.show_till AS CHAR(20)) = '0000-00-00'");
                                });
                            $nearest_location_measure = $this->nearest_location_measure;
                            // Append condition based on latitude and longitude
                            if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                                $results->havingRaw("distance < $nearest_location_measure OR tbl_news.location_id = 0");
                            }
                            $result_count = $results->count();
                            if ($request->limit) {
                                $offset = $request->offset ?? 0;
                                $limit = $request->limit;
                                $results = $results->skip($offset)->take($limit);
                            }
                            $results = $results->orderBy('tbl_news.id', 'DESC')->get();
                        } else {
                            $result_count = 0;
                            $results = collect();
                        }
                    }
                    if ($results) {
                        foreach ($results as $row2) {
                            if ($row->news_type == 'news' || $row->is_based_on_user_choice == '1') {
                                if ($row2->content_type == 'video_upload') {
                                    if (!empty($row2->content_value) && strpos($row2->content_value, 'news_video/') === false) {
                                        $content_value = 'news_video/' . $row2->content_value;
                                    } else {
                                        $content_value = $row2->content_value;
                                    }
                                    $row2->content_value = url(Storage::url($content_value));
                                }
                                if (!empty($row2->image) && strpos($row2->image, 'news/') === false) {
                                    $image = 'news/' . $row2->image;
                                } else {
                                    $image = $row2->image;
                                }
                                $row2->image = url(Storage::url($image));
                                $img = [];
                                $images = News_image::with('news')->where('news_id', $row2->id)->get();
                                $imageArray = $images->map(function ($image) {
                                    return [
                                        'other_image' => url(Storage::url($image->getOtherImagePathAttribute())),
                                    ];
                                })->toArray();

                                $row2->images = $imageArray;

                                $news_like = News_like::where('news_id', $row2->id);
                                $row2->total_like = $news_like->clone()->where('status', 1)->count('id');
                                $row2->total_dislike = $news_like->clone()->where('status', 2)->count('id');
                                $news_bookmark = Bookmark::where('news_id', $row2->id);
                                $row2->total_bookmark = $news_bookmark->clone()->count('id');
                                $row2->bookmark = $news_bookmark->clone()->where('user_id', $user_id)->count('id');
                                $row2->total_views = News_view::where('news_id', $row2->id)->count('id');
                                if (isset($row2->tag_id) && !empty($row2->tag_id)) {
                                    $row2->tag_id = '';
                                    if (isset($row2->tag_id) && !empty($row2->tag_id)) {
                                        $tagNames = Tag::whereIn('id', explode(',', $row2->tag_id))
                                            ->distinct()
                                            ->pluck('tag_name')
                                            ->implode(',');
                                        $row2->tag_name = $tagNames;
                                        $row2->tag_id = !empty($res2) ? $res2[0]->tag_id : $row2->tag_id;
                                    }
                                }
                            } elseif ($row->news_type == 'breaking_news') {
                                if (!empty($row2->image) && strpos($row2->image, 'breaking_news/') === false) {
                                    $image = 'breaking_news/' . $row2->image;
                                } else {
                                    $image = $row2->image;
                                }
                                $row2->image = url(Storage::url($image));
                                if ($row2->content_type == 'video_upload') {
                                    if (!empty($row2->content_value) && strpos($row2->content_value, 'breaking_news_video/') === false) {
                                        $content_value = 'breaking_news_video/' . $row2->content_value;
                                    } else {
                                        $content_value = $row2->content_value;
                                    }
                                    $row2->content_value = url(Storage::url($content_value));
                                }
                                $row2->total_views = BreakingNewsView::where('breaking_news_id', $row2->id)->count('id');
                            } elseif ($row->news_type == 'videos') {
                                if ($row->videos_type == 'news') {
                                    if (!empty($row2->image) && strpos($row2->image, 'news/') === false) {
                                        $image = 'news/' . $row2->image;
                                    } else {
                                        $image = $row2->image;
                                    }
                                    $row2->image = url(Storage::url($image));
                                    if ($row2->content_type == 'video_upload') {
                                        if (!empty($row2->content_value) && strpos($row2->content_value, 'news_video/') === false) {
                                            $content_value = 'news_video/' . $row2->content_value;
                                        } else {
                                            $content_value = $row2->content_value;
                                        }
                                        $row2->content_value = url(Storage::url($content_value));
                                    }
                                    $img = [];
                                    $img = News_image::select('other_image')
                                        ->select('id')
                                        ->where('news_id', $row2->id)
                                        ->get();
                                    for ($k = 0; $k < count($img); $k++) {
                                        $img[$k]->other_image = $img[$k]->other_image ? $img[$k]->other_image : '';
                                        $img[$k]->id = $img[$k]->id;
                                    }
                                    $row2->images = $img;

                                    $news_like = News_like::where('news_id', $row2->id);
                                    $row2->total_like = $news_like->clone()->where('status', 1)->count('id');
                                    $row2->total_dislike = $news_like->clone()->where('status', 2)->count('id');
                                    $news_bookmark = Bookmark::where('news_id', $row2->id);
                                    $row2->total_bookmark = $news_bookmark->clone()->count('id');
                                    $row2->bookmark = $news_bookmark->clone()->where('user_id', $user_id)->count('id');
                                    $row2->total_views = News_view::where('news_id', $row2->id)->count('id');
                                    if (isset($row2->tag_id) && !empty($row2->tag_id)) {
                                        $row2->tag_id = '';
                                        if (isset($row2->tag_id) && !empty($row2->tag_id)) {
                                            $tagNames = Tag::whereIn('id', explode(',', $row2->tag_id))
                                                ->distinct()
                                                ->pluck('tag_name')
                                                ->implode(',');
                                            $row2->tag_name = $tagNames;
                                            $row2->tag_id = !empty($res2) ? $res2[0]->tag_id : $row2->tag_id;
                                        }
                                    }
                                } elseif ($row->videos_type == 'breaking_news') {
                                    if (!empty($row2->image) && strpos($row2->image, 'breaking_news/') === false) {
                                        $image = 'breaking_news/' . $row2->image;
                                    } else {
                                        $image = $row2->image;
                                    }
                                    $row2->image = url(Storage::url($image));
                                    if ($row2->content_type == 'video_upload') {
                                        if (!empty($row2->content_value) && strpos($row2->content_value, 'breaking_news_video/') === false) {
                                            $content_value = 'breaking_news_video/' . $row2->content_value;
                                        } else {
                                            $content_value = $row2->content_value;
                                        }
                                        $row2->content_value = url(Storage::url($content_value));
                                    }
                                    $row2->total_views = BreakingNewsView::where('breaking_news_id', $row2->id)->count('id');
                                }
                            }
                        }
                        $total1 = $result_count;
                        $data[$key]->news_type = $data[$key]->is_based_on_user_choice == '1' ? 'user_choice' : $data[$key]->news_type;
                        $content = $data[$key]->is_based_on_user_choice == '1' ? 'news' : $data[$key]->news_type;
                        $content_total = $data[$key]->is_based_on_user_choice == '1' ? 'news_total' : $data[$key]->news_type . '_total';
                        $data[$key]->$content_total = $total1;
                        $data[$key]->$content = $results;
                        $section_id = $data[$key]->id;
                        $ad_space = AdSpaces::where('ad_featured_section_id', $section_id)->where('status', 1)->first();
                        if (!empty($ad_space)) {
                            $row->ad_spaces = $ad_space;
                        }
                    } else {
                        $content = $data[$key]->news_type;
                        $content_total = $data[$key]->news_type . '_total';
                        $data[$key]->$content_total = 0;
                        $data[$key]->$content = $results;
                    }
                }

                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $data,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getBreakingNews(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }
            $sql = BreakingNews::where('language_id', $request->language_id);
            if ($request->slug) {
                $sql->where('slug', $request->slug);
            }
            $res = $sql->orderBy('id', 'DESC')->get();
            if (!$res->isEmpty()) {
                for ($i = 0; $i < count($res); $i++) {
                    $res[$i]->total_views = BreakingNewsView::where('breaking_news_id', $res[$i]->id)->count();
                }
                $response = [
                    'error' => false,
                    'data' => $res,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getNews(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }

            $language_id = $request->language_id;
            $user_id = Auth::check() ? Auth::user()->id : 0;

            $offset = $request->offset ?? 0;
            $limit = $request->limit ?? 10;
            $latitude = $request->latitude ?? 0;
            $longitude = $request->longitude ?? 0;
            $get_user_news = $request->get_user_news ?? 0;

            $news = News::with('category:id,category_name', 'sub_category:id,subcategory_name', 'location:id,location_name,latitude,longitude', 'images')
                ->where(function ($q) {
                    $q->where('show_till', '>=', $this->toDate)->orWhere('show_till', '0000-00-00');
                })->where('status', 1)->where('language_id', $language_id);

            if ($get_user_news != 0) {
                $news->where('user_id', $user_id)->where('user_id', '!=', 0);
            }
            if ($request->id) {
                $news->where('id', $request->id);
            }
            if ($request->slug) {
                $news->where('slug', $request->slug);
            }
            if ($request->category_id) {
                $news->where('category_id', $request->category_id)->where('description', '!=', '');
            }
            if ($request->category_slug) {
                $category_id = Category::select('id')->where('slug', $request->category_slug)->pluck('id')->first();
                $news->where('category_id', $category_id);
            }
            if ($request->subcategory_id) {
                $news->where('subcategory_id', $request->subcategory_id)->where('description', '!=', '');
            }
            if ($request->subcategory_slug) {
                $subcategory_id = SubCategory::select('id')->where('slug', $request->subcategory_slug)->pluck('id')->first();
                $news->where('subcategory_id', $subcategory_id);
            }
            if ($request->tag_id) {
                $tag_ids = $request->tag_id; // Assuming it's a string like "4,2"
                $news->whereIn('tag_id', explode(',', $tag_ids));
            }
            if ($request->tag_slug) {
                $tag_ids = Tag::select('id')->where('slug', $request->tag_slug)->pluck('id')->first();
                $news->whereIn('tag_id', explode(',', $tag_ids));
            }
            if ($request->search) {
                $search = $request->search;
                $news->where(function ($q) use ($search) {
                    $q->where('tbl_news.title', 'LIKE', "%{$search}%");
                });
            }
            $news->select('tbl_news.*');
            if (isset($request->latitude) && isset($request->longitude)) {
                $news->join('tbl_location', 'tbl_news.location_id', '=', 'tbl_location.id', 'left')
                    ->selectRaw('SQRT(POW(111.2 * (tbl_location.latitude - ?), 2) + POW(111.2 * (? - tbl_location.longitude) * COS(RADIANS(tbl_location.latitude) / 57.3), 2)) AS distance', [$latitude, $longitude])
                    ->where(function ($q1) use ($latitude, $longitude) {
                        $q1->having(DB::raw('distance <' . $this->nearest_location_measure . ' OR tbl_news.location_id=. 0'));
                    })
                    ->orderByRaw('CASE WHEN distance IS NULL THEN 1 ELSE 0 END, distance ASC');
            } else {
                $news->orderBy('tbl_news.id', 'DESC'); // Adjust the column name to 'tbl_news.id' if needed
            }

            $total = $news->clone()->count();
            if ($total) {
                $res = $news->clone()->skip($offset)->take($limit)->get();

                // Calculate and set the 'distance' for each news item
                $res->each(function ($item) use ($latitude, $longitude, $user_id) {
                    $news_like = News_like::where('news_id', $item->id);
                    $item->total_like = $news_like->clone()->where('status', 1)->count('id');
                    $item->total_dislike = $news_like->clone()->where('status', 2)->count('id');
                    $item->like = $news_like->clone()->where('status', 1)->where('user_id', $user_id)->count('id');
                    $item->dislike = $news_like->clone()->where('status', 2)->where('user_id', $user_id)->count('id');
                    $news_bookmark = Bookmark::where('news_id', $item->id);
                    $item->total_bookmark = $news_bookmark->clone()->count('id');
                    $item->bookmark = $news_bookmark->clone()->where('user_id', $user_id)->count('id');
                    $item->total_views = News_view::where('news_id', $item->id)->count('id');

                    if ($item->content_type == 'video_upload') {
                        if (!empty($item->content_value) && strpos($item->content_value, 'news_video/') === false) {
                            $content_value = 'news_video/' . $item->content_value;
                        } else {
                            $content_value = $item->content_value;
                        }
                        $item->content_value = url(Storage::url('/' . $content_value));
                    }
                    if (isset($item->tag_id) && !empty($item->tag_id)) {
                        $tagNames = Tag::whereIn('id', explode(',', $item->tag_id))
                            ->distinct()
                            ->pluck('tag_name')
                            ->implode(',');
                        $item->tag_name = $tagNames;
                        $item->tag_id = !empty($res2) ? $res2[0]->tag_id : $item->tag_id;

                        $item->tag = Tag::select('id', 'tag_name', 'slug')->whereIn('id', explode(',', $item->tag_id))->get();
                    }
                });
                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $res,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $language_id = $request->language_id;
            $offset = $request->offset ?? 0;
            $limit = $request->limit ?? 10;

            $data = SendNotification::with([
                'news' => function ($query) {
                    $query->select('id', 'title', 'slug', 'status');
                },
                'category:id,category_name',
            ])->where('language_id', $language_id)
                ->when('type' === 'category', function ($query) {
                    return $query->where('news.status', '=', '1');
                });
            $total = $data->clone()->count('id');
            if ($total) {
                $res = $data->clone()->orderBy('id', 'DESC')->limit($limit)->offset($offset)->get();
                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $res,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getLiveStreaming(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }
            $data = LiveStreaming::where('language_id', $request->language_id);
            $total = $data->clone()->count('id');
            if ($total) {
                $offset = $request->offset ?? 0;
                $limit = $request->limit ?? 10;
                $res = $data->clone()->skip($offset)->take($limit)->get();
                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $res,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getVideos(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $language_id = $request->language_id;

            $offset = $request->offset ?? 0;
            $limit = $request->limit ?? 10;
            $latitude = $request->latitude ?? 0;
            $longitude = $request->longitude ?? 0;

            $res = DB::table('tbl_news')
                ->selectRaw(
                    'tbl_news.*,tbl_category.category_name, tbl_location.latitude, tbl_location.longitude, SQRT(POW(111.2
                * (tbl_location.latitude - ?), 2) + POW(111.2 * (? - tbl_location.longitude) * COS(tbl_location.latitude / 57.3), 2)) AS distance',
                    [$latitude, $longitude],
                )
                ->join('tbl_category', 'tbl_news.category_id', '=', 'tbl_category.id')
                ->leftJoin('tbl_subcategory', 'tbl_news.subcategory_id', '=', 'tbl_subcategory.id')
                ->leftJoin('tbl_location', 'tbl_news.location_id', '=', 'tbl_location.id')
                ->where('tbl_news.language_id', $language_id)
                ->where(function ($q1) {
                    $q1->where('tbl_news.show_till', '>=', $this->toDate)->orWhere('tbl_news.show_till', '0000-00-00');
                })
                ->whereIn('content_type', ['video_upload', 'video_youtube', 'video_other'])
                ->where('tbl_news.status', '1');
            if (isset($latitude) && isset($longitude) && $latitude != null && $longitude != null) {
                $res->orderByRaw('CASE WHEN distance IS NULL THEN 1 ELSE 0 END, distance ASC')->where(function ($q2) use ($latitude, $longitude, $limit, $offset) {
                    $q2->having(DB::raw('distance <' . $this->nearest_location_measure . ' OR tbl_news.location_id=. 0'));
                });
            } else {
                $res->orderBy('tbl_news.id', 'DESC');
            }
            $total = $res->clone()->count('tbl_news.id');
            if ($total) {
                $data = $res->clone()->limit($limit)->offset($offset)->get();
                foreach ($data as $item) {
                    if (!empty($item->image) && strpos($item->image, 'news/') === false) {
                        $item->image = 'news/' . $item->image;
                    }
                    $item->image = url(Storage::url($item->image));
                    if ($item->content_type == 'video_upload') {
                        $item->content_value = $item->content_value ? url(Storage::url($item->content_value)) : '';
                    }
                }
                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $data,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getTag(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }
            $offset = $request->offset ?? 0;
            $limit = $request->limit ?? 10;

            $tags = Tag::where('language_id', $request->language_id);
            if ($request->slug) {
                $tags->where('slug', $request->slug);
            }
            $total = $tags->clone()->count('id');

            if ($total) {
                $offset = $request->offset ?? 0;
                $limit = $request->limit ?? 10;
                $res = $tags->clone()->skip($offset)->take($limit)->get();
                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $res,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
            ];
        }
        return response()->json($response);
    }

    public function getSubcategoryByCategory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
                'category_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $category_id = $request->category_id;
            $language_id = $request->language_id;
            $res = SubCategory::with('category:id,category_name')->where('language_id', $language_id)->where('category_id', $category_id)->orderBy('row_order', 'ASC')->get();
            if (!$res->isEmpty()) {
                $response = [
                    'error' => false,
                    'data' => $res,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
                // 'message' => trans('error_occurred'),
            ];
        }
        return response()->json($response);
    }

    public function getCategory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }
            $offset = $request->offset ?? 0;
            $limit = $request->limit ?? 10;

            $category = Category::with('sub_categories')->where('language_id', $request->language_id);
            if ($request->slug) {
                $category = $category->where('slug', $request->slug);
            }
            $res = $category->clone()->orderBy('row_order', 'ASC');
            $res = $res->skip($offset)->take($limit)->get();

            $total = Category::with('sub_categories')
                ->where('language_id', $request->language_id)
                ->count();
            if (!$res->isEmpty()) {
                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $res,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
                // 'message' => trans('error_occurred'),
            ];
        }
        return response()->json($response);
    }

    public function getLanguageJsonData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }

            $code = $request->code;
            $jsonFilePath = storage_path('app/public/language/' . $code . '.json');
            if (file_exists($jsonFilePath)) {
                $jsonData = file_get_contents($jsonFilePath);
                $jsonData = json_decode($jsonData, true);
                $response = [
                    'error' => false,
                    'data' => $jsonData,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
                // 'message' => trans('error_occurred'),
            ];
        }
        return response()->json($response);
    }

    public function getLanguagesList(Request $request)
    {
        try {
            $offset = $request->offset ?? 0;
            $limit = $request->limit ?? 10;
            $language = Language::select('id', 'language', 'code', 'status', 'isRTL', 'image', 'display_name');
            $res = $language->clone()->where('status', 1)->skip($offset)->take($limit)->get();

            if (!$res->isEmpty()) {
                $setting = Settings::where('type', 'default_language')->pluck('message')->first();
                $default_lang = $setting ?? 0;
                if ($default_lang == 0) {
                    $default_language = $language->clone()->where('code', 'en')->first();
                } else {
                    $default_language = $language->clone()->where('id', $default_lang)->first();
                }
                $response = [
                    'error' => false,
                    'default_language' => $default_language,
                    'data' => $res,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
                // 'message' => trans('error_occurred'),
            ];
        }
        return response()->json($response);
    }

    public function getLocation(Request $request)
    {
        try {
            $total = Location::count('id');
            if ($total) {
                $offset = $request->offset ?? 0;
                $limit = $request->limit ?? 10;
                $data = Location::select('id', 'location_name', 'latitude', 'longitude')->skip($offset)->take($limit)->orderBy('id', 'DESC')->get();
                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $data,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getAdSpaceNewsDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }
            $data = [];
            $res = AdSpaces::where('language_id', $request->language_id)->where('status', 1);
            $ad_space = $res->clone()->where('ad_space', 'news_details-top')->first();
            if (!empty($ad_space)) {
                $ad_space->position = 'top';
                $data['ad_spaces_top'] = $ad_space;
            }
            $ad_space1 = $res->clone()->where('ad_space', 'news_details-bottom')->first();
            if (!empty($ad_space1)) {
                $ad_space1->position = 'bottom';
                $data['ad_spaces_bottom'] = $ad_space1;
            }

            if (empty($data)) {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            } else {
                $response = [
                    'error' => false,
                    'data' => $data,
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getWebSeoPages(Request $request)
    {
        try {
            $total = WebSeoPages::count('id');
            if ($total) {
                $offset = $request->offset ?? 0;
                $limit = $request->limit ?? 10;

                $data = WebSeoPages::orderBy('id', 'DESC');
                if ($request->type) {
                    $data->where('page_type', $request->type);
                }
                $data = $data->skip($offset)->take($limit)->get();
                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $data,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getSettings(Request $request)
    {
        try {
            $types = ['system_timezone', 'category_mode', 'subcategory_mode', 'breaking_news_mode', 'live_streaming_mode', 'comments_mode', 'location_news_mode', 'nearest_location_measure', 'auto_delete_expire_news_mode', 'fcm_sever_key', 'app_version', 'ads_type', 'in_app_ads_mode', 'ios_in_app_ads_mode', 'ios_ads_type', 'fb_rewarded_video_id', 'fb_interstitial_id', 'fb_banner_id', 'fb_native_unit_id', 'ios_fb_rewarded_video_id', 'ios_fb_interstitial_id', 'ios_fb_banner_id', 'ios_fb_native_unit_id', 'google_rewarded_video_id', 'google_interstitial_id', 'google_banner_id', 'google_native_unit_id', 'ios_google_rewarded_video_id', 'ios_google_interstitial_id', 'ios_google_banner_id', 'ios_google_native_unit_id', 'unity_rewarded_video_id', 'unity_interstitial_id', 'unity_banner_id', 'android_game_id', 'ios_unity_rewarded_video_id', 'ios_unity_interstitial_id', 'ios_unity_banner_id', 'ios_game_id'];
            $res = Settings::whereIn('type', $types)->pluck('message', 'type')->toArray();
            if (!empty($res)) {
                $setting = Settings::where('type', 'default_language')->pluck('message')->first();
                $default_lang = $setting ?? 0;
                $language = Language::select('id', 'language', 'code', 'status', 'isRTL', 'image', 'display_name');
                if ($default_lang == 0) {
                    $default_language = $language->clone()->where('code', 'en')->first();
                } else {
                    $default_language = $language->clone()->where('id', $default_lang)->first();
                }
                $res['default_language'] = $default_language;

                $web_setting = WebSetting::pluck('message', 'type')->toArray();
                if (!empty($web_setting)) {
                    $web_setting['web_header_logo'] = asset('storage/' . $web_setting['web_header_logo']); // Replace 'path/to/header/logo.png' with the actual path
                    $web_setting['web_footer_logo'] = asset('storage/' . $web_setting['web_footer_logo']);
                    $web_setting['web_placeholder_image'] = isset($web_setting['web_placeholder_image']) ? asset('storage/' . $web_setting['web_placeholder_image']) : '';  // Replace 'path/to/footer/logo.png' with the actual path
                }
                $res['web_setting'] = $web_setting;

                $response = [
                    'error' => false,
                    'data' => $res,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
                // 'message' => trans('error_occurred'),
            ];
        }
        return response()->json($response);
    }

    public function getPolicyPages(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }
            // $res = Pages::select('id', 'language_id', 'title', 'page_content')->where('language_id', $request->language_id);
            $terms_policy = Pages::select('id', 'language_id', 'title', 'page_content')->where('language_id', $request->language_id)->where('page_type', 'terms-condition')->first();
            $privacy_policy = Pages::select('id', 'language_id', 'title', 'page_content')->where('language_id', $request->language_id)->where('page_type', 'privacy-policy')->first();
            if (!empty($terms_policy) || !empty($privacy_policy)) {
                $response = [
                    'error' => false,
                    'terms_policy' => $terms_policy,
                    'privacy_policy' => $privacy_policy,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getPages(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }
            $data = Pages::where('language_id', $request->language_id)->where('status', 1);
            if($request->has('slug')){
                $data->where('slug', $request->slug);
            }
            $total = $data->clone()->count('id');
            if ($total) {
                $offset = $request->offset ?? 0;
                $limit = $request->limit ?? 10;

                $res = $data->clone()->skip($offset)->take($limit)->get();
                $response = [
                    'error' => false,
                    'total' => $total,
                    'data' => $res,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => trans('error_occurred'),
            ];
        }
        return response()->json($response);
    }

    public function checkSlugAvailability(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'slug' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            $slug = News::where('slug', $request->slug)
                ->where('id', '!=', $request->news_id)
                ->first();
            if (!empty($slug)) {
                $response = [
                    'error' => true,
                    'message' => 'The slug is already in use. Please choose another.',
                ];
            } else {
                $response = [
                    'error' => false,
                    'message' => 'This slug can be used.',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function registerToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ]);
            }
            if ($request->token && $request->language_id) {
                $token = $request->token;
                $latitude = $request->latitude ?? 0;
                $longitude = $request->longitude ?? 0;
                $language_id = $request->language_id;

                $user = User::find(Auth::user()->id);
                $user->fcm_id = $token;
                $user->save();
                $data = Token::where('token', $token)->first();
                if (empty($data)) {
                    $data = new Token();
                    $data->token = $token;
                    $data->language_id = $language_id;
                    $data->latitude = $latitude;
                    $data->longitude = $longitude;
                    $data->save();
                    $response = [
                        'error' => false,
                        'message' => 'Device registered successfully',
                    ];
                } else {
                    $edit_id = $data->id;
                    $data = Token::find($edit_id);
                    $data->token = $token;
                    $data->language_id = $language_id;
                    $data->latitude = $latitude;
                    $data->longitude = $longitude;
                    $data->save();
                    $response = [
                        'error' => false,
                        'message' => 'Device already registered & Location Updated',
                    ];
                }
            } else {
                $response = [
                    'error' => true,
                    'message' => 'Please fill all the data and submit!',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function deleteUser()
    {
        try {
            $user_id = Auth::user()->id;
            Bookmark::where('user_id', $user_id)->delete();
            BreakingNewsView::where('user_id', $user_id)->delete();
            Comments::where('user_id', $user_id)->delete();
            CommentsFlag::where('user_id', $user_id)->delete();
            CommentsLike::where('user_id', $user_id)->delete();
            CommentNotification::where('user_id', $user_id)->delete();
            News_like::where('user_id', $user_id)->delete();
            News_view::where('user_id', $user_id)->delete();
            SurveyResult::where('user_id', $user_id)->delete();
            UserCategory::where('user_id', $user_id)->delete();
            User::where('id', $user_id)->delete();
            $response = [
                'error' => false,
                'message' => 'user deleted successfully',
            ];
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function updateProfile(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $user = User::find($user_id);
            if (!empty($user)) {
                if ($request->name) {
                    $user->name = $request->name;
                }
                if ($request->mobile) {
                    $user->mobile = $request->mobile;
                }
                if ($request->email) {
                    $user->email = $request->email;
                }
                if ($request->hasFile('profile')) {
                    Storage::disk('public')->delete($user->getRawOriginal('profile'));
                    $user->profile = $request->file('profile')->store('profile', 'public');
                }
                $user->save();
                $res = User::where('id', $user_id)->first();
                $response = [
                    'error' => false,
                    'message' => 'Profile updated successfully',
                    'data' => $res,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'User not found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function getUserById()
    {
        try {
            $user_id = Auth::user()->id;
            $res = User::with('user_category')->where('id', $user_id)->first();
            if ($res) {
                $response = [
                    'error' => false,
                    'data' => $res,
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No Data Found',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }

    public function userSignup(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firebase_id' => 'required',
            ]);
            if ($validator->fails()) {
                $response = [
                    'error' => true,
                    'message' => $validator->errors()->first(),
                ];
                return response()->json($response);
            }
            $firebase_id = $request->firebase_id;
            $type = $request->type;
            $user = User::where('firebase_id', $firebase_id)->first();

            if (!$user) {
                // Create a new user if not found
                $user = new User();
                $user->firebase_id = $firebase_id;
                $user->name = $request->name ?? '';
                $user->type = $type;
                $user->email = $request->email ?? '';
                $user->mobile = $request->mobile ?? '';
                $user->profile = $request->profile ?? '';
                $user->fcm_id = $request->fcm_id ?? '';
                $user->status = $request->status ?? '1';
                $user->date = now();
                $user->role = '0';
                $user->save();
                $user->is_login = '0'; // Dynamically add 'is_login' column and set its value
                $token = $user->createToken('MyApp')->plainTextToken;
                $user['token'] = $token;

                $response = [
                    'error' => false,
                    'data' => $user,
                    'message' => 'User Registered successfully',
                ];
            } elseif ($user->status == 1) {
                // Update user's FCM ID if provided
                if ($request->fcm_id) {
                    $user->fcm_id = $request->fcm_id;
                    $user->save();
                    $user->is_login = '1'; // Dynamically add 'is_login' column and set its value
                }
                $token = $user->createToken('MyApp')->plainTextToken;
                $user['token'] = $token;
                $response = [
                    'error' => false,
                    'data' => $user,
                    'message' => 'Successfully logged in',
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'User is deactivated.',
                ];
            }
        } catch (Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }
}
