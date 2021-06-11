@if(!empty($data) && $data->count())
    @foreach($data as $position)
        <tr>
            <td>
                <a href="/users/positions/{{ $position->id }}" class="text-decoration-none">{{ $position->name }}</a>
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
