<x-app-layout>
    <x-slot name="title">Должности</x-slot>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Должности</h5>
    </x-slot>

    @include('layouts.sub-navigation')

    <div class="container-fluid px-4 px-md-5 mb-5">
        @include('components.header-search-preloader', ['id' => 'positions-search', 'placeholder' => 'Поиск по названию или метке'])

        <div class="row mb-3 mt-md-5">
            <div class="col-12 col-md-auto lh-1">
                <h5 class="d-inline-block fw-normal align-middle m-0">Должности</h5>
            </div>

            @permission('users_position_create')
                <div class="col-12 col-md text-end">
                    <a href="{{ route('positions.create') }}" class="d-block d-md-inline-block btn btn-outline-primary py-1 py-md-0 mt-3 mt-md-0">Новая должность</a>
                </div>
            @endpermission
        </div>

        <div class="row">
            <div class="col">
                <div class="table-responsive-sm">
                    <table class="table table-striped">
                        <thead>
                            <tr class="fw-light bg-lightgray table-header">
                                <td class="border-0">Название</td>
                                <td class="border-0">Метка</td>
                                <td class="border-0">Статус</td>
                            </tr>
                        </thead>
                        <tbody id="positions_ajax"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            updateTableList('positions', '{{ route('positions.getAjax') }}');
        });
    </script>
</x-app-layout>
