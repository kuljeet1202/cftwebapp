<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Pages;
use App\Models\Settings;
use App\Models\WebSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstallationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admin')->insert([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'email' => 'admin@gmail.com',
            'forgot_unique_code' => '',
            'forgot_at' => '',
            'image' => 'admin/profile.jpg',
            'updated_at' => now(),
            'created_at' => now(),
        ]);

        $settingsData = [
            ['type' => 'system_timezone', 'message' => 'Asia/Kolkata'],
            ['type' => 'app_name', 'message' => 'News'],
            ['type' => 'primary_color', 'message' => '#000000'],
            ['type' => 'secondary_color', 'message' => '#ff0000'],
            ['type' => 'auto_delete_expire_news_mode', 'message' => '0'],
            ['type' => 'fcm_sever_key', 'message' => 'YOUR_SERVER_KEY'],
            ['type' => 'app_logo_full', 'message' => 'logo.png'],
            ['type' => 'app_logo', 'message' => 'favicon.png'],
            ['type' => 'smtp_host', 'message' => 'smtp.googlemail.com'],
            ['type' => 'smtp_user', 'message' => 'SMTP User'],
            ['type' => 'smtp_password', 'message' => 'SMTP Password'],
            ['type' => 'smtp_port', 'message' => '465'],
            ['type' => 'smtp_crypto', 'message' => 'tls'],
            ['type' => 'from_name', 'message' => 'News'],
            ['type' => 'category_mode', 'message' => '1'],
            ['type' => 'subcategory_mode', 'message' => '1'],
            ['type' => 'breaking_news_mode', 'message' => '1'],
            ['type' => 'live_streaming_mode', 'message' => '1'],
            ['type' => 'comments_mode', 'message' => '1'],
            ['type' => 'location_news_mode', 'message' => '0'],
            ['type' => 'nearest_location_measure', 'message' => '1000'],
            ['type' => 'in_app_ads_mode', 'message' => '0'],
            ['type' => 'ads_type', 'message' => '1'],
            ['type' => 'google_rewarded_video_id', 'message' => 'google Rewarded Video Id'],
            ['type' => 'google_interstitial_id', 'message' => 'google Interstitial Id'],
            ['type' => 'google_banner_id', 'message' => 'google Banner Id'],
            ['type' => 'google_native_unit_id', 'message' => 'google Native Unit Id'],
            ['type' => 'fb_rewarded_video_id', 'message' => 'fb Native Unit Id'],
            ['type' => 'fb_interstitial_id', 'message' => 'fb Interstitial Id'],
            ['type' => 'fb_banner_id', 'message' => 'fb Banner Id'],
            ['type' => 'fb_native_unit_id', 'message' => 'fb Native Unit Id'],
            ['type' => 'unity_rewarded_video_id', 'message' => '1'],
            ['type' => 'unity_interstitial_id', 'message' => '1'],
            ['type' => 'unity_banner_id', 'message' => '1'],
            ['type' => 'android_game_id', 'message' => '1'],
            ['type' => 'ios_in_app_ads_mode', 'message' => '0'],
            ['type' => 'ios_ads_type', 'message' => '1'],
            ['type' => 'ios_fb_rewarded_video_id', 'message' => 'fb IOS Rewarded Video Id'],
            ['type' => 'ios_fb_interstitial_id', 'message' => 'fb IOS Interstitial Id'],
            ['type' => 'ios_fb_banner_id', 'message' => 'fb IOS Banner Id'],
            ['type' => 'ios_fb_native_unit_id', 'message' => 'fb IOS Native Unit Id'],
            ['type' => 'ios_google_rewarded_video_id', 'message' => 'google Rewarded Video Id'],
            ['type' => 'ios_google_interstitial_id', 'message' => 'google Interstitial Id'],
            ['type' => 'ios_google_banner_id', 'message' => 'google Banner Id'],
            ['type' => 'ios_google_native_unit_id', 'message' => 'google Native Unit Id'],
            ['type' => 'ios_unity_rewarded_video_id', 'message' => '1'],
            ['type' => 'ios_unity_interstitial_id', 'message' => '1'],
            ['type' => 'ios_unity_banner_id', 'message' => '1'],
            ['type' => 'ios_game_id', 'message' => '1'],
            ['type' => 'app_version', 'message' => '3.1.5'],
            ['type' => 'default_language', 'message' => '1'],
        ];

        if (Settings::count() === 0) {
            Settings::insert($settingsData);
        }

        $languagesData = [
            [
                'id' => 1,
                'language' => 'English (US)',
                'display_name' => 'English (US)',
                'code' => 'en',
                'status' => 1,
                'isRTL' => 0,
                'image' => 'flags/en.webp',
                'updated_at' => now(),
                'created_at' => now(),
            ],
        ];

        if (Language::count() === 0) {
            Language::insert($languagesData);
        }

        $pagesData = [
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'meta_description' => 'Contact Us',
                'meta_keywords' => 'Contact',
                'is_custom' => 0,
                'page_content' => '<p style="text-align: center;"><strong>How can we help you?</strong></p>',
                'page_type' => 'contact-us',
                'language_id' => 1,
                'page_icon' => '',
                'is_termspolicy' => 0,
                'is_privacypolicy' => 0,
                'status' => 1,
                'schema_markup' => '',
                'meta_title' => '',
                'og_image' => '',
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'meta_description' => 'About Us',
                'meta_keywords' => 'About',
                'is_custom' => 0,
                'page_content' => '<p><strong>About Us:</strong></p>',
                'page_type' => 'about-us',
                'language_id' => 1,
                'page_icon' => '',
                'is_termspolicy' => 0,
                'is_privacypolicy' => 0,
                'status' => 1,
                'schema_markup' => '',
                'meta_title' => '',
                'og_image' => '',
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-condition',
                'meta_description' => 'Terms & Conditions',
                'meta_keywords' => 'Terms',
                'is_custom' => 0,
                'page_content' => '<p style="text-align: left;"><strong>1. Terms Conditions</strong></p>',
                'page_type' => 'terms-condition',
                'language_id' => 1,
                'page_icon' => '',
                'is_termspolicy' => 1,
                'is_privacypolicy' => 0,
                'status' => 1,
                'schema_markup' => '',
                'meta_title' => '',
                'og_image' => '',
                'updated_at' => now(),
                'created_at' => now(),
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'meta_description' => 'Privacy Policy',
                'meta_keywords' => 'Policy',
                'is_custom' => 0,
                'page_content' => '<p style="text-align: left;">NEWS APP &amp; CONTENT POLICY</p>',
                'page_type' => 'privacy-policy',
                'language_id' => 1,
                'page_icon' => '',
                'is_termspolicy' => 0,
                'is_privacypolicy' => 1,
                'status' => 1,
                'schema_markup' => '',
                'meta_title' => '',
                'og_image' => '',
                'updated_at' => now(),
                'created_at' => now(),
            ],
        ];
        if (Pages::count() === 0) {
            Pages::insert($pagesData);
        }

        $WebsettingsData = [['type' => 'web_name', 'message' => 'News'], ['type' => 'web_color_code', 'message' => '#FF0000'], ['type' => 'web_header_logo', 'message' => 'logos/header-logo.svg'], ['type' => 'web_footer_logo', 'message' => 'logos/footer-logo.svg'], ['type' => 'web_placeholder_image', 'message' => 'logos/placeholder.png'], ['type' => 'web_footer_description', 'message' => 'News Web website is an online platform that provides news and information about various topics, including current events, entertainment, politics, sports, technology, and more.']];

        if (WebSetting::count() === 0) {
            WebSetting::insert($WebsettingsData);
        }
    }
}
