<x-app-layout>
    <x-slot name="title">{{ isset($role) ? $role->name : 'Новая должность' }}</x-slot>
    <x-slot name="back_href">{{ route('positions.index') }}</x-slot>
    <x-slot name="back_title">
        <span class="d-none d-md-inline-block">Должности</span>
        <span class="d-inline-block d-md-none">Назад</span>
    </x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">{{ isset($user) ? 'Информация о должности' : 'Новая должность' }}</h5>
    </x-slot>

    <div class="container-fluid bg-light px-4 px-md-5 py-4 mb-4 border border-start-0 border-end-0 border-secondary">
        <div class="row">
            <div class="col-12 col-md d-flex align-items-center">
                <div class="info">
                    <h4 class="text-muted fw-light">{{ $role->name ?? 'Название' }} ({{ $role->slug ?? 'Метка' }})</h4>
                    <h6 class="text-muted fw-normal mb-0">{{ isset($role->status) ? $statuses[$role->status]['name'] : 'Статус' }}</h6>
                </div>
            </div>

            <div class="col-12 col-md text-end lh-base">
                @anyPermission('users_position_modify|users_position_create')
                    <p class="mb-2">
                        <a href="javascript:" class="save_btn d-block d-md-inline-block btn btn-outline-secondary py-0 mt-3 mt-md-0 disabled">Сохранить</a>
                    </p>
                @endanyPermission

                @if (isset($role))
                    <p class="mb-0 text-muted">
                        @php ($created_at = Date::parse($role->created_at))

                        <small>
                            Добавлена: {{ $created_at->format(now()->year == $created_at->year ? 'j F, H:i' : 'j F Y') }}
                        </small>
                    </p>
                    <p class="mb-0 text-muted">
                        @php ($updated_at = Date::parse($role->updated_at))

                        <small>
                            Обновлено: {{ $updated_at->format( now()->year == $updated_at->year ? 'j F, H:i' : 'j F Y') }}
                        </small>
                    </p>

                    @permission('users_position_delete')
                        <form action="{{ route('positions.destroy', ['position' => $role->id]) }}" method="post" id="delete_position">
                            @method('delete')
                            @csrf

                            <button id="delete" class="btn-link text-decoration-none text-danger text-sm pt-2">Удалить должность</button>
                        </form>
                    @endpermission
                @endif
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 px-md-5 mb-5">
        <div class="col-auto my-4 d-flex align-items-center" id="preloader">
            <div class="spinner-border text-secondary mr-4" role="status" style="width: 3rem; height: 3rem;"></div>
            <strong class="text-muted">Загрузка...</strong>
        </div>

        <div id="formAjax"></div>
    </div>

    <script>
        let positionsView = false;

        $.ajaxSetup({
            beforeSend: function () {
                $('#preloader').removeClass('d-none');
            },
            complete: function() {
                $('#preloader').addClass('d-none');
            },
        });

        $(document).ready(function() {
            $.ajax({
                url: '{{ route('positions/form/get.ajax') }}',
                type: 'GET',
                data: {
                    action: '{{ isset($role) ? 'show' : 'create' }}',
                    {{ isset($role) ? 'id: ' . $role->id : '' }}
                },
                success: function (data) {
                    $('#formAjax').html(data);
                }
            });
        });

        $('body').on('keyup change', '.update_position input, .update_position select', function() {
            $('#position_form input, #position_form select, #permissions').removeClass('is-invalid validation-error alert-danger');

            $('.save_btn').addClass('disabled btn-outline-secondary');

            let fields = {
                'name': validateField($('#name').val().length < 2, $('#name')),
                'slug': validateField($('#slug').val().length < 2, $('#slug')),
                'status': validateField($('#status').val() === null, $('#status')),
                'permissions': validateField($('input[name="permissions[]"]:checked').length <= 0, $('#permissions'), 'validation-error alert-danger'),
            };

            if (checkValidation(fields))
                $('.save_btn').removeClass('disabled btn-outline-secondary').addClass('btn-outline-primary');
        });
    </script>

    @permission('users_positions_view')
    <script>
        positionsView = true;
    </script>
    @endpermission

    @anyPermission('users_position_modify|users_position_create')
    <script>
        $('body').on('click', '.save_btn', function(event) {
            event.preventDefault();

            $.ajax({
                url: $('#position_form').prop('action'),
                type: 'POST',
                data: $('#position_form').serialize(),
                success: function (data) {
                    if (JSON.parse(data).success) {
                        if (positionsView) {
                            window.location.replace('{{ route('positions.index') }}');
                        } else {
                            window.location.reload();
                        }
                    }
                },
                error: function (response) {
                    if (response.responseJSON.errors.slug) {
                        $('#slug').addClass('is-invalid');
                        $('#slug + .invalid-feedback').remove();
                        $('#slug').parent().append(
                            '<div class="invalid-feedback text-sm">' + response.responseJSON.errors.slug[0] + '</div>'
                        );
                    }

                    if (response.responseJSON.errors.permissions) {
                        $('#permissions').addClass('alert-danger validation-error');
                    }
                }
            });
        });
    </script>
    @endanyPermission

    @permission('users_position_delete')
        <script>
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
                        $.ajax({
                            url: $('#delete_position').prop('action'),
                            type: 'POST',
                            data: $('#delete_position').serialize(),
                            success: function () {
                                window.location.replace('{{ route('positions.index') }}');
                            },
                            error: function (response) {
                                Swal.fire({
                                    dangerMode: true,
                                    title: 'Внимание!',
                                    text: response.responseJSON.message,
                                    icon: 'warning',
                                    cancelButtonText: 'Закрыть',
                                    showCancelButton: true,
                                    showConfirmButton: false,
                                });
                            }
                        });
                    }
                });
            });
        </script>
    @endpermission

</x-app-layout>
