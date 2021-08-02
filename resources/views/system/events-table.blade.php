@if (!empty($data) && $data->count() > 0)
    @foreach($data as $event)
        <tr>
            <td>{{ $event['date'] }}</td>
            <td>{{ $event['label'] }}</td>
            <td>{{ $event['user']->full_name ?? 'Неавторизован' }}</td>
            <td>
                <div class="row">
                    <div class="col">
                        {{ $event['msg'] }}

                        @if (!empty($event['data']))
                            <button type="button" class="btn btn-primary btn-sm rounded-circle ms-1 px-2 py-0" data-bs-toggle="modal" data-bs-target="#event_data_{{ $event['id'] }}">i</button>

                            <div class="modal fade" id="event_data_{{ $event['id'] }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel"><strong>Дополнительные данные:</strong></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <pre>{{ $event['data_formatted'] }}</pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    @endforeach

    {{ $data->links('vendor.pagination.table-next') }}
@else
    <tr>
        <td class="text-center text-muted" colspan="5">События не найдены</td>
    </tr>
@endif
