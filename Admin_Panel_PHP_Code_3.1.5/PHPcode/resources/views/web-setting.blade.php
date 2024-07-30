@extends('layouts.main')

@section('title')
    {{ __('web_setting') }}
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item text-dark">
                            <a href="{{ route('home') }}" class="text-dark"><i class="fas fa-home mr-1"></i>{{ __('dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item text-dark">
                            <a href="{{ url('system-settings') }}" class="text-dark"><i class="nav-icon fas fa-cogs mr-1"></i>{{ __('system_setting') }}</a>
                        </li>
                        <li class="breadcrumb-item  active"><i class="fas fas fa-tv mr-1"></i>{{ __('web_setting') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('system_settings_for_web') }}
                                <small class="text-bold">{{ __('directly_reflect_changes_in_web') }} </small>
                            </h3>
                        </div>
                        <form action="{{ route('web-settings.store') }}" role="form" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label>{{ __('web_name') }}</label>
                                        <input type="text" name="web_name" value="<?= $setting['web_name'] ? $setting['web_name'] : '' ?>" class="form-control" required />
                                    </div>
                                    <div class="form-group col-md-6 col-sm-12">
                                        <label>{{ __('web_color') }}</label>
                                        <input type="color" name="web_color_code" value="<?= $setting['web_color_code'] ? $setting['web_color_code'] : '' ?>" class="form-control" required />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label>{{ __('header_logo') }} <small class="text-danger">({{ __('size') }} 180 * 60)</small></label>
                                        <input name="web_header_logo" type="file" class="filepond">
                                        <img src="{{ url(Storage::url($setting['web_header_logo'])) }}" width="100" />
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label>{{ __('footer_logo') }}<small class="text-danger">({{ __('size') }} 180 * 60)</small></label>
                                        <input name="web_footer_logo" type="file" class="filepond">
                                        <img src="{{ url(Storage::url($setting['web_footer_logo'])) }}" height="100" />
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label>{{ __('placeholder_image') }}</label>
                                        <input name="web_placeholder_image" type="file" class="filepond">
                                        @if (isset($setting['web_placeholder_image']))
                                            <img src="{{ url(Storage::url($setting['web_placeholder_image'])) }}" height="100" />
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12 col-sm-12">
                                        <label>{{ __('footer_description') }}</label>
                                        <textarea name="web_footer_description" class="form-control"><?= $setting['web_footer_description'] ? $setting['web_footer_description'] : '' ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <button type="submit" class="btn btn-primary float-right">{{ __('submit') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
