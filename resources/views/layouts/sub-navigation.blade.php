<div class="container-fluid bg-lightgray px-4 px-md-5">
    <div class="row">
        <div class="col py-3 pb-md-0">
            <ul class="nav nav-tabs justify-content-center justify-content-md-start flex-column flex-md-row">
                @permission('users_view')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('users') ? 'active disabled' : '' }}" href="{{ route('users.index') }}">Пользователи</a>
                    </li>
                @endpermission

                @permission('users_positions_view')
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('users/positions') ? 'active disabled' : '' }}" href="{{ route('positions.index') }}">Должности</a>
                    </li>
                @endpermission
            </ul>
        </div>
    </div>
</div>
