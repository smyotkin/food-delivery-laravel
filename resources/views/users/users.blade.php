<x-app-layout>
    <x-slot name="title">Пользователи</x-slot>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Пользователи</h5>
    </x-slot>

    @include('layouts.sub-navigation')

    <div class="container-fluid px-4 px-md-5 mb-5">
        @include('components.header-search-preloader', ['id' => 'users-search', 'placeholder' => 'Поиск по номеру телефона или фамилии'])

        <div class="row mb-3 mt-md-5">
            <div class="col-auto lh-1">
                <h5 class="d-inline-block fw-normal align-middle m-0">
                    Пользователи
                    @permission('users_download')
                        <a href="{{ '/users/export.csv' }}" class="btn btn-sm btn-danger align-bottom rounded-0 px-1 py-0 ms-2" id="download_csv"><small>CSV</small></a>
                    @endpermission
                </h5>
            </div>

            <div class="col-12 col-md text-center text-md-end">
                @anyPermission('users_employee_add|users_specialist_add|users_head_add|users_owner_add')
                    <a href="{{ route('users.create') }}" class="d-block d-md-inline-block btn btn-outline-primary py-1 py-md-0 mt-3 mt-md-0">Новый пользователь</a>
                @endanyPermission
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="table-responsive-sm">
                    <table class="table table-striped">
                        <thead>
                            <tr class="fw-light bg-lightgray table-header">
                                <td class="border-0">Имя</td>
                                <td class="border-0">Телефон</td>
                                <td class="border-0">Статус</td>
                                <td class="border-0">Должность</td>
                                <td class="border-0">Регистрация</td>
                                <td class="border-0">Страница</td>
                                <td class="border-0">Онлайн</td>
                            </tr>
                        </thead>
                        <tbody id="users_ajax"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            updateTableList('users', '{{ route('users/get.ajax') }}', 10000);
        });
    </script>
</x-app-layout>
