@extends('layouts.main')

@section('title')
    {{ __('seo_setting') }}
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
                            <a href="{{ 'system-settings' }}" class="text-dark"><i class="nav-icon fas fa-cogs mr-1"></i>{{ __('system_setting') }}</a>
                        </li>
                        <li class="breadcrumb-item active"><i class="fas fas fa-chart-bar mr-1"></i>{{ __('seo_setting') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end">
                    <button id="toggleButton" class="btn btn-primary mb-3 ml-1"><i class="fas fa-plus-circle mr-2"></i>{{ __('create') . ' ' . __('seo_page') }}</button>
                </div>
                <div class="col-md-12" id="add_card">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('create') . ' ' . __('seo_page') }}</h3>
                        </div>
                        <form id="create_form" action="{{ url('seo-setting') }}" role="form" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label class="required">{{ __('seo_news_pages') }}</label>
                                        <select id="page_type" name="page_type" class="form-control">
                                            @foreach ($options as $option)
                                                <option value="{{ $option }}" @if (in_array($option, $pages->toArray())) disabled @endif>{{ page_type($option) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label>{{ __('schema_markup') }}</label>
                                        <i data-content="Schema markup, also known as structured data, is the language search engines use to read and understand the content
                                                    on your pages. By language, we mean a semantic vocabulary (code) that helps search engines characterize and categorize the content of web pages.
                                                    Learn more about schema markup and generate it for your website using the .<a href='https://www.rankranger.com/schema-markup-generator' target='_blank'>Rank Ranger Schema Markup Generator</a>".
                                            class="fa fa-question-circle"></i>
                                        <input type="text" name="schema_markup" id="schema_markup" class="form-control" placeholder="{{ __('schema_markup') }}" value="{{ isset($setting) && isset($setting['schema_markup']) && isset($setting['schema_markup']) ? $setting['schema_markup'] : '' }}">
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <?php
                                        $meta_keyword = [];
                                        if (isset($setting['meta_keyword']) && isset($setting['meta_keyword'])) {
                                            json_decode($setting['meta_keyword']);
                                            if (json_last_error() === JSON_ERROR_NONE) {
                                                $meta_keyword = json_decode($setting['meta_keyword']);
                                            } else {
                                                $meta_keyword = $setting['meta_keyword'];
                                            }
                                        } else {
                                            $meta_keyword = '';
                                        }
                                        ?>
                                        <label class="required">{{ __('meta_keywords') }}</label>
                                        <input id="meta_tags" style="border-radius: 0.25rem" class="w-100" type="text" name="meta_keyword" placeholder="{{ __('press_enter_add_keywords') }}" value="{{ $meta_keyword }}">
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label class="required">{{ __('meta_title') }}</label>
                                        <input type="text" name="meta_title" class="form-control" id="meta_title" oninput="getWordCount('meta_title','meta_title_count','19.9px arial')" placeholder="{{ __('meta_title') }}"
                                            value="{{ isset($setting) && isset($setting['meta_title']) && isset($setting['meta_title']) ? $setting['meta_title'] : '' }}">
                                        <h6 id="meta_title_count">0</h6>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label class="required">{{ __('meta_description') }}</label>
                                        <textarea id="meta_description" name="meta_description" required class="form-control" oninput="getWordCount('meta_description','meta_description_count','12.9px arial')">{{ isset($setting) && isset($setting['meta_description']) && isset($setting['meta_description']) ? $setting['meta_description'] : '' }} </textarea>
                                        <h6 id="meta_description_count">0</h6>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label class="required">{{ __('og_image') }} </label>
                                        <input name="og_image" type="file" class="filepond">
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
        <div class="col-md-12">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('seo_page') . ' ' . __('list') }}</h3>
                </div>
                <div class="card-body">
                    <table aria-describedby="mydesc" id='table' data-toggle="table" data-url="{{ route('seoSettingList') }}" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true"
                        data-show-refresh="true" data-toolbar="#toolbar" data-mobile-responsive="true" data-buttons-class="primary" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc"
                        data-query-params="queryParams">
                        <thead>
                            <tr>
                                <th scope="col" data-field="id" data-sortable="true">{{ __('id') }}</th>
                                <th scope="col" data-field="og_image">{{ __('og_image') }}</th>
                                <th scope="col" data-field="page_type_badge">{{ __('page_type') }}</th>
                                <th scope="col" data-field="meta_title">{{ __('name') }}</th>
                                <th scope="col" data-field="meta_keyword">{{ __('meta_keywords') }}</th>
                                <th scope="col" data-field="meta_title">{{ __('meta_title') }}</th>
                                <th scope="col" data-field="schema_markup">{{ __('schema_markup') }}</th>
                                <th scope="col" data-field="meta_description">{{ __('meta_description') }}</th>
                                <th scope="col" data-field="operate" data-events="actionEvents">{{ __('operate') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editDataModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('edit') . ' ' . __('seo_page') }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="update_form" action="{{ url('seo-setting') }}" role="form" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type='hidden' name="edit_id" id="edit_id" value='' />
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-6 col-sm-12">
                                    <label class="required">{{ __('seo_news_pages') }}</label>
                                    <select id="edit_page_type" name="page_type" class="form-control">
                                        @foreach ($options as $option)
                                            <option value="{{ $option }}">{{ page_type($option) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label>{{ __('schema_markup') }}</label>
                                    <i data-content="Schema markup, also known as structured data, is the language search engines use to read and understand the content
                                                    on your pages. By language, we mean a semantic vocabulary (code) that helps search engines characterize and categorize the content of web pages.
                                                    Learn more about schema markup and generate it for your website using the .<a href='https://www.rankranger.com/schema-markup-generator' target='_blank'>Rank Ranger Schema Markup Generator</a>".
                                        class="fa fa-question-circle"></i>
                                    <input type="text" name="schema_markup" class="form-control" id="edit_schema_markup" placeholder="{{ __('schema_markup') }}">
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label class="required">{{ __('meta_keywords') }}</label>
                                    <input id="edit_meta_tags" style="border-radius: 0.25rem" class="w-100" type="text" name="meta_keyword" placeholder="{{ __('press_enter_add_keywords') }}">
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label class="required">{{ __('meta_title') }}</label>
                                    <input type="text" name="meta_title" class="form-control" id="edit_meta_title" oninput="getWordCount('edit_meta_title','edit_meta_title_count','19.9px arial')" placeholder="{{ __('meta_title') }}">
                                    <h6 id="edit_meta_title_count">0</h6>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label class="required">{{ __('image') }} </label>
                                    <input name="og_image" type="file" class="filepond">
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label class="required">{{ __('meta_description') }} </label>
                                    <textarea id="edit_meta_description" name="meta_description" required class="form-control" oninput="getWordCount('edit_meta_description','edit_meta_description_count','12.9px arial')"></textarea>
                                    <h6 id="edit_meta_description_count">0</h6>
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
        window.actionEvents = {
            'click .edit-data': function(e, value, row, index) {
                $('#edit_id').val(row.id);
                $('#edit_page_type').val(row.page_type);
                $('#edit_meta_title').val(row.meta_title);
                $('#edit_meta_tags').val(row.meta_keyword);
                $('#edit_schema_markup').val(row.schema_markup);
                $('#edit_meta_description').val(row.meta_description)
                getWordCount('edit_meta_description', 'edit_meta_description_count', '12.9px arial');
                getWordCount('edit_meta_title', 'edit_meta_title_count', '19.9px arial');
            }
        }

        function queryParams(p) {
            return {
                sort: p.sort,
                order: p.order,
                limit: p.limit,
                offset: p.offset,
                search: p.search,
            };
        }
    </script>
@endsection
