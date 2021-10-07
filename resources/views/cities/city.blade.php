<x-app-layout>
    <x-slot name="title">{{ isset($city) ? $city->name : 'Новый город' }}</x-slot>
    <x-slot name="back_href">{{ route('cities.index') }}</x-slot>
    <x-slot name="back_title">
        <span class="d-none d-md-inline-block">Города</span>
        <span class="d-inline-block d-md-none">Назад</span>
    </x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">{{ isset($city) ? 'Информация о городе' : 'Новый город' }}</h5>
    </x-slot>

    <div class="container-fluid bg-light px-4 px-md-5 py-4 mb-4 border border-start-0 border-end-0 border-secondary">
        <div class="row">
            <div class="col-12 col-md d-flex align-items-center">
                <div class="info">
                    <h4 class="text-muted fw-light">{{ $city->name ?? 'Название города' }}</h4>
                    <h6 class="text-muted fw-normal mb-0">{{ $city->phone ?? 'Телефон колл-центра' }}</h6>
                </div>
            </div>

            <div class="col-12 col-md text-end lh-base">
                @permission("cities_modify")
                    <p class="mb-2">
                        <a href="javascript:" class="save_btn d-block d-md-inline-block btn btn-outline-secondary py-1 py-md-0 mt-3 mt-md-0 disabled">Сохранить</a>
                    </p>
                @endpermission

                @isset($city)
                    @permission("cities_modify")
                        <form action="{{ route('cities.destroy', ['city' => $city->id]) }}" id="delete_city" method="post">
                            @method('delete')
                            @csrf

                            <button id="delete" class="text-danger text-sm pt-2">Удалить город</button>
                        </form>
                    @endpermission
                @endisset
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 px-md-5 mb-5">
        <div class="col-auto my-4 d-flex align-items-center" id="preloader">
            <div class="spinner-border text-secondary mr-4" role="status" style="width: 3rem; height: 3rem;"></div>
            <strong class="text-muted">Загрузка...</strong>
        </div>

        <div id="form_ajax"></div>
    </div>

    <script>
        $(document).ready(function() {
            $.ajax({
                url: '{{ route('cities/form/get.ajax') }}',
                type: 'GET',
                data: {
                    action: '{{ isset($city) ? 'show' : 'create' }}',
                    {{ isset($city) ? 'id: ' . $city->id : '' }}
                },
                beforeSend: function () {
                    $('#preloader').removeClass('d-none');
                },
                complete: function() {
                    $('#preloader').addClass('d-none');
                },
                success: function (data) {
                    $('#form_ajax').html(data);
                    $('.ru-phone_format').mask("+7 999 999-99-99", {
                        autoclear: false,
                    });
                },
                error: function(request) {
                    let errorMsg = request.status === 500 || request.responseJSON.message.length === 0 ? 'Произошла неизвестная ошибка' : request.responseJSON.message;

                    Swal.fire({
                        title: 'Ошибка',
                        text: errorMsg,
                        icon: 'warning',
                        confirmButtonText: 'Обновить',
                        cancelButtonText: 'Отмена',
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload(true);
                        }
                    });
                }
            });

            $('body').on('click', '.save_btn', function(event) {
                event.preventDefault();

                $.ajax({
                    url: $('#cities_form').prop('action'),
                    type: 'POST',
                    data: $('#cities_form').serialize(),
                    beforeSend: function () {
                        $('#preloader').removeClass('d-none');
                    },
                    complete: function() {
                        $('#preloader').addClass('d-none');
                    },
                    success: function () {
                        // window.location.replace('/cities');
                    },
                    error: function (response) {
                        // if (typeof(response.responseJSON.errors) != 'undefined') {
                        //     $('#phone').addClass('is-invalid');
                        //     $('#phone + .invalid-feedback').remove();
                        //     $('#phone').parent().append(
                        //         '<div class="invalid-feedback text-sm">' + response.responseJSON.errors.phone[0] + '</div>'
                        //     );
                        // }
                    }
                });
            });

        });

        $('body').on('keyup change', '#cities_form input, #cities_form select', function() {
            checkFormValidation();
        });

        function checkFormValidation() {
            $('#cities_form input, #cities_form select').removeClass('is-invalid validation-error alert-danger');

            $('.save_btn').addClass('disabled btn-outline-secondary');

            let fields = {
                'name': validateField($('#name').val().length < 2, $('#name')),
            };

            if (checkValidation(fields))
                $('.save_btn').removeClass('disabled btn-outline-secondary').addClass('btn-outline-primary');
        }

        $('body').on('click', '#delete', function (e) {
            e.preventDefault();

            Swal.fire({
                dangerMode: true,
                title: 'Вы уверены?',
                text: 'Данная запись будет удалена',
                icon: 'warning',
                confirmButtonText: 'Да, я уверен!',
                cancelButtonText: 'Отмена',
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#delete_city').submit();
                }
            });
        });
    </script>
</x-app-layout>
