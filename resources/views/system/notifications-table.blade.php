@if (!empty($data))
    <div class="table-responsive-sm">
        <table class="table align-middle">
            <thead>
                <tr class="bg-lightgray">
                    <th class="px-3" scope="col">Ключ</th>
                    <th class="px-3" scope="col" style="min-width: 300px;">Сообщение \ ID получателей</th>
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
                        <td class="p-3 position-relative">
                            <form action="{{ route('notifications.update', ['notification' => $notification->key]) }}">
                                @csrf

                                <input type="hidden" name="key" value="{{ $notification->key }}">

                                <div class="row">
                                    <div class="col-12 col-md d-flex align-items-center">
                                        <a href="javascript:" onclick="$(this).hide()" class="notification_option btn text-start text-decoration-none text-dark rounded-3 border border-dark w-100 px-3 py-2">{!! nl2br($notification['msg_template']) !!}</a>

                                        <textarea class="d-none form-control" name="msg_template" rows="1">{{ $notification['msg_template'] }}</textarea>
                                    </div>
                                    <div class="col-12 col-md-auto mt-3 mt-md-0">
                                        <a href="javascript:" onclick="$(this).hide()" class="notification_option btn text-decoration-none text-dark rounded-3 border border-dark w-100 px-3 py-2">{{ !empty($notification['recipient_ids']) ? $notification['recipient_ids'] : 'Не задано' }}</a>

                                        <textarea class="d-none form-control" name="recipient_ids" rows="1">{{ $notification['recipient_ids'] }}</textarea>

                                        <a href="javascript:" class="save-icon position-absolute start-100 top-0 h-100 ms-3" style="display: none;"></a>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>

                    @php ($previousLabelValue = $notification->label)
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <li class="list-group-item py-4 px-0">
        Уведомления не найдены
    </li>
@endif
