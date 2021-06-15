<x-app-layout>
    <x-slot name="back_href">{{ route('users.index') }}</x-slot>
    <x-slot name="back_title">Пользователи</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Информация о пользователе</h5>
    </x-slot>

    <div class="container-fluid bg-light px-5 py-4 mb-4 border border-start-0 border-end-0 border-secondary">
        <div class="row">
            <div class="col d-flex align-items-center">
                <div class="info">
                    <h4 class="text-muted fw-light">{{ $user->first_name ?? 'Имя' }} {{ $user->last_name ?? 'Фамилия' }}</h4>
                    <h6 class="text-muted fw-normal mb-0">{{ isset($user->phone) ? $user->phoneNumber($user->phone) : 'Телефон' }}</h6>
                </div>
            </div>
            <div class="col text-end lh-base">
                <p class="mb-1">
                    <a href="javascript:" onclick="event.preventDefault(); $('#user_form').submit();" id="save_user" class="btn btn-outline-secondary py-0 disabled">Сохранить</a>
                </p>
                @if (isset($user))
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
                @endif
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
        <form method="post" action="{{ isset($user) ? route('users.update', ['user' => $user]) : route('users.store') }}" id="user_form" class="row g-3 update_user">
            @method(isset($user) ? 'patch' : 'post')
            @csrf

            <div class="col-4">
                <div class="row g-3">
                    @if (isset($user))
                        <input type="hidden" name="id" value="{{ $user->id }}">
                    @endif

                    <div class="col-6">
                        <label for="first_name" class="form-label">Имя</label>
                        <input type="text" class="form-control rounded-0" id="first_name" name="first_name" value="{{ $user->first_name ?? '' }}" placeholder="Имя">
                    </div>

                    <div class="col-6">
                        <label for="last_name" class="form-label">Фамилия</label>
                        <input type="text" class="form-control rounded-0" id="last_name" name="last_name" value="{{ $user->last_name ?? '' }}" placeholder="Фамилия">
                    </div>

                    <div class="col-12">
                        <label for="phone" class="form-label">Мобильный телефон</label>
                        <input type="text" class="form-control rounded-0 ru-phone_format" id="phone" name="phone" value="{{ isset($user->phone) ? $user->phoneNumber($user->phone) : '' }}" placeholder="+7 555 555-55-55">
                    </div>

                    <div class="col-12">
                        <label for="status" class="form-label">Статус</label>

                        <select class="form-select" id="status" name="status" required>
                            <option disabled selected>Ничего не выбрано</option>
                            @foreach ($statuses as $key => $status)
                                <option value="{{ $key }}" {{ isset($role->status) && $role->status == $key || old('status') == $key ? 'selected' : '' }}>{{ $status['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="position" class="form-label">Должность</label>

                        @include('users/positions-select')
                    </div>

                    <div class="col-12">
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ !empty($user->is_active) || !isset($user) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Учетная запись активна</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="row g-3">
                    <div class="col-12" id="permissions">
                        <label class="form-label">Права</label>

                        @if (isset($permissions))
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr class="bg-lightgray">
                                    <th class="text-start px-4" scope="col"></th>
                                    <th scope="col">Название</th>
                                    <th scope="col">Метка</th>
                                </tr>
                            </thead>

                            <tbody>
                            @php ($previousGroupValue = '')

                            @forelse ($permissions as $permission)
                                @if ($permission->group != $previousGroupValue)
                                    <tr>
                                        <td></td>
                                        <td colspan="2" class="py-3">
                                            <strong>{{ $permission->group }}</strong>
                                        </td>
                                    </tr>
                                @endif

                                <tr class="bg-light">
                                    <td class="text-center">
                                        <input class="form-check-input permission" type="checkbox" name="permissions[]" value="{{ $permission->slug }}" id="{{ $permission->slug }}" {{ isset($role_permissions) && in_array($permission->slug, $role_permissions) ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <label class="form-check-label" for="{{ $permission->slug }}">{{ $permission->name }}</label>
                                    </td>
                                    <td>{{ $permission->slug }}</td>
                                </tr>

                                @php ($previousGroupValue = $permission->group)
                            @empty
                                none
                            @endforelse
                            </tbody>
                        </table>
                        @else
                            <p class="text-muted text-center py-4">Пусто</p>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $("#status").on('change', function() {
                // $('#permissions').html('');

                $.ajax({
                    type: 'GET',
                    data: {
                        status: $(this).val(),
                    },
                    url: '{{ route('positions.getAjaxByStatus') }}',
                    success: function (data) {
                        $('#permissions').html($(
                            '<label class="form-label">Права</label><p class="text-muted text-center py-4">Пусто</p>'
                        ));

                        if (data.length > 0) {
                            $('#position').attr('disabled', false);

                            $('#position').html(data);
                        } else {
                            $('#position').attr('disabled', true);
                        }
                    }
                });
            });

            $("#position").on('change', function() {
                $.ajax({
                    type: 'GET',
                    data: {
                        id: $(this).val(),
                    },
                    url: '{{ route('positions.getPermissionsWithRole') }}',
                    success: function (data) {
                        $('#permissions').html(data);
                    }
                });
            });
        });
    </script>
</x-app-layout>
