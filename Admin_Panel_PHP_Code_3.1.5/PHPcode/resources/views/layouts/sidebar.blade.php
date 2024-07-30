<!-- Main Sidebar Container -->
@php
    $currentUrl = url()->current();
@endphp

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('home') }}" class="brand-link">
        <img src="{{ url(Storage::url($setting['app_logo'])) }}" alt="Logo" class="brand-image" style="opacity:.8">
        <span class="brand-text text-bold">{{ isset($setting['app_name']) ? $setting['app_name'] : env('APP_NAME') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ url('home') }}" class="nav-link  {{ $currentUrl == url('home') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('dashboard') }}</p>
                    </a>
                </li>
                <div class="sidebar-new-title">
                    {{ __('news_management') }}
                </div>

                <li class="nav-item">
                    <a href="{{ url('category') }}" class="nav-link  {{ $currentUrl == url('category') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cube"></i>
                        <p>{{ __('category') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('sub_category') }}" class="nav-link  {{ $currentUrl == url('sub_category') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cubes"></i>
                        <p>{{ __('subcategory') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('tag') }}" class="nav-link {{ $currentUrl == url('tag') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tag"></i>
                        <p>{{ __('tag') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('news') }}" class="nav-link {{ $currentUrl == url('news') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-newspaper"></i>
                        <p>{{ __('news') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('breaking_news') }}" class="nav-link {{ $currentUrl == url('breaking_news') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-newspaper"></i>
                        <p>{{ __('breaking_news') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('live_streaming') }}" class="nav-link {{ $currentUrl == url('live_streaming') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-stream"></i>
                        <p>{{ __('live_streaming') }}</p>
                    </a>
                </li>

                <div class="sidebar-new-title">
                    {{ __('home_screen_management') }}
                </div>

                <li class="nav-item">
                    <a href="{{ url('featured_sections') }}" class="nav-link {{ $currentUrl == url('featured_sections') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>{{ __('featured_section') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('ad_spaces') }}" class="nav-link {{ $currentUrl == url('ad_spaces') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-ad"></i>
                        <p> {{ __('ad_spaces') }} </p>
                    </a>
                </li>

                <div class="sidebar-new-title">
                    {{ __('user_management') }}
                </div>

                <li class="nav-item">
                    <a href="{{ url('app_users') }}" class="nav-link {{ $currentUrl == url('app_users') ? 'active' : '' }}">
                        <em class="fas fa-user nav-icon"></em>
                        <p>{{ __('user') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('app_users_roles') }}" class="nav-link {{ $currentUrl == url('app_users_roles') ? 'active' : '' }}">
                        <em class="fas fa-user-tie nav-icon"></em>
                        <p>{{ __('user_role') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('comments') }}" class="nav-link {{ $currentUrl == url('comments') ? 'active' : '' }}">
                        <em class="nav-icon fas fa-comments"></em>
                        <p> {{ __('comment') }} </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('comments_flag') }}" class="nav-link {{ $currentUrl == url('comments_flag') ? 'active' : '' }}">
                        <em class="nav-icon fas fa-flag"></em>
                        <p> {{ __('comment_flag') }} </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('notifications') }}" class="nav-link {{ $currentUrl == url('notifications') ? 'active' : '' }}">
                        <em class="nav-icon fas fa-bullhorn"></em>
                        <p> {{ __('send_notification') }} </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('survey') }}" class="nav-link {{ $currentUrl == url('survey') ? 'active' : '' }}">
                        <em class="nav-icon fas fa-poll-h"></em>
                        <p> {{ __('survey') }} </p>
                    </a>
                </li>


                <div class="sidebar-new-title">
                    {{ __('others') }}
                </div>

                <li class="nav-item">
                    <a href="{{ url('location') }}" class="nav-link  {{ $currentUrl == url('location') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-map-marker"></i>
                        <p>{{ __('location') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('pages') }}" class="nav-link  {{ $currentUrl == url('pages') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file"></i>
                        <p>{{ __('pages') }}</p>
                    </a>
                </li>

                <div class="sidebar-new-title">
                    {{ __('system_configuration') }}
                </div>

                <li class="nav-item">
                    <a href="{{ url('system-settings') }}" class="nav-link  {{ $currentUrl == url('system-settings') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>{{ __('system_setting') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('database-backup') }}" class="nav-link  download-link">
                        <em class="nav-icon fas fa-cloud-download-alt"></em>
                        <p> {{ __('database_backup') }} </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
@section('script')
<script type="text/javascript">
        // Add this script to open the dropdown
        document.addEventListener('DOMContentLoaded', function() {
            var menuOpenElement = document.querySelector('.nav-item.has-treeview.menu-open > ul');
            if (menuOpenElement) {
                menuOpenElement.style.display = 'block';
            }
        });
    </script>
@endsection
