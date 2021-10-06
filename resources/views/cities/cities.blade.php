<x-app-layout>
    <x-slot name="title">Города</x-slot>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Города</h5>
    </x-slot>

    @include('layouts.settings-navigation')

    <div class="container-fluid px-5 mb-5">
        @include('components.header-search-preloader', ['id' => 'cities-search', 'placeholder' => 'Поиск по названию города или коду'])

        <div class="row mt-md-5 mb-3">
            <div class="col-auto lh-1">
                <h5 class="d-inline-block fw-normal align-middle m-0">
                    Города
                </h5>
            </div>

            <div class="col-12 col-md text-center text-md-end">
                @anyPermission('cities_modify')
                    <a href="{{ route('cities.create') }}" class="d-block d-md-inline-block btn btn-outline-primary py-1 py-md-0 mt-3 mt-md-0">Новый город</a>
                @endanyPermission
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="table-responsive-sm">
                    <table class="table table-striped">
                        <thead>
                            <tr class="fw-light bg-lightgray table-header">
                                <td class="border-0">Название</td>
                                <td class="border-0">Телефонный код</td>
                                <td class="border-0">Часовой пояс</td>
                                <td class="border-0">Каталог</td>
                                <td class="border-0">Прием заказов</td>
                            </tr>
                        </thead>
                        <tbody id="cities_ajax"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            updateTableList('cities', '{{ route('cities/get.ajax') }}');
        });
    </script>
</x-app-layout>
