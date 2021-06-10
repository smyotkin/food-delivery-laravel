@if(!empty($data) && $data->count())
    @foreach($data as $position)
        <tr>
            <td>
                <a href="/users/positions/{{ $position->id }}" class="text-decoration-none">{{ $position->name }}</a>
            </td>
            <td>{{ $position->slug }}</td>
            <td>{{ config('custom.statuses.' . $position->status)  }}</td>
        </tr>
    @endforeach

    {{ $data->links('vendor.pagination.table-next') }}
@else
    <tr>
        <td colspan="6" class="text-center">Должности не найдены</td>
    </tr>
@endif
