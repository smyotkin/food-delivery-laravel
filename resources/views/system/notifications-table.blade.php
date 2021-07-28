@if (!empty($data))
    <table class="table align-middle">
        <thead>
            <tr class="bg-lightgray">
                <th class="px-3" scope="col">Ключ</th>
                <th class="px-3" scope="col">Сообщение</th>
                <th class="px-3" scope="col">ID получателей</th>
            </tr>
        </thead>

        <tbody>
            @php ($previousLabelValue = '')

            @foreach ($data as $notification)
                @if ($notification->label != $previousLabelValue)
                    <tr>
                        <td colspan="3" class="fs-5 p-3">{{ $notification->label }}</td>
                    </tr>
                @endif

                <tr class="bg-light">
                    <td class="p-3 fw-bold">{{ $notification->key }}</td>
                    <td class="p-3">
                        <a href="javascript:" onclick="$(this).hide()" class="notification_option btn text-start text-decoration-none text-dark rounded-3 border border-dark w-100 px-3 py-2">{{ $notification['msg_template'] }}</a>

                        <textarea class="d-none form-control" name="msg_template" rows="1">{{ $notification['msg_template'] }}</textarea>
                    </td>
                    <td class="position-relative p-3">
                        <a href="javascript:" onclick="$(this).hide()" class="notification_option btn text-decoration-none text-dark rounded-3 border border-dark w-100 px-3 py-2">{{ !empty($notification['recipient_ids']) ? $notification['recipient_ids'] : 'Не задано' }}</a>

                        <textarea class="d-none form-control" name="recipient_ids" rows="1">{{ $notification['recipient_ids'] }}</textarea>

                        <a href="javascript:" class="save-icon position-absolute start-100 top-0 h-100 ms-3" style="display: none;"></a>
                    </td>
                </tr>

                @php ($previousLabelValue = $notification->label)
            @endforeach
        </tbody>
    </table>
@else
    <li class="list-group-item py-4 px-0">
        Уведомления не найдены
    </li>
@endif
