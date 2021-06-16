@if(!empty($permissions) && $permissions->count())
    <label class="form-label">Права</label>

    <table class="table table-sm align-middle" id="permissions">
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
@endif
