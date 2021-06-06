<x-app-layout>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Пользователи</h5>
    </x-slot>

    <div class="container-fluid px-5 mb-5">
        <div class="row">
            <div class="col-5 mt-4">
                <input type="text" id="name-search" class="form-control rounded-0" placeholder="Поиск по номеру телефона или фамилии" aria-label="Поиск по номеру телефона или фамилии">
            </div>
            <div class="col-auto mt-4 d-flex align-items-center">
                <div class="spinner-border text-secondary d-none" role="status" id="preloader">
                    <span class="sr-only">Загрузка...</span>
                </div>
            </div>
        </div>
        <div class="row mt-5 mb-3">
            <div class="col-auto lh-1">
                <h5 class="d-inline-block fw-normal align-middle m-0">Пользователи <a href="javascript:" class="btn btn-sm btn-danger align-bottom rounded-0 px-1 py-0 ms-2"><small>CSV</small></a></h5>
            </div>
            <div class="col text-end">
                <a href="{{ route('users/add') }}" class="btn btn-outline-primary py-0">Новый пользователь</a>
            </div>
        </div>

        <div id="users_ajax">
        </div>
    </div>

    <script>
        $(document).ready(function() {
            showUsersList();

            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                let page = $(this).attr('href').split('page=')[1];

                showUsersList(page);
            });
        });

        function showUsersList(page = 1) {
            $.ajax({
                type: 'GET',
                url: '{{ route('users/getAJAX') }}?page=' + page,
                beforeSend: function () {
                    $('#preloader').removeClass('d-none');
                },
                complete: function() {
                    $('#preloader').addClass('d-none');
                },
                success: function (data) {
                    $('#users_ajax').html(data);
                }
            });
        }
    </script>
</x-app-layout>
