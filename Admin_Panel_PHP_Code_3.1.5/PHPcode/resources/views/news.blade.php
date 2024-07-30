@extends('layouts.main')

@section('title')
    {{ __('news') }}
@endsection

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ __('create_and_manage') . ' ' . __('news') }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item text-dark">
                            <a href="{{ route('home') }}" class="text-dark"><i class="fas fa-home mr-1"></i>{{ __('dashboard') }}</a>
                        </li>
                        <li class="breadcrumb-item active"><i class="nav-icon fas fa-newspaper mr-1"></i>{{ __('news') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end">
                    <button id="toggleButton" class="btn btn-primary mb-3 ml-1"><i class="fas fa-plus-circle mr-2"></i>{{ __('create') . ' ' . __('news') }}</button>
                </div>
                <div class="col-md-12" id="add_card">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('create') . ' ' . __('news') }}</h3>
                        </div>
                        <div class="card-body">
                            <form id="create_form" action="{{ url('news') }}" role="form" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label class="required">{{ __('language') }}</label>
                                            <select id="language_id" name="language" class="form-control" required>
                                                <option value="">{{ __('select') . ' ' . __('language') }}</option>
                                                @foreach ($languageList as $row)
                                                    <option value="{{ $row->id }}">{{ $row->language }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if (is_category_enabled() == 1)
                                            <div class="form-group">
                                                <label class="required">{{ __('category') }}</label>
                                                <select id="category_id" name="category_id" class="form-control" required>
                                                    <option value="">{{ __('select') . ' ' . __('category') }}</option>
                                                </select>
                                            </div>
                                            @if (is_subcategory_enabled() == 1)
                                                <div class="form-group">
                                                    <label>{{ __('subcategory') }}</label>
                                                    <select id="subcategory_id" name="subcategory_id" class="form-control">
                                                        <option value="">{{ __('select') . ' ' . __('subcategory') }}</option>
                                                    </select>
                                                </div>
                                            @endif
                                        @endif
                                        <div class="form-group">
                                            <label class="mr-2">{{ __('schema_markup') }}</label>
                                            <i data-content="Schema markup, also known as structured data, is the language search engines use to read and understand the content
                                                on your pages. By language, we mean a semantic vocabulary (code) that helps search engines characterize and categorize the content of web pages.
                                                Learn more about schema markup and generate it for your website using the .<a href='https://www.rankranger.com/schema-markup-generator' target='_blank'>Rank Ranger Schema Markup Generator</a>".
                                                class="fa fa-question-circle"></i>
                                            <input type="text" name="schema_markup" class="form-control" placeholder="{{ __('schema_markup') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>{{ __('notify_users') }}</label>
                                            <input type="hidden" id="notification" class="notification" name="notification" value="0">
                                            <input type="checkbox" id="is_notification" name="is_notification" class="status-switch">
                                        </div>

                                        <div class="form-group">
                                            <label class="required">{{ __('image') }}</label>
                                            <input name="file" type="file" accept="image/*" class="filepond" required>
                                        </div>
                                        <div class="form-group">
                                            <label>{{ __('gallery_image') }}</label>
                                            <input name="ofile[]" type="file" accept="image/*" multiple class="filepond logo">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <div class="form-group">
                                            <label class="required">{{ __('title') }}</label>
                                            <input name="title" type="text" placeholder="{{ __('title') }}" required class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label class="required">{{ __('slug') }}</label>
                                            <input name="slug" required type="text" placeholder="{{ __('slug') }}" class="form-control">
                                            <span class="text-danger">{{ __('avoid_special_characters') }}</span>
                                        </div>
                                        <div class="form-group">
                                            <label class="required">{{ __('content_type') }}</label>
                                            <select id="content_type" name="content_type" class="form-control" required>
                                                <option value="standard_post">{{ __('standard_post') }}</option>
                                                <option value="video_youtube">{{ __('video_youtube') }}</option>
                                                <option value="video_other">{{ __('video_other_url') }}</option>
                                                <option value="video_upload">{{ __('video_upload') }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group video_youtube">
                                            <label>{{ __('youtube_url') }}</label>
                                            <input type="url" name="youtube_url" class="form-control youtube_url">
                                            <span class="error invalid-feedback youtube_url_error"></span>
                                        </div>
                                        <div class="form-group video_other">
                                            <label>{{ __('other_url') }}</label>
                                            <input type="url" name="other_url" class="form-control other_url">
                                            <span class="error invalid-feedback other_url_error"></span>
                                        </div>
                                        <div class="form-group video_upload">
                                            <label>{{ __('video_uploads') }}</label>
                                            <input name="video_file" type="file" class="filepond-video">
                                        </div>
                                        <div class="form-group">
                                            <label class="required">{{ __('meta_keywords') }}</label>
                                            <input id="meta_tags" style="border-radius: 0.25rem" class="w-100" type="text" name="meta_keyword" placeholder="{{ __('press_enter_add_keywords') }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="required">{{ __('meta_title') }}</label>
                                            <input type="text" name="meta_title" class="form-control" id="meta_title" oninput="getWordCount('meta_title','meta_title_count','19.9px arial')" placeholder="{{ __('meta_title') }}" required>
                                            <h6 id="meta_title_count">0</h6>
                                        </div>
                                        <div class="form-group">
                                            <label class="required">{{ __('meta_description') }}</label>
                                            <textarea id="meta_description" name="meta_description" required class="form-control" oninput="getWordCount('meta_description','meta_description_count','12.9px arial')"></textarea>
                                            <h6 id="meta_description_count">0</h6>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        @if (is_location_news_enabled() == 1)
                                            <div class="form-group">
                                                <label>{{ __('location') }}</label>
                                                <select name="location_id" class="form-control" required>
                                                    <option value="">{{ __('select') . ' ' . __('location') }}</option>
                                                    @foreach ($locationList as $row)
                                                        <option value="{{ $row->id }}">{{ $row->location_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        <div class="form-group">
                                            <?php
                                            $datetime = new DateTime('tomorrow');
                                            $tomorrow = $datetime->format('Y-m-d');
                                            ?>
                                            <label>{{ __('show_till_expiry_date') }}</label>
                                            <input name="show_till" type="date" min="{{ $tomorrow }}" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>{{ __('tag') }}</label>
                                            <select id="tag_id" name="tag_id[]" class="form-control select2 select2-multiple" multiple="multiple">
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>{{ __('description') }}</label>
                                            <textarea id="des" name="des" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex col-12 justify-content-end p-0">
                                    <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('news') . ' ' . __('list') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-lg-2 col-md-3 col-sm-12">
                                    <label>{{ __('language') }}</label>
                                    <select id="filter_language_id" class="form-control">
                                        <option value="0">{{ __('select') . ' ' . __('language') }}</option>
                                        @foreach ($languageList as $row)
                                            <option value="{{ $row->id }}">{{ $row->language }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if (is_category_enabled() == 1)
                                    <div class="col-lg-2 col-md-3 col-sm-12">
                                        <label>{{ __('category') }}</label>
                                        <select id="filter_category_id" class="form-control">
                                            <option value="0">{{ __('select') . ' ' . __('category') }}</option>
                                        </select>
                                    </div>
                                    @if (is_subcategory_enabled() == 1)
                                        <div class="col-lg-2 col-md-3 col-sm-12">
                                            <label>{{ __('subcategory') }}</label>
                                            <select id="filter_subcategory_id" class="form-control">
                                                <option value="0">{{ __('select') . ' ' . __('subcategory') }}</option>
                                            </select>
                                        </div>
                                    @endif
                                @endif
                                @if (is_location_news_enabled() == 1)
                                    <div class="col-lg-2 col-md-3 col-sm-12">
                                        <label>{{ __('location') }}</label>
                                        <select id="filter_location_id" name="location" class="form-control" required>
                                            <option value="0">{{ __('select') . ' ' . __('location') }}</option>
                                            @foreach ($locationList as $row)
                                                <option value="{{ $row->id }}">{{ $row->location_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-lg-2 col-md-3 col-sm-12">
                                    <label>{{ __('status') }}</label>
                                    <select id="filter_status" name="status" class="form-control">
                                        <option value="">{{ __('status') }}</option>
                                        <option value="1">{{ __('active') }}</option>
                                        <option value="0">{{ __('deactive') }}</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-12">
                                    <label>{{ __('user') }}</label>
                                    <select id="filter_user_id" class="form-control">
                                        <option value="0">{{ __('select') . ' ' . __('user') }}</option>
                                        @foreach ($userList as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="toolbar">
                                <button class="btn bg-primary text-white" type="submit" id="bulk_order_update">{{ __('bulk_delete') }}</button>
                            </div>
                            <table aria-describedby="mydesc" id='table' data-toggle="table" data-url="{{ route('newsList') }}" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                                data-show-columns="true" data-show-refresh="true" data-toolbar="#toolbar" data-mobile-responsive="true" data-buttons-class="primary" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th class="text-center multi-check" data-checkbox="true">
                                        <th scope="col" data-field="id" data-sortable="true">{{ __('id') }}</th>
                                        <th scope="col" data-field="language_id" data-sortable="true" data-visible="false">{{ __('language_id') }}</th>
                                        <th scope="col" data-field="language_name">{{ __('language') }} </th>
                                        <th scope="col" data-field="category_id" data-sortable="true" data-visible="false">{{ __('category_id') }}</th>
                                        <th scope="col" data-field="category_name">{{ __('category') }}</th>
                                        <th scope="col" data-field="subcategory_id" data-visible="false">{{ __('subcategory_id') }}</th>
                                        <th scope="col" data-field="subcategory_name" data-visible="false">{{ __('subcategory') }}</th>
                                        <th scope="col" data-field="image">{{ __('image') }}</th>
                                        <th scope="col" data-field="title" data-sortable="true">{{ __('title') }}</th>
                                        <th scope="col" data-field="slug" data-visible="false">{{ __('slug') }}</th>
                                        <th scope="col" data-field="tag_id" data-visible="false">{{ __('tag_id') }}</th>
                                        <th scope="col" data-field="tag_name" data-visible="false">{{ __('tags') }}</th>
                                        <th scope="col" data-field="date" data-visible="false">{{ __('created_at') }}</th>
                                        <th scope="col" data-field="content_type">{{ __('content_type') }}</th>
                                        <th scope="col" data-field="short_description" data-visible="false">{{ __('description') }}</th>
                                        <th scope="col" data-field="meta_title" data-visible="fale">{{ __('meta_title') }}</th>
                                        <th scope="col" data-field="meta_keyword" data-visible="false">{{ __('meta_keywords') }}</th>
                                        <th scope="col" data-field="meta_description" data-visible="false">{{ __('meta_description') }}</th>
                                        <th scope="col" data-field="show_till" data-visible="false">{{ __('show_till') }}</th>
                                        <th scope="col" data-field="status_badge">{{ __('status') }}</th>
                                        <th scope="col" data-field="is_clone" data-sortable="true" data-visible="false">{{ __('is_clone') }}</th>
                                        <th scope="col" data-field="location" data-visible="false">{{ __('location') }}</th>
                                        <th scope="col" data-field="location_id" data-sortable="true" data-visible="false">{{ __('location_id') }}</th>
                                        <th scope="col" data-field="is_expire">{{ __('is_expired') }}</th>
                                        <th scope="col" data-field="total_image">{{ __('gallery_image') }}</th>
                                        <th scope="col" data-field="views">{{ __('views') }}</th>
                                        <th scope="col" data-field="likes">{{ __('likes') }}</th>
                                        <th scope="col" data-field="dislikes">{{ __('dislikes') }}</th>
                                        <th scope="col" data-field="operate" data-sortable="false" data-events="actionEvents">{{ __('operate') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editDataModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('edit') . ' ' . __('news') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="update_form" action="{{ url('news') }}" role="form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type='hidden' name="edit_id" id="edit_id" value='' />
                        <input type='hidden' name="image_url" id="image_url" value='' />
                        <input type='hidden' name="video_url" id="video_url" value='' />
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label class="required">{{ __('language') }}</label>
                                        <select id="edit_language_id" name="language" class="form-control" required>
                                            <option value="">{{ __('select') . ' ' . __('language') }}</option>
                                            @foreach ($languageList as $row)
                                                <option value="{{ $row->id }}">{{ $row->language }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if (is_category_enabled() == 1)
                                        <div class="form-group">
                                            <label class="required">{{ __('category') }}</label>
                                            <select id="edit_category_id" name="category_id" class="form-control" required>
                                                <option value="">{{ __('select') . ' ' . __('category') }}</option>
                                            </select>
                                        </div>
                                        @if (is_subcategory_enabled() == 1)
                                            <div class="form-group">
                                                <label>{{ __('subcategory') }}</label>
                                                <select id="edit_subcategory_id" name="subcategory_id" class="form-control">
                                                    <option value="">{{ __('select') . ' ' . __('subcategory') }}</option>
                                                </select>
                                            </div>
                                        @endif
                                    @endif
                                    <div class="form-group">
                                        <label class="mr-2">{{ __('schema_markup') }}</label>
                                        <i data-content="Schema markup, also known as structured data, is the language search engines use to read and understand the content
                                            on your pages. By language, we mean a semantic vocabulary (code) that helps search engines characterize and categorize the content of web pages.
                                            Learn more about schema markup and generate it for your website using the .<a href='https://www.rankranger.com/schema-markup-generator' target='_blank'>Rank Ranger Schema Markup Generator</a>".
                                            class="fa fa-question-circle"></i>
                                        <input type="text" id="edit_schema_markup" name="schema_markup" class="form-control" placeholder="{{ __('schema_markup') }}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('notify_users') }}</label>
                                        <input type="checkbox" id="edit_is_notification" name="edit_is_notification" class="edit-status-switch">
                                        <input type="hidden" id="edit_notification" name="notification" value="0">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('status') }}</label><br>
                                        <div id="status1" class="btn-group">
                                            <label class="btn btn-success" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                                <input type="radio" name="status" value="1" checked>{{ __('active') }}
                                            </label>
                                            <label class="btn btn-danger" data-toggle-class="btn-primary" data-toggle-passive-class="btn-default">
                                                <input type="radio" name="status" value="0">{{ __('deactive') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <div class="form-group">
                                        <label class="required">{{ __('title') }}</label>
                                        <input type="text" name="title" id="edit_title" class="form-control" placeholder="news title" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="required">{{ __('slug') }}</label>
                                        <input type="text" name="slug" id="edit_slug" class="form-control" placeholder="{{ __('slug') }}" required>
                                        <span class="text-danger">{{ __('avoid_special_characters') }}</span>
                                    </div>
                                    <div class="form-group">
                                        <?php
                                        $datetime = new DateTime('tomorrow');
                                        $tomorrow = $datetime->format('Y-m-d');
                                        ?>
                                        <label>{{ __('show_till_expiry_date') }}</label>
                                        <div class="custom-file">
                                            <input id="edit_show_till" type="date" name="show_till" class="form-control" placeholder="" min="{{ $tomorrow }}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="required">{{ __('content_type') }}</label>
                                        <select name="content_type" id="edit_content_type" class="form-control" required>
                                            <option value="standard_post" selected>{{ __('standard_post') }}</option>
                                            <option value="video_youtube">{{ __('video_youtube') }}</option>
                                            <option value="video_other">{{ __('video_other_url') }}</option>
                                            <option value="video_upload">{{ __('video_upload') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group evideo_youtube">
                                        <label>{{ __('youtube_url') }}</label>
                                        <input type="url" name="youtube_url" id="youtube_url" class="form-control youtube_url">
                                        <span class="error invalid-feedback youtube_url_error"></span>
                                    </div>
                                    <div class="form-group evideo_other">
                                        <label>{{ __('other_url') }}</label>
                                        <input type="url" name="other_url" id="other_url" class="form-control other_url">
                                        <span class="error invalid-feedback other_url_error"></span>
                                    </div>
                                    <div class="form-group evideo_upload">
                                        <label>{{ __('video_uploads') }}</label>
                                        <input name="video_file" type="file" class="filepond-video" id="exampleVideoInputFile1_edit">
                                    </div>
                                    <div class="form-group">
                                        <label class="required">{{ __('image') }} </label>
                                        <input name="file" type="file" accept="image/*" class="filepond">
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    @if (is_location_news_enabled() == 1)
                                        <div class="form-group">
                                            <label class="required">{{ __('location') }}</label>
                                            <select name="location_id" id="edit_location_id" class="form-control" required>
                                                <option value="">{{ __('select') . ' ' . __('location') }}</option>
                                                @foreach ($locationList as $row)
                                                    <option value="{{ $row->id }}">{{ $row->location_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label>{{ __('tag') }}</label>
                                        <select id="edit_tag_id" name="tag_id[]" class="form-control select2 select2-multiple" multiple="multiple">
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="required">{{ __('meta_keywords') }}</label>
                                        <input id="edit_meta_tags" style="border-radius: 0.25rem" class="w-100" type="text" name="meta_keyword" placeholder="{{ __('press_enter_add_keywords') }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="required">{{ __('meta_title') }}</label>
                                        <input type="text" name="meta_title" class="form-control" id="edit_meta_title" oninput="getWordCount('edit_meta_title','edit_meta_title_count','19.9px arial')" placeholder="{{ __('meta_title') }}" required>
                                        <h6 id="edit_meta_title_count">0</h6>
                                    </div>
                                    <div class="form-group">
                                        <label class="required">{{ __('meta_description') }}</label>
                                        <textarea id="edit_meta_description" name="meta_description" required class="form-control" oninput="getWordCount('edit_meta_description','edit_meta_description_count','12.9px arial')"></textarea>
                                        <h6 id="edit_meta_description_count">0</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editDataDesModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('edit') . ' ' . __('description') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('news_update_description') }}" method="POST" role="form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type='hidden' name="edit_id" value='' />
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <label>{{ __('description') }}</label>
                                    <textarea id="edit_des" name="des" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on('change', '#language_id', function(e) {
            var data = {
                language_id: $('#language_id').val(),
            };
            var url = '{{ route('get_category_by_language') }}';
            fetchList(url, data, '#category_id');

            var url1 = '{{ route('get_tag_by_language') }}';
            fetchList(url1, data, '#tag_id');
        });

        $(document).on('change', '#category_id', function(e) {
            var data = {
                category_id: $('#category_id').val(),
            };
            var url = '{{ route('get_subcategory_by_category') }}';
            fetchList(url, data, '#subcategory_id');
        });

        $(document).on('change', '#edit_language_id', function(e, row_language_id, row_category_id, row_subcategory_id, row_tag_id) {
            var language_id = $('#edit_language_id').val();
            $.ajax({
                url: '{{ route('get_category_by_language') }}',
                type: "POST",
                data: {
                    language_id: language_id,
                },
                beforeSend: function() {
                    $('#edit_category_id').html("Please wait..");
                },
                success: function(result) {
                    $('#edit_category_id').html(result);
                    if (language_id == row_language_id && row_category_id != 0) {
                        $('#edit_category_id').val(row_category_id).trigger('change', [row_category_id, row_subcategory_id]);
                    }
                },
                error: function(errors) {
                    console.log(errors);
                },
            });

            $.ajax({
                url: '{{ route('get_tag_by_language') }}',
                type: "POST",
                data: {
                    language_id: language_id,
                },
                beforeSend: function() {
                    $('#edit_tag_id').html("Please wait..");
                },
                success: function(result) {
                    $('#edit_tag_id').html(result).trigger("change");
                    if (language_id == row_language_id && row_tag_id != '') {
                        var valueArray = row_tag_id;
                        var arrayArea = valueArray.split(',');
                        $("#edit_tag_id").val(arrayArea).trigger("change");
                    }
                },
                error: function(errors) {
                    console.log(errors);
                },
            });
        });

        $(document).on('change', '#edit_category_id', function(e, row_category_id, row_subcategory_id) {
            var category_id = $('#edit_category_id').val();
            $.ajax({
                url: '{{ route('get_subcategory_by_category') }}',
                type: "POST",
                data: {
                    category_id: category_id,
                },
                beforeSend: function() {
                    $('#edit_subcategory_id').html("Please wait..");
                },
                success: function(result) {
                    $('#edit_subcategory_id').html(result);
                    if (category_id == row_category_id && row_subcategory_id != 0) {
                        $('#edit_subcategory_id').val(row_subcategory_id);
                    }
                },
                error: function(errors) {
                    console.log(errors);
                },
            });
        });

        $('input[name=status]').on('change', function(e) {
            var status = $(this).val();
            switcheryInstances.forEach(function(switchery) {
                if (status == 0) {
                    switchery.disable();
                } else {
                    switchery.enable();
                }
            });
        });
    </script>

    <script type="text/javascript">
        $(document).on('change', '#filter_language_id', function() {
            $('#table').bootstrapTable('refresh');
            var data = {
                language_id: $('#filter_language_id').val(),
            };
            var url = '{{ route('get_category_by_language') }}';
            fetchList(url, data, '#filter_category_id');
        });
        $(document).on('change', '#filter_category_id', function() {
            $('#table').bootstrapTable('refresh');
            var data = {
                category_id: $('#filter_category_id').val(),
            };
            var url = '{{ route('get_subcategory_by_category') }}';
            fetchList(url, data, '#filter_subcategory_id');
        });
        $(document).on('change', '#filter_subcategory_id', function() {
            $('#table').bootstrapTable('refresh');
        });
        $(document).on('change', '#filter_location_id', function() {
            $('#table').bootstrapTable('refresh');
        });
        $(document).on('change', '#filter_user_id', function() {
            $('#table').bootstrapTable('refresh');
        });
        $(document).on('change', '#filter_status', function() {
            $('#table').bootstrapTable('refresh');
        });
    </script>

    <script type="text/javascript">
        window.actionEvents = {
            'click .edit-data': function(e, value, row, index) {
                $('#edit_id').val(row.id);
                $("#image_url").val(row.image_url);
                $('#edit_slug').val(row.slug);
                $("#edit_title").val(row.title);
                $("#edit_date").val(row.date1);
                $("#edit_location_id").val(row.location_id);
                $("#edit_show_till").val(row.show_till);
                $('#edit_meta_tags').val(row.meta_keyword);
                $('#edit_schema_markup').val(row.schema_markup);
                $("#edit_content_type").val(row.content_type1).trigger('change');

                var con_value = row.content_value;
                $('.evideo_youtube').hide();
                $('.evideo_other').hide();
                $('.evideo_upload').hide();
                if (row.content_type1 == "video_youtube") {
                    $('.evideo_youtube').show();
                    $('#youtube_url').val(con_value);
                } else if (row.content_type1 == "video_other") {
                    $('.evideo_other').show();
                    $('#other_url').val(con_value);
                } else if (row.content_type1 == "video_upload") {
                    $('.evideo_upload').show();
                    $("#video_url").val('public/images/news_video/' + con_value);
                }
                if (row.status == '0') {
                    $("input[name=status][value=0]").prop('checked', true);
                } else {
                    $("input[name=status][value=1]").prop('checked', true);
                }
                switcheryInstances.forEach(function(switchery) {
                    if (row.status == '0') {
                        switchery.disable();
                    } else {
                        switchery.enable();
                    }
                });

                $('#edit_meta_description').val(row.meta_description);
                $('#edit_meta_title').val(row.meta_title);
                getWordCount('edit_meta_description', 'edit_meta_description_count', '12.9px arial');
                getWordCount('edit_meta_title', 'edit_meta_title_count', '19.9px arial');
                $("#edit_language_id").val(row.language_id).trigger('change', [row.language_id, row.category_id, row.subcategory_id, row.tag_id]);
            },
            'click .edit-data-des': function(e, value, row, index) {
                $('input[name=edit_id]').val(row.id);
                var des1 = tinyMCE.get('edit_des').setContent(row.description);
                $('#edit_des').val(des1);
            }
        };

        function queryParams(p) {
            return {
                sort: p.sort,
                order: p.order,
                limit: p.limit,
                offset: p.offset,
                search: p.search,
                language_id: $('#filter_language_id').val(),
                category_id: $('#filter_category_id').val(),
                subcategory_id: $('#filter_subcategory_id').val(),
                location_id: $('#filter_location_id').val(),
                user_id: $('#filter_user_id').val(),
                status: $('#filter_status').val(),
            };
        }
    </script>

    <script type="text/javascript">
        $('.youtube_url').on('change', function(e) {
            var url = $(this).val();
            if (url != undefined || url != '') {
                var regExp = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
                var match = url.match(regExp);
                if (match && match[1].length == 11) {
                    $(this).closest(".video_youtube").find(".youtube_url_error").hide();
                    $(this).closest(".video_youtube").find(".youtube_url_error").text("");
                    $(this).closest(".evideo_youtube").find(".youtube_url_error").hide();
                    $(this).closest(".evideo_youtube").find(".youtube_url_error").text("");
                } else {
                    $(this).closest(".video_youtube").find(".youtube_url_error").show();
                    $(this).closest(".video_youtube").find(".youtube_url_error").text("Please enter youtube url.");
                    $(this).val("");
                    $(this).closest(".evideo_youtube").find(".youtube_url_error").show();
                    $(this).closest(".evideo_youtube").find(".youtube_url_error").text("Please enter youtube url.");
                }
            }
        });
        $('.other_url').on('change', function(e) {
            var url = $(this).val();
            if (url != undefined || url != '') {
                var regExp =
                    /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
                var match = url.match(regExp);
                if (match && match[1].length == 11) {
                    $(this).closest(".video_other").find(".other_url_error").show();
                    $(this).closest(".video_other").find(".other_url_error").text("Youtube url is not valid for other url.");
                    $(this).val("");
                    $(this).closest(".evideo_other").find(".other_url_error").show();
                    $(this).closest(".evideo_other").find(".other_url_error").text("Youtube url is not valid for other url.");
                } else {
                    $(this).closest(".video_other").find(".other_url_error").hide();
                    $(this).closest(".video_other").find(".other_url_error").text("");
                    $(this).closest(".evideo_other").find(".other_url_error").hide();
                    $(this).closest(".evideo_other").find(".other_url_error").text("");
                }
            }
        });
        var notification = document.querySelector('#is_notification');
        notification.onchange = function() {
            if (notification.checked) {
                var t = $('#notification').val(1);
            } else {
                var t = $('#notification').val(0);
            }
        };
        var edit_is_notification = document.querySelector('#edit_is_notification');
        edit_is_notification.onchange = function() {
            if (edit_is_notification.checked) {
                $('#edit_notification').val(1);
            } else {
                $('#edit_notification').val(0);
            }
        };
        $(document).ready(function() {
            $(document).on('focusin', function(e) {
                if ($(e.target).closest(".tox-tinymce-aux, .moxman-window, .tam-assetmanager-root")
                    .length) {
                    e.stopImmediatePropagation();
                }
            });
            var base_url = baseUrl;
            tinymce.init({
                selector: "#des, #edit_des",
                height: 300,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify bullist numlist outdent indent removeformat link image media',
                image_uploadtab: false,
                images_upload_url: base_url + "/upload_img",
                relative_urls: false,
                remove_script_host: false,
                file_picker_types: 'image media',
                media_poster: false,
                media_alt_source: false,
                file_picker_callback: function(callback, value, meta) {
                    if (meta.filetype == "media" || meta.filetype == "image") {
                        const input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/* audio/* video/*');
                        input.addEventListener('change', (e) => {
                            const file = e.target.files[0];
                            var reader = new FileReader();
                            var fd = new FormData();
                            var files = file;
                            fd.append("file", files);
                            fd.append('filetype', meta.filetype);
                            // AJAX
                            jQuery.ajax({
                                url: base_url + "/upload_img",
                                type: "post",
                                data: fd,
                                contentType: false,
                                processData: false,
                                success: function(response) {
                                    const url = "/storage/" +
                                        response;
                                    callback(url);
                                }
                            });
                            reader.onload = function(e) {};
                            reader.readAsDataURL(file);
                        });
                        input.click();
                    }
                },
                setup: function(editor) {
                    editor.on("change keyup", function(e) {
                        editor.save();
                        $(editor.getElement()).trigger(
                            'change');
                    });
                }
            });
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function(e) {
            var elems = Array.prototype.slice.call(
                document.querySelectorAll(".status-switch")
            );
            elems.forEach(function(elem) {
                var switchery = new Switchery(elem, {
                    size: "small",
                    color: "#47C363",
                    secondaryColor: "#EB4141",
                    jackColor: "#ffff",
                    jackSecondaryColor: "#ffff",
                });
            });
            $('.video_youtube').hide();
            $('.video_other').hide();
            $('.video_upload').hide();
        });

        $(document).on('change', '#content_type', function() {
            var type = $("#content_type").val();
            $('.video_youtube').hide();
            $('.video_other').hide();
            $('.video_upload').hide();
            if (type == "video_youtube") {
                $('.video_youtube').show();
            } else if (type == "video_other") {
                $('.video_other').show();
            } else if (type == "video_upload") {
                $('.video_upload').show();
            }
        });

        $(document).on('change', '#edit_content_type', function() {
            var type = $("#edit_content_type").val();
            $('.evideo_youtube').hide();
            $('.evideo_other').hide();
            $('.evideo_upload').hide();
            if (type == "video_youtube") {
                $('.evideo_youtube').show();
            } else if (type == "video_other") {
                $('.evideo_other').show();
            } else if (type == "video_upload") {
                $('.evideo_upload').show();
            }
        });
    </script>

    <script type="text/javascript">
        //Clone News
        $(document).on('click', '.clone-data', function() {
            var base_url = baseUrl;
            var id = $(this).data("id");
            var image = $(this).data("image");
            var con_value = $(this).data("cvalue");
            $.ajax({
                url: base_url + '/clone_news',
                type: "POST",
                dataType: "json",
                data: {
                    id: id,
                    image_url: image,
                    con_value: con_value
                },
                success: function(result) {
                    if (result) {
                        showSuccessToast(result.message);
                        setTimeout(function() {
                            $("#table").bootstrapTable("refresh");
                        }, 1000);
                    }
                }
            });
        });

        $("#editDataDesModal form").on("submit", function(e) {
            e.preventDefault();
            let form = $(this);
            let data = form.serialize();

            $.ajax({
                type: "PUT",
                url: form.attr("action"),
                data: data,
                success: function(response) {
                    if (!response.error) {
                        $("#editDataDesModal").modal("hide");
                        $("#table").bootstrapTable("refresh");
                        showSuccessToast(response.message);
                    }
                },
                error: function(error) {
                    console.error(error);
                },
            });
        });

        var elems = Array.prototype.slice.call(document.querySelectorAll(".edit-status-switch"));
        var switcheryInstances = [];
        elems.forEach(function(elem) {
            var switchery = new Switchery(elem, {
                size: "small",
                color: "#47C363",
                secondaryColor: "#EB4141",
                jackColor: "#ffff",
                jackSecondaryColor: "#ffff",
            });
            switcheryInstances.push(switchery);
        });
    </script>

    <script type="text/javascript">
        $('#bulk_order_update').click(function() {
            var request_ids = [];
            selected = $('#table').bootstrapTable('getSelections');
            var arr = Object.values(selected);
            var i;
            var final_selection = [];
            var request_ids = arr.map(({
                id
            }) => id);
            if (request_ids.length) {
                Swal.fire({
                    title: '{{ __('are_you_sure') }}',
                    text: 'You won\'t be able to revert this!',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, proceed'
                }).then((result) => {
                    if (result.value) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            type: 'POST',
                            url: '{{ url('bulk_news_delete') }}',
                            data: {
                                request_ids: request_ids
                            },
                            success: function(response) {
                                if (response.error == false) {
                                    showSuccessToast(response.message);
                                    $('#table').bootstrapTable('refresh');
                                } else {
                                    showErrorToast(response.message);
                                }
                            },
                            error: function(response) {
                                return showToastMessage(response.message, "error");
                            }
                        });
                    }
                });
            } else {
                var message = '{{ __('select_data_to_delete') }}';
                showErrorToast(message);
            }
        });
    </script>
@endsection
