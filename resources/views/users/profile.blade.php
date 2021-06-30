<x-app-layout>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Назад</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Профиль пользователя</h5>
    </x-slot>

    <div class="container-fluid bg-light py-5 mb-4 border border-start-0 border-end-0 border-secondary">
        <div class="px-5">
            <div class="row px-5">
                <div class="col d-flex align-items-center">
                    <div class="info">
                        <h4>{{ $user->first_name }} {{ $user->last_name }}</h4>
                        <h6 class="text-muted fw-normal mb-0">{{ $user->phoneNumber($user->phone) }}</h6>
                    </div>
                </div>
                <div class="col">
                    <p class="fw-bold mb-0">Должность</p>
                    <p class="mb-0">{{ $role->name ?? '---' }}</p>
                </div>
                <div class="col">
                    <p class="fw-bold mb-0">Регистрация</p>
                    <p class="mb-0">
                        @php ($created_at = Date::parse($user->created_at))
                        {{ $created_at->format(now()->year == $created_at->year ? 'j F' : 'j F Y') }}
                    </p>
                </div>
                <div class="col">
                    <p class="fw-bold mb-0">Часовой пояс</p>
                    <button type="button" class="btn-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#exampleModal">{{ $timezones[$user->timezone] }}</button>

                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <form action="{{ route('profile.update', ['profile' => $user]) }}" class="modal-content" method="post">
                                @method('patch')
                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel"><strong>Изменить часовой пояс</strong></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="list-group">
                                        @foreach($timezones as $timezone => $title)
                                            <label class="list-group-item list-group-item-action">
                                                <input class="form-check-input me-1" name="timezone" type="radio" value="{{ $timezone }}" {{ !empty($user->timezone) && $user->timezone == $timezone ? 'checked' : '' }}>
                                                {{ $title }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отменить</button>
                                    <button type="submit" class="btn btn-primary">Сохранить</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mb-5">
        <div class="px-5 border-bottom border-gray pt-2 pb-4">
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

        <div class="px-5 border-bottom border-gray py-4">
            <div class="row px-5">
                <div class="col-3">
                    <h4 class="fw-normal mb-0">Доступ к данным</h4>
                </div>
                <div class="col-6">
                    <p class="mb-0 text-muted">Пусто</p>
                </div>
            </div>
        </div>

        <div class="px-5 border-bottom border-gray py-4">
            <div class="row px-5">
                <div class="col-3">
                    <h4 class="fw-normal mb-0">Пароль</h4>
                </div>
                <div class="col-4">
                    <button class="btn btn-link px-0 text-decoration-none" id="change_password">Изменить пароль...</button>

                    <form action="{{ route('profile.update', ['profile' => $user]) }}" method="post" class="d-none" id="change_password-form">
                        @method('patch')
                        @csrf

                        <input type="password" name="password" value="" placeholder="Новый пароль">
                        <button class="btn btn-link text-decoration-none disabled" type="submit">Сохранить</button>

                        <p class="text-sm mt-3 text-muted">Введите новый пароль, минимум шесть символов. <br> Пароль будет выслан на указанный номер телефона</p>
                    </form>
                </div>
            </div>
        </div>

        <div class="px-5 py-4">
            <div class="row px-5">
                <div class="col-3">
                    <form method="POST" action="{{ route('logout') }}">
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
    </div>

    <script>
        let changePwdForm = $('#change_password-form');
        let changePwdButton = $('#change_password-form button[type=submit]');

        $(document).ready(function() {
            $('#change_password').on('click', function (e) {
                $(this).hide();
                $('#change_password-form').removeClass('d-none');
            });
        });

        $('#change_password-form :input').on('keyup change', function() {
            if ($(this).val().length < 6)
                return changePwdButton.addClass('disabled');

            changePwdButton.removeClass('disabled');
        });

        changePwdButton.on('click', function (e) {
            e.preventDefault();

            swal({
                dangerMode: true,
                title: 'Вы уверены?',
                text: 'Что хотите изменить пароль',
                icon: 'warning',
                buttons: ['Отмена', 'Да, я уверен!']
            }).then(function(isConfirm) {
                if (isConfirm) {
                    changePwdForm.submit();
                }
            });
        });

        $('.logout-link').on('click', function (e) {
            e.preventDefault();
            let thisButton = $(this);

            swal({
                dangerMode: true,
                title: 'Вы уверены?',
                text: 'Что хотите выйти',
                icon: 'warning',
                buttons: ['Отмена', 'Да, я уверен!']
            }).then(function(isConfirm) {
                if (isConfirm) {
                    thisButton.closest('form').submit();
                }
            });
        });
    </script>
</x-app-layout>
