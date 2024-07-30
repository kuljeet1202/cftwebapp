<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['auth.optional']], function () {
    Route::get('get_news', [ApiController::class, 'getNews']);
    Route::get('get_comment_by_news', [ApiController::class, 'getCommentByNews']);
    Route::get('get_breaking_news', [ApiController::class, 'getBreakingNews']);
    Route::get('get_featured_sections', [ApiController::class, 'getFeaturedSections']);
    Route::get('get_featured_section_by_id', [ApiController::class, 'getFeaturedSectionById']);
});

Route::get('get_notification', [ApiController::class, 'getNotification']);
Route::get('get_live_streaming', [ApiController::class, 'getLiveStreaming']);
Route::get('get_videos', [ApiController::class, 'getVideos']);
Route::get('get_tag', [ApiController::class, 'getTag']);
Route::get('get_subcategory_by_category', [ApiController::class, 'getSubcategoryByCategory']);
Route::get('get_category', [ApiController::class, 'getCategory']);
Route::get('get_language_json_data', [ApiController::class, 'getLanguageJsonData']);
Route::get('get_languages_list', [ApiController::class, 'getLanguagesList']);
Route::get('get_location', [ApiController::class, 'getLocation']);
Route::get('get_ad_space_news_details', [ApiController::class, 'getAdSpaceNewsDetails']);
Route::get('get_web_seo_pages', [ApiController::class, 'getWebSeoPages']);
Route::get('get_settings', [ApiController::class, 'getSettings']);
Route::get('get_policy_pages', [ApiController::class, 'getPolicyPages']);
Route::get('get_pages', [ApiController::class, 'getPages']);
Route::get('check_slug_availability', [ApiController::class, 'checkSlugAvailability']);
Route::post('user_signup', [ApiController::class, 'userSignup']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('set_user_category', [ApiController::class, 'setUserCategory']);
    Route::get('get_question_result', [ApiController::class, 'getQuestionResult']);
    Route::post('set_question_result', [ApiController::class, 'setQuestionResult']);
    Route::get('get_question', [ApiController::class, 'getQuestion']);
    Route::post('delete_news_images', [ApiController::class, 'deleteNewsImages']);
    Route::post('delete_news', [ApiController::class, 'deleteNews']);
    Route::post('set_news', [ApiController::class, 'setNews']);
    Route::post('delete_user_notification', [ApiController::class, 'deleteUserNotification']);
    Route::get('get_user_notification', [ApiController::class, 'getUserNotification']); // pagintion pending
    Route::post('set_news_view', [ApiController::class, 'setNewsView']);
    Route::post('set_breaking_news_view', [ApiController::class, 'setBreakingNewsView']);
    Route::post('set_comment_like_dislike', [ApiController::class, 'setCommentLikeDislike']); // change in user detail object
    Route::post('delete_comment', [ApiController::class, 'deleteComment']);
    Route::post('set_flag', [ApiController::class, 'setFlag']);
    Route::post('set_comment', [ApiController::class, 'setComment']);
    Route::get('get_bookmark', [ApiController::class, 'getBookmark']);
    Route::post('set_bookmark', [ApiController::class, 'setBookmark']);
    Route::get('get_like', [ApiController::class, 'getLike']); //for app
    Route::post('set_like_dislike', [ApiController::class, 'setLikeDislike']);

    Route::post('register_token', [ApiController::class, 'registerToken']);
    Route::post('delete_user', [ApiController::class, 'deleteUser']);
    Route::post('update_profile', [ApiController::class, 'updateProfile']);
    Route::get('get_user_by_id', [ApiController::class, 'getUserById']);
});
