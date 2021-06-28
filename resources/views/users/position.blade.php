<x-app-layout>
    <x-slot name="back_href">{{ route('positions.index') }}</x-slot>
    <x-slot name="back_title">Должности</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Информация о должности</h5>
    </x-slot>

    <div class="container-fluid bg-light px-5 py-4 mb-4 border border-start-0 border-end-0 border-secondary">
        <div class="row">
            <div class="col d-flex align-items-center">
                <div class="info">
                    <h4 class="text-muted fw-light">{{ $role->name ?? 'Название' }} ({{ $role->slug ?? 'Метка' }})</h4>
                    <h6 class="text-muted fw-normal mb-0">{{ isset($role->status) ? $statuses[$role->status]['name'] : 'Статус' }}</h6>
                </div>
            </div>
            <div class="col text-end lh-base">
                @if (isset($role))
                    @permission('users_position_modify')
                        <p class="mb-1">
                            <a href="javascript:" onclick="event.preventDefault(); $('#position_form').submit();" id="save" class="btn btn-outline-secondary py-0 disabled">Сохранить</a>
                        </p>
                    @endpermission
                @else
                    @permission('users_position_create')
                        <p class="mb-1">
                            <a href="javascript:" onclick="event.preventDefault(); $('#position_form').submit();" id="save" class="btn btn-outline-secondary py-0 disabled">Сохранить</a>
                        </p>
                    @endpermission
                @endif

                @if (isset($role))
                    <p class="mb-0 text-muted">
                        @php ($created_at = Date::parse($role->created_at))

                        <small>
                            Добавлена: {{ $created_at->format(now()->year == $created_at->year ? 'j F, H:i' : 'j F Y') }}
                        </small>
                    </p>
                    <p class="mb-0 text-muted">
                        @php ($updated_at = Date::parse($role->updated_at))

                        <small>
                            Обновлено: {{ $updated_at->format( now()->year == $updated_at->year ? 'j F, H:i' : 'j F Y') }}
                        </small>
                    </p>

                    @permission('users_position_delete')
                        <form action="{{ route('positions.destroy', ['position' => $role->id]) }}" method="post" id="delete_position">
                            @method('delete')
                            @csrf

                            <button id="delete" class="text-danger text-sm pt-2">Удалить должность</button>
                        </form>
                    @endpermission
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

        <div class="row">
            <form method="post" action="{{ isset($role) ? route('positions.update', ['position' => $role]) : route('positions.store') }}" id="position_form" class="col-10 update_position">
                @method(isset($role) ? 'patch' : 'post')
                @csrf

                <fieldset class="row g-3" {{ isset($role) && !auth()->user()->hasPermission('users_position_modify') ? 'disabled' : '' }}>
                    <div class="col-5">
                        <div class="row g-3">
                            @if (isset($role))
                                <input type="hidden" name="id" value="{{ $role->id }}">
                            @endif

                            <div class="col-12">
                                <label for="name" class="form-label fw-bold">Название</label>
                                <input type="text" class="form-control rounded-0" id="name" name="name" value="{{ $role->name ?? '' }}" placeholder="Название">
                            </div>

                            <div class="col-12">
                                <label for="slug" class="form-label fw-bold">Метка</label>
                                <input type="text" class="form-control rounded-0" id="slug" name="slug" value="{{ $role->slug ?? '' }}" placeholder="Метка (латиницей с нижним подчеркиванием)">
                            </div>

                            <div class="col-12">
                                <label for="status" class="form-label fw-bold">Статус</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option disabled selected>Ничего не выбрано</option>
                                    @foreach ($statuses as $key => $status)
                                        <option value="{{ $key }}" {{ isset($role->status) && $role->status == $key ? 'selected' : '' }}>{{ $status['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-7">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">Права</label>

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

                                        @foreach ($permissions as $permission)
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
                                                    <label class="form-check-label" for="{{ $permission->slug }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </td>
                                                <td>
                                                    {{ $permission->slug }}
                                                </td>
                                            </tr>

                                            @php ($previousGroupValue = $permission->group)
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <script>
        $('#delete').on('click', function (e) {
            e.preventDefault();

            swal({
                dangerMode: true,
                title: 'Вы уверены?',
                text: 'Данная запись будет удалена',
                icon: 'warning',
                buttons: ['Отмена', 'Да, я уверен!']
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $('#delete_position').submit();
                }
            });
        });
    </script>
</x-app-layout>
