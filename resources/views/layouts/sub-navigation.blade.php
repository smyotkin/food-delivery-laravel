<div class="container-fluid bg-lightgray px-5">
    <div class="row">
        <div class="col pt-3">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('users') ? 'active disabled' : '' }}" href="{{ route('users.index') }}">Пользователи</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('users/positions') ? 'active disabled' : '' }}" href="{{ route('positions.index') }}">Должности</a>
                </li>
            </ul>
        </div>
    </div>
</div>
