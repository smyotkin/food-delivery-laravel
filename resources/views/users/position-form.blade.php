<div class="row">
    <div class="col">
        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />
    </div>
</div>

<div class="row">
    <form method="post" action="{{ isset($role) ? route('positions.update', ['position' => $role]) : route('positions.store') }}" id="position_form" class="col-10 update_position needs-validation" novalidate>
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
                        <input type="text" class="form-control rounded-0" id="name" name="name" value="{{ $role->name ?? '' }}" placeholder="Название" required>
                        <div class="invalid-feedback text-sm">
                            Поле обязательное, не менее 2 симв.
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="slug" class="form-label fw-bold">Метка</label>
                        <input type="text" class="form-control rounded-0" id="slug" name="slug" value="{{ $role->slug ?? '' }}" placeholder="Метка (латиницей с нижним подчеркиванием)" required>
                        <div class="invalid-feedback text-sm">
                            Поле обязательное, не менее 2 симв.
                        </div>
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

                        <table class="table table-sm align-middle caption-top" id="permissions">
                            <caption class="text-error text-danger text-xs pt-1 pb-2">Поле обязательное, не менее 1 права</caption>
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
                                        <input class="form-check-input permission" type="checkbox" name="permissions[]" value="{{ $permission->slug }}" id="{{ $permission->slug }}" {{ isset($role_permissions) && in_array($permission->slug, $role_permissions) ? 'checked' : '' }} required>
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
