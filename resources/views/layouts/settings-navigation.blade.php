<div class="container-fluid bg-lightgray px-5">
    <div class="row">
        <div class="col pt-3">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('settings') ? 'active disabled' : '' }}" href="{{ route('settings.index') }}">Общие</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('system/events') ? 'active disabled' : '' }}" href="{{ route('events.index') }}">Системные события</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('system/logs') ? 'active disabled' : '' }}" href="{{ route('logs.index') }}">Лог ошибок</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Request::is('system/notifications') ? 'active disabled' : '' }}" href="{{ route('notifications.index') }}">Уведомления</a>
                </li>
            </ul>
        </div>
    </div>
</div>
