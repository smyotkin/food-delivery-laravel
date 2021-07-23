@if (!empty($data))
    @foreach($data as $event)
        <tr>
            <td>{{ $event['date'] }}</td>
            <td>{{ $event['name'] }}</td>
            <td>{{ $event['user']->full_name }}</td>
            <td>
                <div class="row">
                    <div class="col">
                        {{ $event['msg'] }}
                        <button type="button" class="btn btn-info btn-sm rounded-circle ms-1 px-2 py-0">i</button>
                    </div>
                </div>
            </td>
        </tr>
    @endforeach

    {{ $data->links('vendor.pagination.table-next') }}
@else
    <li class="list-group-item py-4 px-0">
        События не найдены
    </li>
@endif
