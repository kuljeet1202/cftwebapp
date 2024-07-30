@extends('layouts.main')

@section('title')
    {{ __('app_setting') }}
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="form-group col-md-6 col-sm-12">
                </div>
                <div class="form-group col-md-6 col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item text-dark">
                            <a href="{{ route('home') }}" class="text-dark"><i class="fas fa-home mr-1"></i>{{ __('dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item text-dark">
                            <a href="{{ url('system-settings') }}" class="text-dark"><i class="nav-icon fas fa-cogs mr-1"></i>{{ __('system_setting') }}</a>
                        </li>
                        <li class="breadcrumb-item active"><i class="fas fas fa-tablet-alt mr-1"></i>{{ __('app_setting') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card card-secondary h-100">
                        <form id="extra_form" action="{{ route('app-settings.store') }}" novalidate role="form" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-header">
                                <h3 class="card-title">{{ __('system_settings_for_app') }}
                                    <small class="text-bold">{{ __('directly_reflect_changes_in_app') }} </small>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-2">
                                        <label>{{ __('category') }}</label>
                                        <div>
                                            <input type="checkbox" id="is_category" name="is_category" class="status-switch" @if ($setting['category_mode'] == '1') checked @endif>
                                            <input type="hidden" id="category_mode" name="category_mode" value="{{ $setting['category_mode'] ? $setting['category_mode'] : 0 }}">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>{{ __('subcategory') }}</label>
                                        <div>
                                            <input type="checkbox" id="is_subcategory" name="is_subcategory" class="status-switch" @if ($setting['subcategory_mode'] == '1') checked @endif>
                                            <input type="hidden" id="subcategory_mode" class="status-switch" name="subcategory_mode" value="{{ $setting['subcategory_mode'] ? $setting['subcategory_mode'] : 0 }}">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>{{ __('breaking_news') }}</label>
                                        <div>
                                            <input type="checkbox" id="breaking_news" name="breaking_news" class="status-switch" @if ($setting['breaking_news_mode'] == '1') checked @endif>
                                            <input type="hidden" id="breaking_news_mode" class="status-switch" name="breaking_news_mode" value="{{ $setting['breaking_news_mode'] ? $setting['breaking_news_mode'] : 0 }}">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>{{ __('live_streaming') }}</label>
                                        <div>
                                            <input type="checkbox" id="is_live_streaming" name="is_live_streaming" class="status-switch" @if ($setting['live_streaming_mode'] == '1') checked @endif>
                                            <input type="hidden" id="live_streaming_mode" class="status-switch" name="live_streaming_mode" value="{{ $setting['live_streaming_mode'] ? $setting['live_streaming_mode'] : 0 }}">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>{{ __('comment') }}</label>
                                        <div>
                                            <input type="checkbox" id="is_comments" name="is_comments" class="status-switch" @if ($setting['comments_mode'] == '1') checked @endif>
                                            <input type="hidden" id="comments_mode" class="status-switch" name="comments_mode" value="{{ $setting['comments_mode'] ? $setting['comments_mode'] : 0 }}">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>
                                            {{ __('enable_location_in_news') }}</label>
                                        <div>
                                            <input type="checkbox" id="location_news" name="location_news" class="status-switch" @if ($setting['location_news_mode'] == '1') checked @endif>
                                            <input type="hidden" id="location_news_mode" class="status-switch" name="location_news_mode" value="{{ $setting['location_news_mode'] ? $setting['location_news_mode'] : 0 }}">
                                        </div>
                                    </div>

                                    <div class="form-group col-md-3 col-sm-12 mt-2 nearestLocationMeasureHide">
                                        <label>{{ __('nearest_location_measure_in_km') }}</label>
                                        <input min="1000" type="number" name="nearest_location_measure" value="{{ $setting['nearest_location_measure'] ? $setting['nearest_location_measure'] : 0 }}" class="form-control" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="form-group col-md-6 col-sm-12">
                                    <div class="card card-secondary h-100">
                                        <div class="card-header">
                                            <h5 class="text-bold">{{ __('android_ads') }}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="form-group col-md-2 col-sm-6">
                                                    <label>{{ __('in_app_ads') }}</label>
                                                    <div>
                                                        <input type="checkbox" id="in_app_ads" name="in_app_ads" class="status-switch" @if ($setting['in_app_ads_mode'] == '1') checked @endif>
                                                        <input type="hidden" id="in_app_ads_mode" name="in_app_ads_mode" value="{{ $setting['in_app_ads_mode'] ? $setting['in_app_ads_mode'] : '0' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6 col-sm-12 adsHide">
                                                    <div>
                                                        <div class="form-check">
                                                            <input type="radio" class="form-check-input" name="ads_type" value="1" @if ($setting['ads_type'] == '1') checked @endif>
                                                            <label class="form-check-label mr-4">{{ __('google_admob') }}</label>

                                                            <input type="radio" class="form-check-input" name="ads_type" value="2" @if ($setting['ads_type'] == '2') checked @endif>
                                                            <label class="form-check-label mr-4">{{ __('facebook_ads') }}</label>

                                                            <input type="radio" class="form-check-input" name="ads_type" value="3" @if ($setting['ads_type'] == '3') checked @endif>
                                                            <label class="form-check-label">{{ __('unity_ads') }}</label>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                            <div class="adsgoogle adsHide row">
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label> {{ __('google_rewarded_video_id') }}</label>
                                                    <input type="text" name="google_rewarded_video_id" class="form-control googleAtt" placeholder="google Rewarded Video Id" required value="{{ $setting['google_rewarded_video_id'] ? $setting['google_rewarded_video_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('google_interstitial_id') }}</label>
                                                    <input type="text" name="google_interstitial_id" class="form-control googleAtt" placeholder="google Interstitial Id" required value="{{ $setting['google_interstitial_id'] ? $setting['google_interstitial_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('google_banner_id') }}</label>
                                                    <input type="text" name="google_banner_id" class="form-control googleAtt" required value="{{ $setting['google_banner_id'] ? $setting['google_banner_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('google_native_unit_id') }}</label>
                                                    <input type="text" name="google_native_unit_id" class="form-control googleAtt" required value="{{ $setting['google_native_unit_id'] ? $setting['google_native_unit_id'] : '0' }}" />
                                                </div>
                                            </div>
                                            <div class="adsfacebook adsHide row">
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('fb_rewarded_video_id') }}</label>
                                                    <input type="text" name="fb_rewarded_video_id" class="form-control facebookAtt" required value="{{ $setting['fb_rewarded_video_id'] ? $setting['fb_rewarded_video_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('fb_interstitial_id') }}</label>
                                                    <input type="text" name="fb_interstitial_id" class="form-control facebookAtt" required value="{{ $setting['fb_interstitial_id'] ? $setting['fb_interstitial_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('fb_banner_id') }}</label>
                                                    <input type="text" name="fb_banner_id" class="form-control facebookAtt" required value="{{ $setting['fb_banner_id'] ? $setting['fb_banner_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('fb_native_unit_id') }}</label>
                                                    <input type="text" name="fb_native_unit_id" class="form-control facebookAtt" required value="{{ $setting['fb_native_unit_id'] ? $setting['fb_native_unit_id'] : '0' }}" />
                                                </div>
                                            </div>
                                            <div class="adsunity adsHide row">
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('unity_rewarded_video_id') }}</label>
                                                    <input type="text" name="unity_rewarded_video_id" class="form-control unityAtt" required value="{{ $setting['unity_rewarded_video_id'] ? $setting['unity_rewarded_video_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('unity_interstitial_id') }}</label>
                                                    <input type="text" name="unity_interstitial_id" class="form-control unityAtt" required value="{{ $setting['unity_interstitial_id'] ? $setting['unity_interstitial_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('unity_banner_id') }}</label>
                                                    <input type="text" name="unity_banner_id" class="form-control unityAtt" required value="{{ $setting['unity_banner_id'] ? $setting['unity_banner_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('unity_game_id') }}</label>
                                                    <input type="text" name="android_game_id" class="form-control unityAtt" required value="{{ $setting['android_game_id'] ? $setting['android_game_id'] : '0' }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <div class="card card-secondary h-100">
                                        <div class="card-header">
                                            <h5 class="text-bold">
                                                {{ __('ios_ads') }} </h5>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="form-group col-md-2 col-sm-6">
                                                    <label>{{ __('in_app_ads') }}</label>
                                                    <div>
                                                        <input type="checkbox" id="ios_in_app_ads" name="ios_in_app_ads" class="status-switch" @if ($setting['ios_in_app_ads_mode'] == '1') checked @endif>
                                                        <input type="hidden" id="ios_in_app_ads_mode" class="status-switch" name="ios_in_app_ads_mode" value="{{ $setting['ios_in_app_ads_mode'] ? $setting['ios_in_app_ads_mode'] : '0' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6 col-sm-12 iOSadsHide">
                                                    <div>
                                                        <div class="form-check">
                                                            <input type="radio" class="form-check-input" name="ios_ads_type" value="1" @if ($setting['ios_ads_type'] == '1') checked @endif>
                                                            <label class="form-check-label mr-4">{{ __('google_admob') }}</label>

                                                            <input type="radio" class="form-check-input" @if ($setting['ios_ads_type'] == '2') checked @endif name="ios_ads_type" value="2">
                                                            <label class="form-check-label mr-4">{{ __('facebook_ads') }}</label>

                                                            <input type="radio" class="form-check-input" name="ios_ads_type" value="3" @if ($setting['ios_ads_type'] == '3') checked @endif>
                                                            <label class="form-check-label">{{ __('unity_ads') }} </label>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                            <div class="iOSadsgoogle iOSadsHide row">
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('google_rewarded_video_id') }}</label>
                                                    <input type="text" name="ios_google_rewarded_video_id" class="form-control iOSgoogleAtt" placeholder="google Rewarded Video Id" required value="{{ $setting['ios_google_rewarded_video_id'] ? $setting['ios_google_rewarded_video_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('google_interstitial_id') }}</label>
                                                    <input type="text" name="ios_google_interstitial_id" class="form-control iOSgoogleAtt" placeholder="google Interstitial Id" required value="{{ $setting['ios_google_interstitial_id'] ? $setting['ios_google_interstitial_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('google_banner_id') }}</label>
                                                    <input type="text" name="ios_google_banner_id" class="form-control iOSgoogleAtt" required value="{{ $setting['ios_google_banner_id'] ? $setting['ios_google_banner_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('google_native_unit_id') }}</label>
                                                    <input type="text" name="ios_google_native_unit_id" class="form-control iOSgoogleAtt" required value="{{ $setting['ios_google_native_unit_id'] ? $setting['ios_google_native_unit_id'] : '0' }}" />
                                                </div>
                                            </div>
                                            <div class="iOSadsfacebook iOSadsHide row">
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('fb_rewarded_video_id') }}</label>
                                                    <input type="text" name="ios_fb_rewarded_video_id" class="form-control iOSfacebookAtt" required value="{{ $setting['ios_fb_rewarded_video_id'] ? $setting['ios_fb_rewarded_video_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('fb_interstitial_id') }}</label>
                                                    <input type="text" name="ios_fb_interstitial_id" class="form-control iOSfacebookAtt" required value="{{ $setting['ios_fb_interstitial_id'] ? $setting['ios_fb_interstitial_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('fb_banner_id') }}</label>
                                                    <input type="text" name="ios_fb_banner_id" class="form-control iOSfacebookAtt" value="{{ $setting['ios_fb_banner_id'] ? $setting['ios_fb_banner_id'] : '0' }}" required />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('fb_native_unit_id') }}</label>
                                                    <input type="text" name="ios_fb_native_unit_id" class="form-control iOSfacebookAtt" required value="{{ $setting['ios_fb_native_unit_id'] ? $setting['ios_fb_native_unit_id'] : '0' }}" />
                                                </div>
                                            </div>
                                            <div class="iOSadsunity iOSadsHide row">
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('unity_rewarded_video_id') }}</label>
                                                    <input type="text" name="ios_unity_rewarded_video_id" class="form-control iOSunityAtt" required value="{{ $setting['ios_unity_rewarded_video_id'] ? $setting['ios_unity_rewarded_video_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('unity_interstitial_id') }}</label>
                                                    <input type="text" name="ios_unity_interstitial_id" class="form-control iOSunityAtt" required value="{{ $setting['ios_unity_interstitial_id'] ? $setting['ios_unity_interstitial_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('unity_banner_id') }}</label>
                                                    <input type="text" name="ios_unity_banner_id" class="form-control iOSunityAtt" required value="{{ $setting['ios_unity_banner_id'] ? $setting['ios_unity_banner_id'] : '0' }}" />
                                                </div>
                                                <div class="form-group col-md-6 col-sm-12">
                                                    <label>{{ __('unity_game_id') }}</label>
                                                    <input type="text" name="ios_game_id" class="form-control iOSunityAtt" required value="{{ $setting['ios_game_id'] ? $setting['ios_game_id'] : '0' }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script type="text/javascript">
        $('#extra_form').validate({
            rules: {},
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
        $(document).ready(function(e) {
            var elems = Array.prototype.slice.call(
                document.querySelectorAll(".status-switch")
            );

            @if ($setting['location_news_mode'] == 0)
                $('.nearestLocationMeasureHide').hide();
            @else
                $('.nearestLocationMeasureHide').show();
            @endif


            elems.forEach(function(elem) {
                var switchery = new Switchery(elem, {
                    size: "small",
                    color: "#47C363",
                    secondaryColor: "#EB4141",
                    jackColor: "#ffff",
                    jackSecondaryColor: "#ffff",
                });
            });
            var is_category = document.querySelector('#is_category');
            is_category.onchange = function() {
                if (is_category.checked) {
                    $('#category_mode').val(1);
                } else {
                    $('#category_mode').val(0);
                    $('#subcategory_mode').val(0);
                }
            };
            /* on change of category mode btn - switchery js */
            var is_subcategory = document.querySelector('#is_subcategory');
            is_subcategory.onchange = function() {
                if (is_subcategory.checked) {
                    if ($('#category_mode').val() == '1') {
                        $('#subcategory_mode').val(1);
                    } else if ($('#category_mode').val() == '0') {
                        alert('Please enable category');
                        $("#is_subcategory").bootstrapSwitch('state', false);
                    }
                } else {
                    $('#subcategory_mode').val(0);
                }
            };
            /* on change of breaking_news mode btn - switchery js */
            var breaking_news = document.querySelector('#breaking_news');
            breaking_news.onchange = function() {
                if (breaking_news.checked)
                    $('#breaking_news_mode').val(1);
                else
                    $('#breaking_news_mode').val(0);
            };
            /* on change of comments mode btn - switchery js */
            var is_comments = document.querySelector('#is_comments');
            is_comments.onchange = function() {
                if (is_comments.checked)
                    $('#comments_mode').val(1);
                else
                    $('#comments_mode').val(0);
            };
            /* on change of live streaming mode btn - switchery js */
            var is_live_streaming = document.querySelector('#is_live_streaming');
            is_live_streaming.onchange = function() {
                if (is_live_streaming.checked)
                    $('#live_streaming_mode').val(1);
                else
                    $('#live_streaming_mode').val(0);
            };
            /* on change of Location wise news mode btn - switchery js */
            var location_news = document.querySelector('#location_news');


            location_news.onchange = function() {
                if (location_news.checked) {
                    $('#location_news_mode').val(1);
                    $('.nearestLocationMeasureHide').show();
                } else {
                    $('#location_news_mode').val(0);
                    $('.nearestLocationMeasureHide').hide();
                }
            };

            /* on change of google ads mode btn - switchery js */
            var in_app_ads = document.querySelector('#in_app_ads');
            in_app_ads.onchange = function() {
                if (in_app_ads.checked) {
                    $('#in_app_ads_mode').val(1);
                    $('.adsHide').show();
                    var ads_type = $("input:radio[name=ads_type]:checked").val();
                    ads_type_manage(ads_type);
                } else {
                    $('#in_app_ads_mode').val(0);
                    $('.adsHide').hide();
                    ads_type_manage(0);
                }
            };
            var ios_in_app_ads = document.querySelector('#ios_in_app_ads');
            ios_in_app_ads.onchange = function() {
                if (ios_in_app_ads.checked) {
                    $('#ios_in_app_ads_mode').val(1);
                    $('.iOSadsHide').show();
                    var ios_ads_type = $("input:radio[name=ios_ads_type]:checked").val();
                    ios_ads_type_manage(ios_ads_type);
                } else {
                    $('#ios_in_app_ads_mode').val(0);
                    $('.iOSadsHide').hide();
                    ios_ads_type_manage(0);
                }
            };
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            //google ads
            $('.adsHide').hide();
            $('.adsgoogle').hide();
            $('.adsfacebook').hide();
            $('.adsunity').hide();
            var ads = $('#in_app_ads_mode').val();
            if (ads === '1' || ads === 1) {
                $('.adsHide').show();
                var ads_type = $("input:radio[name=ads_type]:checked").val();
                if (ads_type == undefined) {
                    $("input[name=ads_type][value=1]").prop('checked', true);
                }
            } else {
                $('.adsHide').hide();
                $('.adsfacebook').hide();
                $('.facebookAtt').removeAttr('required');
                $('.adsgoogle').hide();
                $('.googleAtt').removeAttr('required');
                $('.adsunity').hide();
                $('.unityAtt').removeAttr('required');
            }
            var ads_type = $("input:radio[name=ads_type]:checked").val();
            ads_type_manage(ads_type);
            //ios ads
            $('.iOSadsHide').hide();
            $('.iOSadsgoogle').hide();
            $('.iOSadsfacebook').hide();
            $('.iOSadsunity').hide();
            var ios_ads = $('#ios_in_app_ads_mode').val();
            if (ios_ads === '1' || ios_ads === 1) {
                $('.iOSadsHide').show();
                var ios_ads_type = $("input:radio[name=ios_ads_type]:checked").val();
                if (ios_ads_type == undefined) {
                    $("input[name=ios_ads_type][value=1]").prop('checked', true);
                }
            } else {
                $('.iOSadsHide').hide();
                $('.iOSadsgoogle').hide();
                $('.iOSgoogleAtt').removeAttr('required');
                $('.iOSadsfacebook').hide();
                $('.iOSfacebookAtt').removeAttr('required');
                $('.iOSadsunity').hide();
                $('.iOSunityAtt').removeAttr('required');
            }
            var ios_ads_type = $("input:radio[name=ios_ads_type]:checked").val();
            ios_ads_type_manage(ios_ads_type);
        });

        function ads_type_manage(ads_type) {
            var ads = $('#in_app_ads_mode').val();
            if (ads == 1 || ads == '1') {
                // $('.adsHide').hide();
                $('.adsfacebook').hide();
                $('.facebookAtt').prop('required', false);
                $('.adsgoogle').hide();
                $('.googleAtt').prop('required', false);
                $('.adsunity').hide();
                $('.unityAtt').prop('required', false);
                if (ads_type === '1' || ads_type === 1) {
                    $('.adsgoogle').show();
                    $('.googleAtt').prop('required', true);
                } else if (ads_type === '2' || ads_type === 2) {
                    $('.adsfacebook').show();
                    $('.facebookAtt').prop('required', true);
                } else if (ads_type === '3' || ads_type === 3) {
                    $('.adsunity').show();
                    $('.unityAtt').prop('required', true);
                }
            }
        }

        function ios_ads_type_manage(ios_ads_type) {
            var ios_ads = $('#ios_in_app_ads_mode').val();
            if (ios_ads === '1' || ios_ads === 1) {
                // $('.iOSadsHide').hide();
                $('.iOSadsfacebook').hide();
                $('.iOSfacebookAtt').prop('required', false);
                $('.iOSadsgoogle').hide();
                $('.iOSgoogleAtt').prop('required', false);
                $('.iOSadsunity').hide();
                $('.iOSunityAtt').prop('required', false);
                if (ios_ads_type === '1' || ios_ads_type === 1) {
                    $('.iOSadsgoogle').show();
                    $('.iOSgoogleAtt').prop('required', true);
                } else if (ios_ads_type === '2' || ios_ads_type === 2) {
                    $('.iOSadsfacebook').show();
                    $('.iOSfacebookAtt').prop('required', true);
                } else if (ios_ads_type === '3' || ios_ads_type === 3) {
                    $('.iOSadsunity').show();
                    $('.iOSunityAtt').prop('required', true);
                }
            }
        }
        $(document).on('click', 'input[name="ios_ads_type"]', function() {
            var ios_ads_type = $(this).val();
            ios_ads_type_manage(ios_ads_type);
        });
        $(document).on('click', 'input[name="ads_type"]', function() {
            var ads_type = $(this).val();
            ads_type_manage(ads_type);
        });
    </script>
@endsection
