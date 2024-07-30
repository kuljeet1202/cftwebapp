<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\WebSetting;
use dacoto\EnvSet\Facades\EnvSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function indexSetting()
    {
        return view('system-setting');
    }

    public function index()
    {
        $settings = getSetting();
        return view('system-setting', ['setting' => $settings]);
    }

    public function indexGeneralSetting()
    {
        $setting = getSetting();
        return view('general-setting', compact('setting'));
    }

    public function storeGeneralSetting(Request $request)
    {
        $settings = ['system_timezone', 'app_name', 'auto_delete_expire_news_mode', 'smtp_host', 'smtp_user', 'smtp_password', 'smtp_port', 'smtp_crypto', 'from_name', 'primary_color', 'secondary_color', 'fcm_sever_key'];

        if ($request->hasFile('file1')) {
            $image_full = $request->file('file1');
            if ($image_full->isValid()) {
                $previousFileName = Settings::where('type', 'app_logo_full')->value('message');
                if ($previousFileName) {
                    $setting = Settings::where('type', 'app_logo_full')->first();
                    Storage::disk('public')->delete($setting->getRawOriginal('message'));
                }
                $setting = Settings::where('type', 'app_logo_full')->first();
                if ($setting) {
                    $setting->message = $request->file('file1')->store('logos', 'public');
                    $setting->save();
                }
            }
        }
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            if ($image->isValid()) {
                $previousFileName = Settings::where('type', 'app_logo')->value('message');
                if ($previousFileName) {
                    $setting = Settings::where('type', 'app_logo')->first();
                    Storage::disk('public')->delete($setting->getRawOriginal('message'));
                }
                $setting = Settings::where('type', 'app_logo')->first();
                if ($setting) {
                    $setting->message = $request->file('file')->store('logos', 'public');
                    $setting->save();
                }
            }
        }
        foreach ($settings as $type) {
            $message = $request->input($type);
            if ($type == 'smtp_host') {
                EnvSet::setKey('MAIL_HOST', $message);
                EnvSet::save();
            }
            if ($type == 'smtp_port') {
                EnvSet::setKey('MAIL_PORT', $message);
                EnvSet::save();
            }
            if ($type == 'smtp_user') {
                EnvSet::setKey('MAIL_USERNAME', $message);
                EnvSet::save();
            }
            if ($type == 'smtp_password') {
                EnvSet::setKey('MAIL_PASSWORD', $message);
                EnvSet::save();
            }
            if ($type == 'smtp_crypto') {
                EnvSet::setKey('MAIL_ENCRYPTION', $message);
                EnvSet::save();
            }

            if ($type == 'system_timezone') {
                $key = 'APP_TIMEZONE';
                // Check if the key already exists in the .env file
                if (!EnvSet::keyExists($key)) {
                    // If key does not exist, append it to the end of the .env file
                    $envFilePath = base_path('.env');
                    File::append($envFilePath, "{$key}={$message}\n");
                } else {
                    // If key exists, update its value
                    EnvSet::setKey($key, $message);
                    EnvSet::save();
                }
            }
            $setting = Settings::where('type', $type)->first();
            if ($setting) {
                $setting->message = $message;
                $setting->save();
            } else {
                $setting = new Settings();
                $setting->type = $type;
                $setting->message = $message;
                $setting->save();
            }
        }
        return redirect('general-settings')->with('success', __('updated_success'));
    }

    public function indexWebSetting()
    {
        $setting = getWebSetting();
        return view('web-setting', compact('setting'));
    }

    public function storeWebSetting(Request $request)
    {
        $settings = ['web_name', 'web_color_code', 'web_footer_description'];

        if ($request->hasFile('web_header_logo')) {
            $web_header_logo = $request->file('web_header_logo');
            if ($web_header_logo->isValid()) {
                $previousFileName = WebSetting::where('type', 'web_header_logo')->value('message');
                if ($previousFileName) {
                    $setting = WebSetting::where('type', 'web_header_logo')->first();
                    Storage::disk('public')->delete($setting->getRawOriginal('message'));
                }
                $setting = WebSetting::where('type', 'web_header_logo')->first();
                if ($setting) {
                    $setting->message = $request->file('web_header_logo')->store('logos', 'public');
                    $setting->save();
                }
            }
        }

        if ($request->hasFile('web_footer_logo')) {
            $web_footer_logo = $request->file('web_footer_logo');
            if ($web_footer_logo->isValid()) {
                $previousFileName = WebSetting::where('type', 'web_footer_logo')->value('message');
                if ($previousFileName) {
                    $setting = WebSetting::where('type', 'web_footer_logo')->first();
                    Storage::disk('public')->delete($setting->getRawOriginal('message'));
                }
                $setting = WebSetting::where('type', 'web_footer_logo')->first();
                if ($setting) {
                    $setting->message = $request->file('web_footer_logo')->store('logos', 'public');
                    $setting->save();
                }
            }
        }

        if ($request->hasFile('web_placeholder_image')) {
            $web_placeholder_image = $request->file('web_placeholder_image');
            if ($web_placeholder_image->isValid()) {
                $previousFileName = WebSetting::where('type', 'web_placeholder_image')->value('message');
                if ($previousFileName) {
                    $setting = WebSetting::where('type', 'web_placeholder_image')->first();
                    Storage::disk('public')->delete($setting->getRawOriginal('message'));
                }
                $setting = WebSetting::where('type', 'web_placeholder_image')->first();
                if ($setting) {
                    $setting->message = $request->file('web_placeholder_image')->store('logos', 'public');
                    $setting->save();
                } else {
                    $setting = new WebSetting();
                    $setting->type = 'web_placeholder_image';
                    $setting->message = $request->file('web_placeholder_image')->store('logos', 'public');
                    $setting->save();
                }
            }
        }
        foreach ($settings as $type) {
            $message = $request->input($type);
            $setting = WebSetting::firstOrNew(['type' => $type]);
            $setting->message = $message;
            $setting->save();
        }
        return redirect('web-settings')->with('success', __('updated_success'));
    }

    public function indexAppSetting()
    {
        $setting = getSetting();
        return view('app-setting', compact('setting'));
    }

    public function storeAppSetting(Request $request)
    {
        $settings = ['category_mode', 'subcategory_mode', 'breaking_news_mode', 'comments_mode', 'live_streaming_mode', 'ads_type', 'in_app_ads_mode', 'google_rewarded_video_id', 'google_interstitial_id', 'google_banner_id', 'google_native_unit_id', 'fb_rewarded_video_id', 'fb_interstitial_id', 'fb_banner_id', 'fb_native_unit_id', 'ios_ads_type', 'ios_in_app_ads_mode', 'ios_google_rewarded_video_id', 'ios_google_interstitial_id', 'ios_google_banner_id', 'ios_google_native_unit_id', 'ios_fb_rewarded_video_id', 'ios_fb_interstitial_id', 'ios_fb_banner_id', 'ios_fb_native_unit_id', 'unity_rewarded_video_id', 'unity_interstitial_id', 'unity_banner_id', 'android_game_id', 'ios_unity_rewarded_video_id', 'ios_unity_interstitial_id', 'ios_unity_banner_id', 'ios_game_id', 'location_news_mode', 'nearest_location_measure'];
        foreach ($settings as $type) {
            $message = $request->input($type);
            $setting = Settings::where('type', $type)->first();
            if ($setting) {
                $setting->message = $message;
                $setting->save();
            } else {
                $setting = new Settings();
                $setting->type = $type;
                $setting->message = $message;
                $setting->save();
            }
        }
        return redirect('app-settings')->with('success', __('updated_success'));
    }
}
