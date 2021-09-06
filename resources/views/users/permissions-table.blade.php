@if(!empty($permissions) && $permissions->count())
    <label class="form-label fw-bold">Права</label>

    <div class="table-responsive-sm">
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
                            <td colspan="2" class="py-3 {{ !$is_custom_permissions || !has_permission('users_modes_modify') ? 'text-muted' : '' }}">
                                <strong>{{ $permission->group }}</strong>
                            </td>
                        </tr>
                    @endif

                    <tr class="bg-light">
                        <td class="text-center">
                            <input class="form-check-input permission" type="checkbox" name="permissions[]" value="{{ $permission->slug }}" id="{{ $permission->slug }}" {{ isset($current_permissions) && in_array($permission->slug, $current_permissions) ? 'checked' : '' }} {{ !$is_custom_permissions || !has_permission('users_modes_modify') ? 'disabled' : '' }}>
                        </td>
                        <td>
                            <label class="form-check-label {{ isset($role_permissions) && in_array($permission->slug, $role_permissions) ? 'bg-secondary text-white px-2' : '' }} {{ !$is_custom_permissions || !has_permission('users_modes_modify') ? 'bg-transparent text-muted' : '' }} " for="{{ $permission->slug }}">
                                {{ $permission->name }}
                            </label>
                        </td>
                        <td class="{{ !$is_custom_permissions || !has_permission('users_modes_modify') ? 'text-muted' : '' }}">
                            {{ $permission->slug }}
                        </td>
                    </tr>

                    @php ($previousGroupValue = $permission->group)
                @endforeach
            </tbody>
        </table>
    </div>
@endif
