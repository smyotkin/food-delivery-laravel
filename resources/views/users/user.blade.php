<x-app-layout>
    <x-slot name="back_href">{{ route('users.index') }}</x-slot>
    <x-slot name="back_title">Пользователи</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Информация о пользователе</h5>
    </x-slot>

    <div class="container-fluid bg-light px-5 py-4 mb-4 border border-start-0 border-end-0 border-secondary">
        <div class="row">
            <div class="col d-flex align-items-center">
                <div class="info">
                    <h4 class="text-muted fw-light">{{ $user->first_name ?? 'Имя' }} {{ $user->last_name ?? 'Фамилия' }}</h4>
                    <h6 class="text-muted fw-normal mb-0">{{ isset($user->phone) ? $user->phoneNumber($user->phone) : 'Телефон' }}</h6>
                </div>
            </div>
            <div class="col text-end lh-base">
                @if (isset($user))
                    @permission("users_{$user->status}_modify")
                        <p class="mb-2">
                            <a href="javascript:" onclick="" id="save_user" class="btn btn-outline-secondary py-0 disabled">Сохранить</a>
                        </p>
                    @endpermission
                @else
                    @anyPermission('users_employee_add|users_specialist_add|users_head_add|users_owner_add')
                        <p class="mb-2">
                            <a href="javascript:" onclick="" id="save_user" class="btn btn-outline-secondary py-0 disabled">Сохранить</a>
                        </p>
                    @endanyPermission
                @endif

                @isset($user)
                    <p class="mb-0 text-muted">
                        @php ($created_at = Date::parse($user->created_at))

                        <small>
                            Регистрация: {{ $user->first_name }} {{ $user->last_name }},
                            {{ $created_at->format(now()->year == $created_at->year ? 'j F, H:i' : 'j F Y') }}
                        </small>
                    </p>
                    <p class="mb-0 text-muted">
                        @php ($updated_at = Date::parse($user->updated_at))

                        <small>
                            Изменения: {{ $user->first_name }} {{ $user->last_name }},
                            {{ $updated_at->format( now()->year == $updated_at->year ? 'j F, H:i' : 'j F Y') }}
                        </small>
                    </p>

                    @if ((isset($role->status) && (Auth::user()->id != $user->id && !$user::isRoot($user->id))) || ($user::isRoot() && Auth::user()->id != $user->id))
                        @php ($status = empty($role->status) && $user::isRoot() ? 'admin' : $role->status)
                        @permission("users_{$status}_delete")
                            <form action="{{ route('users.destroy', ['user' => $user->id]) }}" id="delete_user" method="post" id="delete_user">
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


    <div class="container-fluid px-5 mb-5">
        <div class="col-auto my-4 d-flex align-items-center" id="preloader">
            <div class="spinner-border text-secondary mr-4" role="status" style="width: 3rem; height: 3rem;"></div>
            <strong class="text-muted">Загрузка...</strong>
        </div>

        <div id="formAjax"></div>
    </div>

    <script>
        $(document).ready(function() {
            $.ajax({
                url: '{{ route('users.getUserFormAjax') }}',
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
                }
            });

            $('body').on('click', '#save_user', function(event) {
                event.preventDefault();

                $.ajax({
                    url: $('#user_form').prop('action'),
                    type: 'POST',
                    // dataType:'json',
                    data: $('#user_form').serialize(),
                    beforeSend: function () {
                        $('#preloader').removeClass('d-none');
                    },
                    complete: function() {
                        $('#preloader').addClass('d-none');
                    },
                    success: function (data) {
                        if (JSON.parse(data).success) {
                            window.location.replace('/users');
                        }
                    },
                    error: function (response) {
                        if (response.responseJSON.errors.phone) {
                            $('#phone').addClass('is-invalid');
                            $('#phone + .invalid-feedback').remove();
                            $('#phone').parent().append('<div class="invalid-feedback text-sm">' + response.responseJSON.errors.phone[0] + '</div>');
                        }
                    }
                });
            });

            $('body').on('keyup change', '.update_user input, .update_user select', function() {
                checkUserForm();
            });

            $('body').on('change', '#status', function() {
                $.ajax({
                    type: 'GET',
                    data: {
                        status: $(this).val(),
                    },
                    url: '{{ route('positions.getAjaxByStatus') }}',
                    success: function (data) {
                        $('#permissions').html('');

                        $('#position').attr('disabled',
                            data.length > 0 ? false : true
                        ).html(data);
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
                    url: '{{ route('users.getPermissionsCheckedAjax') }}',
                    success: function (data) {
                        $('#permissions').html(data);
                    }
                });
            }
        });

        function checkUserForm() {
            let isValid = true;
            $('#user_form input, #user_form select').removeClass('is-invalid');

            $('#save_user').addClass('disabled btn-outline-secondary');

            if ($('#first_name').val().length < 2) {
                $('#first_name').addClass('is-invalid');

                isValid = false;
            }

            if ($('#last_name').val().length < 2) {
                $('#last_name').addClass('is-invalid');

                isValid = false;
            }

            let phoneValidation = new RegExp(/\+7\s\d{3}\s\d{3}\-\d{2}\-\d{2}/gm);

            if (!phoneValidation.test($('#phone').val())) {
                $('#phone').addClass('is-invalid');

                isValid = false;
            }

            if ($('#status').val() === null) {
                $('#status').addClass('is-invalid');

                isValid = false;
            }

            if ($('#position').val() === null) {
                $('#position').addClass('is-invalid');

                isValid = false;
            }

            if (isValid)
                $('#save_user').removeClass('disabled btn-outline-secondary').addClass('btn-outline-primary');
        }

        $('body').on('click', '#delete', function (e) {
            e.preventDefault();

            swal({
                dangerMode: true,
                title: 'Вы уверены?',
                text: 'Данная запись будет удалена',
                icon: 'warning',
                buttons: ['Отмена', 'Да, я уверен!']
            }).then(function(isConfirm) {
                if (isConfirm) {
                    $('#delete_user').submit();
                }
            });
        });
    </script>
</x-app-layout>
