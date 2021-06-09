<x-app-layout>
    <x-slot name="back_href">{{ route('users') }}</x-slot>
    <x-slot name="back_title">Пользователи</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Информация о пользователе</h5>
    </x-slot>

    <div class="container-fluid bg-light px-5 py-4 mb-4 border border-start-0 border-end-0 border-secondary">
        <div class="row">
            <div class="col d-flex align-items-center">
                <div class="info">
                    <h4 class="text-muted fw-light">{{ 'Имя' }} {{ 'Фамилия' }}</h4>
                    <h6 class="text-muted fw-normal mb-0">{{ 'Телефон' }}</h6>
                </div>
            </div>
            <div class="col text-end lh-base">
                <p class="mb-1">
                    <a href="javascript:" onclick="event.preventDefault(); $('#user_form').submit();" id="save_user" class="btn btn-outline-secondary py-0 disabled">Сохранить</a>
                </p>
            </div>
        </div>
    </div>

    <div class="container-fluid px-5 mb-5">
        <div class="row">
            <div class="col">
                <!-- Validation Errors -->
                <x-auth-validation-errors class="mb-4" :errors="$errors" />
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <form method="POST" action="{{ route('users/store') }}" id="user_form" class="row g-3 update_user">
                    @csrf

                    <div class="col-md-6">
                        <label for="first_name" class="form-label">Имя</label>
                        <input type="text" class="form-control rounded-0" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="Имя">
                    </div>
                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Фамилия</label>
                        <input type="text" class="form-control rounded-0" id="last_name" name="last_name" value="{{ old('last_name') }}"  placeholder="Фамилия">
                    </div>
                    <div class="col-12">
                        <label for="phone" class="form-label">Мобильный телефон</label>
                        <input type="text" class="form-control rounded-0 ru-phone_format" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+7 555 555-55-55">
                    </div>
                    <div class="col-12">
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Учетная запись активна
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
