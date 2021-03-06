@if(!empty($data) && $data->count())
    @foreach($data as $user)
        <tr class="{{ $user->online == 'online' ? 'fw-bold' : '' }} {{ $user->is_active == 0 ? 'text-muted' : '' }}" data-page="{{ $data->currentPage() }}">
            <td>
                @permission('users_' . $user->status . '_view')
                    @if (!$user->isRoot($user->id) || $user->isRoot())
                        <a href="users/{{ $user->id }}" class="text-decoration-none {{ $user->is_active == 0 ? 'link-secondary' : '' }}">{{ $user->full_name }}</a>
                    @else
                        {{ $user->full_name }}
                    @endif
                @else_permission
                    {{ $user->full_name }}
                @endpermission
            </td>
            <td>{{ $user->phone_formatted }}</td>
            <td>{{ isset($user->roles[0]->status) ? $statuses[$user->roles[0]->status]['name'] : '---' }}</td>
            <td>{{ $user->roles[0]->name ?? '---' }}</td>
            <td>{{ $user->registered_at }}</td>
            <td>{{ $user->last_page }}</td>
            <td class="{{ $user->online == 'online' ? 'text-success' : '' }}">
                {{ $user->online }}
            </td>
        </tr>
    @endforeach

    {{ $data->links('vendor.pagination.table-next') }}
@else
    <tr>
        <td colspan="7" class="text-center">Пользователи не найдены</td>
    </tr>
@endif
