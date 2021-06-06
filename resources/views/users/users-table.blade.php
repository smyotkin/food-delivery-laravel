@if(!empty($data) && $data->count())
    <div class="row">
        <div class="col">
            <table class="table table-striped">
                <tbody>
                    <tr class="fw-light bg-light table-header">
                        <td class="border-0">Имя</td>
                        <td class="border-0">Телефон</td>
                        <td class="border-0">Должность</td>
                        <td class="border-0">Регистрация</td>
                        <td class="border-0">Страница</td>
                        <td class="border-0">Онлайн</td>
                    </tr>

                    @foreach($data as $user)
                        <tr class="{{ $user->online == 'online' ? 'fw-bold' : '' }}">
                            <td>
                                <a href="users/{{ $user->id }}" class="text-decoration-none">{{ $user->full_name }}</a>
                            </td>
                            <td>{{ $user->phone_formatted }}</td>
                            <td>-</td>
                            <td>{{ $user->registered_at }}</td>
                            <td>{{ $user->last_page }}</td>
                            <td class="{{ $user->online == 'online' ? 'text-success' : '' }}">{{ $user->online }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            {{ $data->links() }}
        </div>
    </div>
@else
    Данные не найдены
@endif
