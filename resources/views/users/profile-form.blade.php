<div class="border-bottom border-gray pt-2 pb-4">
    <div class="row px-5">
        <div class="col-3">
            <h4 class="fw-normal mb-0">Права доступа</h4>
        </div>

        <div class="col-7">
            @php ($previousGroupValue = '')
            @php ($nextGroupValue = '')

            @foreach($current_permissions as $permission)
                @php ($nextGroupValue =  isset($current_permissions[$loop->index + 1]) ? $current_permissions[$loop->index + 1]->group : null)

                @if ($permission->group != $previousGroupValue)
                    <div class="row mb-2">
                        <div class="col-5">
                            @php ($iconPath = "img/{$permission->group_slug}-icon.png")

                            <div class="row">
                                <div class="col-auto">
                                    <img src="{{ url(file_exists($iconPath) ? $iconPath : "img/settings-icon.png") }}" alt="" class="profile-permission_icon">
                                </div>
                                <div class="col d-flex align-items-center">
                                    <strong class="fs-5">{{ $permission->group }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="col-7">
                            <ul class="list-group list-group-flush">
                                @endif

                                <li class="list-group-item">{{ $permission->name }}</li>

                                @if ($permission->group != $nextGroupValue || $loop->last)
                            </ul>
                        </div>
                    </div>

                    @if (!$loop->last)
                        <hr class="bg-secondary">
                    @endif
                @endif

                @php ($previousGroupValue = $permission->group)
            @endforeach
        </div>
    </div>
</div>

<div class="border-bottom border-gray py-4">
    <div class="row px-5">
        <div class="col-3">
            <h4 class="fw-normal mb-0">Доступ к данным</h4>
        </div>
        <div class="col-6">
            <p class="mb-0 text-muted">Пусто</p>
        </div>
    </div>
</div>

<div class="border-bottom border-gray py-4">
    <div class="row px-5">
        <div class="col-3">
            <h4 class="fw-normal mb-0">Пароль</h4>
        </div>
        <div class="col-4">
            <button class="btn btn-link px-0 text-decoration-none" id="change_password">Изменить пароль...</button>

            <form action="{{ route('profile.update', ['profile' => $user]) }}" method="post" class="change_password-form needs-validation d-none" novalidate>
                @method('patch')
                @csrf

                <fieldset class="row">
                    <div class="col">
                        <input class="form-control" type="password" name="password" id="password" value="" placeholder="Новый пароль">
                        <div class="invalid-feedback text-sm">
                            Поле обязательное, не менее 6 симв.
                        </div>

                        <p class="form-text-info text-sm mt-3 text-muted">Введите новый пароль, минимум шесть символов. Пароль будет выслан на указанный номер телефона</p>
                    </div>

                    <div class="col-auto">
                        <button class="btn btn-link text-decoration-none px-0 disabled" id="change_password-submit" type="submit">Сохранить</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>

<div class="py-4">
    <div class="row px-5">
        <div class="col-3">
            <form method="post" action="{{ route('logout') }}">
                @csrf

                <a href="route('logout')" onclick="" class="text-decoration-none logout-link">
                    {{ __('Выйти из системы') }}
                </a>
            </form>
        </div>
        <div class="col-4">
            @php ($updated_at = Date::parse($user->updated_at))

            Последние изменение: {{ $user->first_name }} {{ $user->last_name }}, {{ $updated_at->format( now()->year == $updated_at->year ? 'j F, H:i' : 'j F Y') }}
        </div>
    </div>
</div>
