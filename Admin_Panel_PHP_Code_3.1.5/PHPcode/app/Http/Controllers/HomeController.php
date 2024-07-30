<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdSpaces;
use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\Comments;
use App\Models\FeaturedSections;
use App\Models\Language;
use App\Models\News;
use App\Models\Pages;
use App\Models\Role;
use App\Models\Settings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $setting = Settings::where('type', 'app_version')->where('message', '3.1.3')->first();
        if ($setting) {
            $setting->message = '3.1.4';
            $setting->save();
        }

        $count_news_per_category = DB::table('tbl_news')->select('tbl_news.category_id', 'tbl_category.category_name', DB::raw('COUNT(tbl_news.id) as news_count'))->leftJoin('tbl_category', 'tbl_category.id', '=', 'tbl_news.category_id')->where('tbl_news.status', 1)->groupBy('tbl_news.category_id', 'tbl_category.category_name')->get();
        $news_per_category = [];
        foreach ($count_news_per_category as $row) {
            $news_per_category[] = [
                'category' => $row->category_name,
                'news' => floatval($row->news_count),
            ];
        }

        $count_news_per_language = DB::table('tbl_news as n')->select('l.language', DB::raw('COUNT(n.id) as news_count'))->join('tbl_languages as l', 'l.id', '=', 'n.language_id')->where('n.status', 1)->groupBy('n.language_id', 'l.language')->get();
        $news_per_language = $count_news_per_language->map(function ($row) {
            return [
                'language' => $row->language,
                'news' => floatval($row->news_count),
            ];
        });

        $count_surveys_per_language = DB::table('tbl_survey_question as s')->select('l.language', DB::raw('COUNT(s.id) as surveys_count'))->join('tbl_languages as l', 'l.id', '=', 's.language_id')->where('s.status', 1)->groupBy('s.language_id', 'l.language')->get();
        $surveys_per_language = $count_surveys_per_language->map(function ($row) {
            return [
                'language' => $row->language,
                'surveys' => floatval($row->surveys_count),
            ];
        });
        $countBreakingNews = BreakingNews::count('id');
        $countFeatredSection = FeaturedSections::where('status', 1)->count('id');
        $countCategory = Category::count('id');
        $countNews = News::count('id');
        $countUsers = User::count('id');
        $countUserRole = Role::count('id');
        $countPages = Pages::where('status', 1)->count('id');
        $countAdSpace = AdSpaces::where('status', 1)->count('id');
        $news_view = DB::table('tbl_news')
            ->select('tbl_news.*', 'tbl_category.category_name', 'tbl_subcategory.subcategory_name', 'tbl_news_view.viewcount')
            ->leftJoin('tbl_category', 'tbl_category.id', '=', 'tbl_news.category_id')
            ->leftJoin('tbl_subcategory', 'tbl_subcategory.id', '=', 'tbl_news.subcategory_id')
            ->leftJoin('tbl_location', 'tbl_location.id', '=', 'tbl_news.location_id')
            ->join(DB::raw('(SELECT news_id, COUNT(*) AS viewcount FROM tbl_news_view GROUP BY news_id) AS tbl_news_view'), function ($join) {
                $join->on('tbl_news.id', '=', 'tbl_news_view.news_id');
            })
            ->where('tbl_news.status', 1)
            ->orderBy('tbl_news_view.viewcount', 'DESC')
            ->limit(3)
            ->offset(0)
            ->get();
        // dd($news_view);
        foreach ($news_view as $row) {
            if (!empty($row->image) && strpos($row->image, 'news/') === false) {
                $row->image = 'news/' . $row->image;
            }
            $row->image = Storage::disk('public')->exists($row->image) ? url(Storage::url($row->image)) : '';
            // dd($row->image);
        }
        $recent_categories = Category::orderBy('id', 'DESC')->limit(8)->get();
        $recent_comments = Comments::with('user')->orderBy('id', 'DESC')->limit(6)->get();
        foreach ($recent_comments as $row) {
            $timestamp = Carbon::parse($row->date);
            $row->date = $timestamp->diffForHumans();
        }

        $enbled_language = Language::where('status', 1)->count();
        return view('dashboard', [
            'news_per_category' => $news_per_category,
            'news_per_language' => $news_per_language,
            'surveys_per_language' => $surveys_per_language,
            'countBreakingNews' => $countBreakingNews,
            'countFeatredSection' => $countFeatredSection,
            'countCategory' => $countCategory,
            'countNews' => $countNews,
            'countUsers' => $countUsers,
            'countUserRole' => $countUserRole,
            'countPages' => $countPages,
            'countAdSpace' => $countAdSpace,
            'news_view' => $news_view,
            'recent_categories' => $recent_categories,
            'recent_comments' => $recent_comments,
            'enbled_language' => $enbled_language,
        ]);
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('edit-profile', compact('user'));
    }
    public function checkOldPass(Request $request)
    {
        $id = Auth::user()->id;
        $password = $request->oldpass;
        $data = Admin::find($id);
        if ($data) {
            if (Hash::check($password, $data->password)) {
                return response()->json(true);
            } else {
                return response()->json(false);
            }
        } else {
            return response()->json(false);
        }
    }
    public function update_profile(Request $request)
    {
        $username = $request->username;
        $email = $request->email;
        $new_password = $request->newpassword;
        $confirm_password = $request->confirmpassword;
        $id = Auth::user()->id;
        if (!empty($new_password) && !empty($confirm_password)) {
            if ($new_password == $confirm_password) {
                $admin = Admin::find($id);
                $admin->username = $username;
                $admin->email = $email;
                $admin->password = $confirm_password;
                if ($request->hasFile('file')) {
                    $admin->image = $request->file('file')->store('admin', 'public');
                }
                $admin->save();
                $response = [
                    'error' => false,
                    'message' => trans('Password Change Successfully..'),
                ];
                return response()->json($response);
            } else {
                $response = [
                    'error' => true,
                    'message' => trans('New and Confirm Password not Match..'),
                ];
                return response()->json($response);
            }
        } else {
            $admin = Admin::find($id);
            $admin->username = $username;
            $admin->email = $email;
            if ($request->hasFile('file')) {
                $admin->image = $request->file('file')->store('admin', 'public');
            }
            $admin->save();
            $response = [
                'error' => false,
                'message' => __('updated_success'),
            ];
            return response()->json($response);
        }
    }

    public function database_backup()
    {
        Artisan::call('backup:run', ['--only-db' => true]);
        //    $output = Artisan::output();
        // Artisan::call('backup:run');
        // $path = storage_path('app/Laravel/*');

        $app_name = env('APP_NAME');
        $path = storage_path('app/' . $app_name . '/*');
        $latest_ctime = 0;
        $latest_filename = '';
        $files = glob($path);
        foreach ($files as $file) {
            if (is_file($file) && filectime($file) > $latest_ctime) {
                $latest_ctime = filectime($file);
                $latest_filename = $file;
            }
        }
        return response()->download($latest_filename);
    }
}
