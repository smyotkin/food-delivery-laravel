<x-app-layout>
    <x-slot name="title">{{ isset($user) ? $user->full_name : 'Новый пользователь' }}</x-slot>
    <x-slot name="back_href">{{ route('users.index') }}</x-slot>
    <x-slot name="back_title">
        <span class="d-none d-md-inline-block">Пользователи</span>
        <span class="d-inline-block d-md-none">Назад</span>
    </x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">{{ isset($user) ? 'Информация о пользователе' : 'Новый пользователь' }}</h5>
    </x-slot>

    <div class="container-fluid bg-light px-4 px-md-5 py-4 mb-4 border border-start-0 border-end-0 border-secondary">
        <div class="row">
            <div class="col-12 col-md d-flex align-items-center">
                <div class="info">
                    <h4 class="text-muted fw-light">{{ $user->full_name ?? 'Имя Фамилия' }}</h4>
                    <h6 class="text-muted fw-normal mb-0">{{ isset($user->phone) ? $user->phoneNumber($user->phone) : 'Телефон' }}</h6>
                </div>
            </div>

            <div class="col-12 col-md text-end lh-base">
                @if (isset($user))
                    @permission("users_{$user->status}_modify")
                        <p class="mb-2">
                            <a href="javascript:" onclick="" class="d-block d-md-inline-block btn btn-outline-secondary py-1 py-md-0 mt-3 mt-md-0 save_user disabled">Сохранить</a>
                        </p>
                    @endpermission
                @else
                    @anyPermission('users_employee_add|users_specialist_add|users_head_add|users_owner_add')
                        <p class="mb-2">
                            <a href="javascript:" onclick="" class="d-block d-md-inline-block btn btn-outline-secondary py-1 py-md-0 mt-3 mt-md-0 save_user disabled">Сохранить</a>
                        </p>
                    @endanyPermission
                @endif

                @isset($user)
                    <p class="mb-0 text-muted">
                        @php ($created_at = Date::parse($user->created_at))

                        <small>
                            Регистрация: {{ $user->fullname }},
                            {{ $created_at->format(now()->year == $created_at->year ? 'j F, H:i' : 'j F Y') }}
                        </small>
                    </p>
                    <p class="mb-0 text-muted">
                        @php ($updated_at = Date::parse($user->updated_at))

                        <small>
                            Изменения: {{ $user->fullname }},
                            {{ $updated_at->format( now()->year == $updated_at->year ? 'j F, H:i' : 'j F Y') }}
                        </small>
                    </p>

                    @if ((isset($role_status) && (Auth::user()->id != $user->id && !$user::isRoot($user->id))) || ($user::isRoot() && Auth::user()->id != $user->id))
                        @permission("users_{$role_status}_delete")
                            <form action="{{ route('users.destroy', ['user' => $user->id]) }}" id="delete_user" method="post">
                                @method('delete')
                                @csrf

                                <button id="delete" class="text-danger text-sm pt-2">Удалить пользователя</button>
                            </form>
                        @endpermission
                    @endif
                @endisset
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
        $(document).ready(function() {
            $.ajax({
                url: '{{ route('users/form/get.ajax') }}',
                type: 'GET',
                data: {
                    action: '{{ isset($user) ? 'show' : 'create' }}',
                    {{ isset($user) ? 'id: ' . $user->id : '' }}
                },
                beforeSend: function () {
                    $('#preloader').removeClass('d-none');
                },
                complete: function() {
                    $('#preloader').addClass('d-none');
                },
                success: function (data) {
                    $('#formAjax').html(data);
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

            $('body').on('click', '.save_user', function(event) {
                event.preventDefault();

                $.ajax({
                    url: $('#user_form').prop('action'),
                    type: 'POST',
                    data: $('#user_form').serialize(),
                    beforeSend: function () {
                        $('#preloader').removeClass('d-none');
                    },
                    complete: function() {
                        $('#preloader').addClass('d-none');
                    },
                    success: function () {
                        window.location.replace('/users');
                    },
                    error: function (response) {
                        if (typeof(response.responseJSON.errors) != 'undefined') {
                            $('#phone').addClass('is-invalid');
                            $('#phone + .invalid-feedback').remove();
                            $('#phone').parent().append(
                                '<div class="invalid-feedback text-sm">' + response.responseJSON.errors.phone[0] + '</div>'
                            );
                        }
                    }
                });
            });

            $('body').on('change', '#status', function() {
                $.ajax({
                    type: 'GET',
                    data: {
                        status: $(this).val(),
                    },
                    url: '{{ route('positions/select/get.ajax') }}',
                    success: function (data) {
                        $('#permissions').html('');
                        $('#position').attr('disabled', data.length <= 0).html(data);

                        checkFormValidation();
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
            });

            $('body').on('change', '#position', function() {
                getPermissionsCheckedAjax($(this), $("#is_custom_permissions"));
            });

            $('body').on('change', '#is_custom_permissions', function() {
                if ($('#position').val() > 0) {
                    getPermissionsCheckedAjax($('#position'), $(this));
                }
            });

            function getPermissionsCheckedAjax($position, $is_custom_permissions) {
                $.ajax({
                    type: 'GET',
                    data: {
                        id: $position.val(),
                        is_custom_permissions: $is_custom_permissions.prop('checked'),
                        {{ isset($user) ? "user_id: {$user->id}," : '' }}
                    },
                    url: '{{ route('users/permissions/get.ajax') }}',
                    success: function (data) {
                        $('#permissions').html(data);
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
            }
        });

        $('body').on('keyup change', '.update_user input, .update_user select', function() {
            checkFormValidation();
        });

        function checkFormValidation() {
            $('#user_form input, #user_form select, #permissions').removeClass('is-invalid validation-error alert-danger');

            $('.save_user').addClass('disabled btn-outline-secondary');

            let phoneValidation = new RegExp(/\+7\s\d{3}\s\d{3}\-\d{2}\-\d{2}/gm);
            let fields = {
                'first_name': validateField($('#first_name').val().length < 2, $('#first_name')),
                'last_name': validateField($('#last_name').val().length < 2, $('#last_name')),
                'phone': validateField(!phoneValidation.test($('#phone').val()), $('#phone')),
                'status': validateField($('#status').val() === null, $('#status')),
                'position': validateField($('#position').val() === null, $('#position')),
            };

            if (checkValidation(fields))
                $('.save_user').removeClass('disabled btn-outline-secondary').addClass('btn-outline-primary');
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
                    $('#delete_user').submit();
                }
            });
        });
    </script>
</x-app-layout>
