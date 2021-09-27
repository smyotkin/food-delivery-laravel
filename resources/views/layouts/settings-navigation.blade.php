<div class="container-fluid bg-lightgray px-4 px-md-5">
    <div class="row">
        <div class="col py-3 pb-md-0">
            <ul class="nav nav-tabs justify-content-center justify-content-md-start flex-column flex-md-row">
                @permission('settings_view')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('settings') ? 'active disabled' : '' }}" href="{{ route('settings.index') }}">Общие</a>
                    </li>
                @endpermission

                @permission('events_modify_and_view')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('system/events') ? 'active disabled' : '' }}" href="{{ route('events.index') }}">Системные события</a>
                    </li>
                @endpermission

                @permission('log_modify_and_view')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('system/logs') ? 'active disabled' : '' }}" href="{{ route('logs.index') }}">Лог ошибок</a>
                    </li>
                @endpermission

                @permission('notifications_modify_and_view')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('system/notifications') ? 'active disabled' : '' }}" href="{{ route('notifications.index') }}">Уведомления</a>
                    </li>
                @endpermission

                @permission('cities_view')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('cities') ? 'active disabled' : '' }}" href="{{ route('cities.index') }}">Города</a>
                    </li>
                @endpermission
            </ul>
        </div>
    </div>
</div>
