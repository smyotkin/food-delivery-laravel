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
                        <h4>{{ $user->first_name ?? 'Имя' }} {{ $user->last_name ?? 'Фамилия' }}</h4>
                        <h6 class="text-muted fw-normal mb-0">{{ $user->phoneNumber($user->phone) }}</h6>
                    </div>
                </div>
                <div class="col">
                    <p class="fw-bold mb-0">Статус</p>
                    <p class="mb-0">-</p>
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
                <div class="col-4">
                    <h4 class="fw-normal">Права доступа</h4>
                </div>
                <div class="col-4">
                    <div class="row">
                        <div class="col d-flex align-items-center">
                            <img src="/img/callcenter-icon.png" alt="" class="h-25 mh-25 rounded me-2">
                            <strong>Колл-центр</strong>
                        </div>
                        <div class="col d-flex align-items-center">Чтение и запись</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-5 border-bottom border-gray mb-4">
            <div class="row px-5">
                <div class="col-4">
                    <h4 class="fw-normal">Доступ к данным</h4>
                </div>
                <div class="col-6">

                </div>
            </div>
        </div>

        <div class="px-5 border-bottom border-gray mb-4">
            <div class="row px-5">
                <div class="col-4">
                    <h4 class="fw-normal">Пароль</h4>
                </div>
                <div class="col-4">
                    <a href="javascript:" class="text-decoration-none">Изменить пароль...</a>
                </div>
            </div>
        </div>

        <div class="px-5">
            <div class="row px-5">
                <div class="col-4">
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
