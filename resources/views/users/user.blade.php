<x-app-layout>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Информация о пользователе</h5>
    </x-slot>

    <div class="container-fluid bg-light px-5 py-4 mb-4 border border-start-0 border-end-0 border-secondary">
        <div class="row">
            <div class="col d-flex align-items-center">
                <div class="info">
                    <h4 class="text-muted fw-light">{{ $user->first_name }} {{ $user->last_name }}</h4>
                    <h6 class="text-muted fw-normal mb-0">{{ $user->phone }}</h6>
                </div>
            </div>
            <div class="col text-end lh-base">
                <p class="mb-1">
                    <a href="javascript:" class="btn btn-outline-secondary py-0 disabled">Сохранить</a>
                </p>
                <p class="mb-0 text-muted">
                    @php ($created_at = Date::parse($user->created_at))

                    <small>
                        Регистрация: {{ $user->first_name }} {{ $user->last_name }},
                        {{ $created_at->format(now()->year == $created_at->year ? 'j F, H:i' : 'j F Y') }}
                    </small>
                </p>
                <p class="mb-0 text-muted">
                    @php ($updated_at = Date::parse($user->updated_at))

                    <small>
                        Изменения: {{ $user->first_name }} {{ $user->last_name }},
                        {{ $updated_at->format( now()->year == $updated_at->year ? 'j F, H:i' : 'j F Y') }}
                    </small>
                </p>
            </div>
        </div>
    </div>

    <div class="container-fluid px-5 mb-5">
        <div class="row">
            <div class="col-4">
                <form class="row g-3">
                    <div class="col-md-6">
                        <label for="firstname" class="form-label">Имя</label>
                        <input type="text" class="form-control rounded-0" id="firstname" value="{{ $user->first_name }}">
                    </div>
                    <div class="col-md-6">
                        <label for="lastname" class="form-label">Фамилия</label>
                        <input type="text" class="form-control rounded-0" id="lastname" value="{{ $user->last_name }}">
                    </div>
                    <div class="col-12">
                        <label for="phone" class="form-label">Мобильный телефон</label>
                        <input type="text" class="form-control rounded-0" id="phone" placeholder="" value="{{ $user->phone }}">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gridCheck" {{ $user->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="gridCheck">
                                Учетная запись активна
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
