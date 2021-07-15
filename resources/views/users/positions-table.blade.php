@if(!empty($data) && $data->count())
    @foreach($data as $position)
        <tr>
            <td>
                @permission('users_position_view')
                    <a href="/users/positions/{{ $position->id }}" class="text-decoration-none">{{ $position->name }}</a>
                @else_permission
                    {{ $position->name }}
                @endpermission
            </td>
            <td>{{ $position->slug }}</td>
            <td>{{ $statuses[$position->status]['name'] }}</td>
        </tr>
    @endforeach

    {{ $data->links('vendor.pagination.table-next') }}
@else
    <tr>
        <td colspan="3" class="text-center">Должности не найдены</td>
    </tr>
@endif
