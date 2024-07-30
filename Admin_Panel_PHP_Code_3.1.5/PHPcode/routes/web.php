<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\AdSpacesController;
use App\Http\Controllers\AppUserController;
use App\Http\Controllers\AppUserRolesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BreakingNewsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\FeaturedSectionsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LiveStreamingController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\SendNotificationController;
use App\Http\Controllers\SEOController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UpdaterController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('clear', function () {
    Artisan::call('optimize:clear');
    return redirect()->back();
});

Route::get('storage-link', function () {
    Artisan::call('storage:link');
    return redirect()->back();
});

// Route::get('migrate', function () {
//     Artisan::call('migrate');
//     return redirect()->back();
// });

Route::group(['prefix' => 'install'], static function () {
    Route::controller(InstallerController::class)->group(function () {
        Route::get('purchase-code', 'purchaseCodeIndex')->name('install.purchase-code.index');
        Route::post('purchase-code', 'checkPurchaseCode')->name('install.purchase-code.post');
    });
});

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => 'checkNotLoggedIn'], function () {
    // Routes that should be accessible only if the user is not logged in
    Route::controller(AuthController::class)->group(function () {
        Route::get('login', 'login')->name('login');
        Route::post('authenticate', 'authenticate')->name('authenticate');
        // Route::get('/home', 'dashboard')->name('home');
        Route::post('check_email', 'check_email')->name('check_email');
        Route::get('reset_password', 'reset_password')->name('reset_password');
        Route::post('update_password', 'update_password')->name('update_password');
    });
});

Route::group(['middleware' => ['auth:admin', 'checkLogin']], function () {
    Route::group(['middleware' => 'language'], function () {
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('home', [HomeController::class, 'index'])->name('home');

        // Languages
        Route::resource('language', LanguageController::class, ['except' => ['create', 'edit']]);
        Route::get('language_list', [LanguageController::class, 'show'])->name('languageList');
        Route::post('store_default_language', [LanguageController::class, 'store_default_language']);
        Route::get('sample-download', [LanguageController::class, 'download'])->name('sample-download');
        Route::get('downloadPanelFile', [LanguageController::class, 'downloadPanelFile'])->name('downloadPanelFile');
        Route::get('set-language/{lang}', [LanguageController::class, 'set_language']);

        //category
        Route::resource('category', CategoryController::class, ['except' => ['create', 'edit']]);
        Route::get('category_list', [CategoryController::class, 'show'])->name('categoryList');
        Route::post('update_category_order', [CategoryController::class, 'update_order'])->name('update_category_order');
        Route::post('get_category_by_language', [CategoryController::class, 'get_category_by_language'])->name('get_category_by_language');

        // sub_category
        Route::resource('sub_category', SubCategoryController::class, ['except' => ['create', 'edit']]);
        Route::get('sub_category_list', [SubCategoryController::class, 'show'])->name('subcategoryList');
        Route::post('get_subcategory_by_category', [SubCategoryController::class, 'get_subcategory_by_category'])->name('get_subcategory_by_category');
        Route::post('update_subcategory_order', [SubCategoryController::class, 'update_order'])->name('update_subcategory_order');

        // Tag
        Route::resource('tag', TagController::class, ['except' => ['create', 'edit']]);
        Route::get('tag_list', [TagController::class, 'show'])->name('tagList');
        Route::post('get_tag_by_language', [TagController::class, 'get_tag_by_language'])->name('get_tag_by_language');

        //  Live Streaming
        Route::resource('live_streaming', LiveStreamingController::class, ['except' => ['create', 'edit']]);
        Route::get('live_streaming_list', [LiveStreamingController::class, 'show'])->name('liveStreamingList');

        // Location
        Route::resource('location', LocationController::class, ['except' => ['create', 'edit']]);
        Route::get('location_list', [LocationController::class, 'show'])->name('locationList');

        // News
        Route::resource('news', NewsController::class, ['except' => ['create', 'edit']]);
        Route::post('upload_img', [NewsController::class, 'upload_img'])->name('upload_img');
        Route::get('news_list', [NewsController::class, 'show'])->name('newsList');
        Route::put('news_update_description', [NewsController::class, 'update_description'])->name('news_update_description');
        Route::post('clone_news', [NewsController::class, 'clone_news'])->name('clone_news');
        Route::get('news-image/{id}', [NewsController::class, 'newsImage'])->name('newsImage');
        Route::get('news-image-list', [NewsController::class, 'showImage'])->name('news-image-list');
        Route::post('store-image', [NewsController::class, 'storeImage'])->name('store-image');
        Route::delete('deleteImage/{id}', [NewsController::class, 'deleteImage'])->name('deleteImage');
        Route::post('get_news_by_category', [NewsController::class, 'get_news_by_category'])->name('get_news_by_category');
        Route::post('get_news_by_subcategory', [NewsController::class, 'get_news_by_subcategory'])->name('get_news_by_subcategory');

        // FeaturedSections
        Route::resource('featured_sections', FeaturedSectionsController::class, ['except' => ['create', 'edit']]);
        Route::get('featured_sections_list', [FeaturedSectionsController::class, 'show'])->name('featuredSectionList');
        Route::post('get_categories_tree', [FeaturedSectionsController::class, 'get_categories_tree'])->name('get_categories_tree');
        Route::post('get_custom_news', [FeaturedSectionsController::class, 'getCustomNews'])->name('get_custom_news');
        Route::post('update_featured_sections_order', [FeaturedSectionsController::class, 'update_order'])->name('update_featured_sections_order');
        Route::post('get_feature_section_by_language', [FeaturedSectionsController::class, 'get_feature_section_by_language'])->name('get_feature_section_by_language');

        // Breaking_newss
        Route::resource('breaking_news', BreakingNewsController::class, ['except' => ['create', 'edit']]);
        Route::get('breaking_news_list', [BreakingNewsController::class, 'show'])->name('breakingNewsList');

        // Page
        Route::resource('pages', PagesController::class, ['except' => ['create', 'edit']]);
        Route::get('pages_list', [PagesController::class, 'show'])->name('pagesList');

        // AdSpaces
        Route::resource('ad_spaces', AdSpacesController::class, ['except' => ['create', 'edit']]);
        Route::get('ad_spaces_list', [AdSpacesController::class, 'show'])->name('adSpacesList');
        Route::post('get_featured_sections_by_language', [AdSpacesController::class, 'getFeaturedSectionsByLanguage'])->name('get_featured_sections_by_language');

        // User List
        Route::resource('app_users', AppUserController::class, ['only' => ['index', 'show', 'update']]);
        Route::get('app_users_list', [AppUserController::class, 'show'])->name('usersList');

        // User Role
        Route::resource('app_users_roles', AppUserRolesController::class);
        Route::get('app_users_roles_list', [AppUserRolesController::class, 'show'])->name('userRoleList');

        // Comments
        Route::resource('comments', CommentsController::class, ['only' => ['index', 'show', 'destroy']]);
        Route::get('comments_list', [CommentsController::class, 'show'])->name('commentsList');
        Route::get('comments_flag_list', [CommentsController::class, 'comment_flag'])->name('commentsFlagsList');
        Route::delete('comments-delete/{id}', [CommentsController::class, 'comment_delete'])->name('comments-delete');
        // Comments_flag
        Route::get('comments_flag', [CommentsController::class, 'index1'])->name('comments_flag');

        // Notifications
        Route::resource('notifications', SendNotificationController::class, ['only' => ['index', 'store', 'show', 'destroy']]);
        Route::get('notifications_list', [SendNotificationController::class, 'show'])->name('notificationList');

        // survey
        Route::resource('survey', SurveyController::class, ['except' => ['create', 'edit']]);
        Route::get('survey_question_list', [SurveyController::class, 'show'])->name('surveyQuestionList');
        // survey_option
        Route::get('survey_options/{id}', [SurveyController::class, 'get_survey_option']);
        Route::get('survey_options_list', [SurveyController::class, 'survey_options_show'])->name('surveyOptionsList');
        Route::post('survey_options_store', [SurveyController::class, 'store_option'])->name('survey-options-store');
        Route::put('survey_options_edit', [SurveyController::class, 'update_option'])->name('survey-options-edit');
        Route::delete('survey_options_delete/{id}', [SurveyController::class, 'delete_option'])->name('survey-options-delete');

        // settings
        Route::get('system-settings', [SettingsController::class, 'indexSetting'])->name('system-settings');
        Route::get('general-settings', [SettingsController::class, 'indexGeneralSetting'])->name('general-settings');
        Route::post('general-settings', [SettingsController::class, 'storeGeneralSetting'])->name('general-settings.store');
        Route::get('web-settings', [SettingsController::class, 'indexWebSetting'])->name('web-settings');
        Route::post('web-settings', [SettingsController::class, 'storeWebSetting'])->name('web-settings.store');
        Route::get('app-settings', [SettingsController::class, 'indexAppSetting'])->name('app-settings');
        Route::post('app-settings', [SettingsController::class, 'storeAppSetting'])->name('app-settings.store');

        Route::resource('seo-setting', SEOController::class, ['except' => ['create', 'edit']]);
        Route::get('seo-setting-list', [SEOController::class, 'show'])->name('seoSettingList');

        // Route::get('system_configurations_main', [SettingsController::class, 'system_configurations'])->name('system_configurations');

        Route::get('edit_profile', [HomeController::class, 'editProfile'])->name('edit-profile');
        Route::post('checkOldPass', [HomeController::class, 'checkOldPass'])->name('checkOldPass');
        Route::post('update-profile', [HomeController::class, 'update_profile'])->name('update-profile');
        Route::get('database-backup', [HomeController::class, 'database_backup'])->name('database-backup');

        Route::get('system_update', [UpdaterController::class, 'index'])->name('system-update');
        Route::post('system_update_operation', [UpdaterController::class, 'system_update'])->name('system-update-operation');

        //for bulk delete
        Route::post('bulk_comment_delete', [CommentsController::class, 'bulk_comment_delete'])->name('bulk_comment_delete');
        Route::post('bulk_survey_delete', [SurveyController::class, 'bulk_survey_delete'])->name('bulk_survey_delete');
        Route::post('bulk_news_delete', [NewsController::class, 'bulk_news_delete'])->name('bulk_news_delete');
        Route::post('bulk_brecking_news_delete', [BreakingNewsController::class, 'bulk_brecking_news_delete'])->name('bulk_brecking_news_delete');
    });
});
