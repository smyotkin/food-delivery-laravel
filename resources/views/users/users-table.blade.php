@if(!empty($data) && $data->count())
    @foreach($data as $user)
        @php ($position = isset($roles) && isset($roles->keyBy('id')[$user->position_id]) ? $roles->keyBy('id')[$user->position_id] : null)

        <tr class="{{ $user->online == 'online' ? 'fw-bold' : '' }} {{ $user->is_active == 0 ? 'text-muted' : '' }}" data-page="{{ $data->currentPage() }}">
            <td>
                <a href="users/{{ $user->id }}" class="text-decoration-none {{ $user->is_active == 0 ? 'link-secondary' : '' }}">{{ $user->full_name }}</a>
            </td>
            <td>{{ $user->phone_formatted }}</td>
            <td>{{ $position->name ?? '---' }}</td>
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
        <td colspan="6" class="text-center">Пользователи не найдены</td>
    </tr>
@endif
