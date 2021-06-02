<x-app-layout>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Пользователи</h5>
    </x-slot>

    <div class="container-fluid px-5 mb-5">
        <div class="row">
            <div class="col-5 mt-4">
                <input type="text" id="name-search" class="form-control rounded-0" placeholder="Поиск по номеру телефона или фамилии" aria-label="Поиск по номеру телефона или фамилии">
            </div>
        </div>
        <div class="row mt-5 mb-3">
            <div class="col-auto lh-1">
                <h5 class="d-inline-block fw-normal align-middle m-0">Пользователи <a href="javascript:" class="btn btn-sm btn-danger align-bottom rounded-0 px-1 py-0 ms-2"><small>CSV</small></a></h5>
            </div>
            <div class="col text-end">
                <a href="javascript:" class="btn btn-outline-primary py-0">Новый пользователь</a>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table class="table table-striped">
                     <tbody>
                        <tr class="fw-light">
                            <td>Имя</td>
                            <td>Телефон</td>
                            <td>Должность</td>
                            <td>Регистрация</td>
                            <td>Страница</td>
                            <td>Онлайн</td>
                        </tr>
                        @foreach ($users as $user)
                            @php ($online = Cache::has('user-is-online-' . $user->id))
                            @php ($date = Date::parse($user->created_at))

                            <tr class="{{ $online ? "fw-bold" : '' }}">
                                <td>
                                    <a href="users/{{ $user->id }}" class="text-decoration-none">{{ $user->first_name }} {{ $user->last_name }}</a>
                                </td>
                                <td>{{ $user->phoneNumber($user->phone) }}</td>
                                <td>-</td>
                                <td>
                                    @if (now()->year == $date->year)
                                        {{ $date->format('j F') }}
                                    @else
                                        {{ $date->format('j F Y') }}
                                    @endif
                                </td>
                                <td>{{ Cache::get('user-last-page-' . $user->id) }}</td>
                                <td>
                                    @if($online)
                                        <span class="text-success">online</span>
                                    @else
                                        @if ($user->last_seen != null)
                                            {{ Date::parse($user->last_seen)->diffForHumans() }}
                                        @else
                                            offline
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
