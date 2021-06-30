<x-app-layout>
    <x-slot name="back_href">{{ route('users.index') }}</x-slot>
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
                    <p class="mb-0">Московское время</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mb-5">
        <div class="px-5 border-bottom border-gray mb-4">
            <div class="row px-5">
                <div class="col-3">
                    <h4 class="fw-normal">Права доступа</h4>
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

        <div class="px-5 border-bottom border-gray mb-4">
            <div class="row px-5">
                <div class="col-3">
                    <h4 class="fw-normal">Доступ к данным</h4>
                </div>
                <div class="col-6">

                </div>
            </div>
        </div>

        <div class="px-5 border-bottom border-gray mb-4">
            <div class="row px-5">
                <div class="col-3">
                    <h4 class="fw-normal">Пароль</h4>
                </div>
                <div class="col-4">
                    <a href="javascript:" class="text-decoration-none">Изменить пароль...</a>
                </div>
            </div>
        </div>

        <div class="px-5">
            <div class="row px-5">
                <div class="col-3">
                    <a href="{{ route('logout') }}" class="text-decoration-none">Выйти из системы</a>
                </div>
                <div class="col-4">
                    @php ($updated_at = Date::parse($user->updated_at))

                    Последние изменение: {{ $user->first_name }} {{ $user->last_name }}, {{ $updated_at->format( now()->year == $updated_at->year ? 'j F, H:i' : 'j F Y') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
