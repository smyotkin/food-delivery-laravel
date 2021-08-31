<x-app-layout>
    <x-slot name="title">Профиль</x-slot>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Назад</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Профиль пользователя</h5>
    </x-slot>

    <div class="container-fluid bg-light px-5 py-4 py-md-5 mb-4 border border-start-0 border-end-0 border-secondary">
        <div class="row px-md-5">
            <div class="col-12 col-md d-flex align-items-center">
                <div class="info">
                    <h4>{{ $user->first_name }} {{ $user->last_name }}</h4>
                    <h6 class="text-muted fw-normal mb-0">{{ $user->phoneNumber($user->phone) }}</h6>
                </div>
            </div>

            <div class="col-12 col-md mt-3 mt-md-0">
                <p class="fw-bold mb-0">Должность</p>
                <p class="mb-0">{{ $role->name ?? '---' }}</p>
            </div>

            <div class="col-12 col-md mt-3 mt-md-0">
                <p class="fw-bold mb-0">Регистрация</p>
                <p class="mb-0">
                    @php ($created_at = Date::parse($user->created_at))
                    {{ $created_at->format(now()->year == $created_at->year ? 'j F' : 'j F Y') }}
                </p>
            </div>

            <div class="col-12 col-md mt-3 mt-md-0">
                <p class="fw-bold mb-0">Часовой пояс</p>
                <button type="button" class="btn-link text-decoration-none" data-bs-toggle="modal" data-bs-target="#timezoneModal" id="timezone-link">{{ $timezones[$user->timezone] }}</button>

                <div class="modal fade" id="timezoneModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('profile.update', ['profile' => $user]) }}" class="modal-content" id="change_timezone" method="post">
                            @method('patch')
                            @csrf

                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel"><strong>Изменить часовой пояс</strong></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <div class="list-group">
                                    @foreach($timezones as $timezone => $title)
                                        <label class="list-group-item list-group-item-action">
                                            <input class="form-check-input me-1 timezone" name="timezone" type="radio" value="{{ $timezone }}" {{ !empty($user->timezone) && $user->timezone == $timezone ? 'checked' : '' }} required>
                                            {{ $title }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отменить</button>
                                <button type="submit" class="btn btn-primary"  id="change_timezone-btn">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-5 mb-5">
        <div class="col-auto my-4 px-5 d-flex align-items-center" id="preloader">
            <div class="spinner-border text-secondary mr-4" role="status" style="width: 3rem; height: 3rem;"></div>
            <strong class="text-muted">Загрузка...</strong>
        </div>

        <div id="formAjax"></div>
    </div>

    <script>
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
                url: '{{ route('profile.getAjax') }}',
                type: 'GET',
                data: {
                    {{ isset($user) ? 'id: ' . $user->id : '' }}
                },
                success: function (data) {
                    $('#formAjax').html(data);
                }
            });
        });

        $('body').on('click', '#change_password', function (e) {
            $(this).hide();
            $('.change_password-form').removeClass('d-none');
        });

        $('body').on('click', '#change_timezone-btn', function (e) {
            e.preventDefault();

            let checkedTitle = $('#change_timezone .timezone:checked').parent().text().trim();

            $.ajax({
                url: $('#change_timezone').prop('action'),
                type: 'POST',
                data: $('#change_timezone').serialize(),
                success: function (data) {
                    if (JSON.parse(data).success) {
                        $('#timezoneModal').modal('hide');

                        $('#timezone-link').addClass('text-success').prop('disabled', true).removeAttr("data-bs-toggle").text(checkedTitle);

                        $('#change_timezone-btn').remove();
                        $("#change_timezone .modal-footer button[data-bs-dismiss='modal']").text('Закрыть');
                    }
                }
            });
        });

        $('body').on('keyup change', '.change_password-form input', function() {
            $('.change_password-form input').removeClass('is-invalid');
            $('#change_password-submit').addClass('disabled');

            let fields = {
                'password': validateField($('#password').val().length < 6, $('#password')),
            };

            if (checkValidation(fields))
                $('#change_password-submit').removeClass('disabled');
        });

        $('body').on('click', '#change_password-submit', function (e) {
            e.preventDefault();

            Swal.fire({
                dangerMode: true,
                title: 'Вы уверены?',
                text: 'Что хотите изменить пароль',
                icon: 'warning',
                confirmButtonText: 'Да, я уверен!',
                cancelButtonText: 'Отмена',
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: $('.change_password-form').prop('action'),
                        type: 'POST',
                        data: $('.change_password-form').serialize(),
                        success: function (data) {
                            if (JSON.parse(data).success) {
                                $('#password').addClass('is-valid').parent().find('.form-text-info').addClass('text-success').text('Пароль успешно изменен!');
                                $('#password').closest('form').find('fieldset').prop('disabled', true);
                                $('#change_password-submit').remove();
                            }
                        },
                        error: function (response) {
                            console.log(response.responseJSON.errors.password);

                            if (response.responseJSON.errors.password) {
                                $('#password').addClass('is-invalid');
                                $('#password + .invalid-feedback').text(response.responseJSON.errors.password[0]);
                            }
                        }
                    });
                }
            });
        });

        $('body').on('click', '.logout-link', function (e) {
            e.preventDefault();
            let thisButton = $(this);

            Swal.fire({
                dangerMode: true,
                title: 'Вы уверены?',
                text: 'Что хотите выйти',
                icon: 'warning',
                confirmButtonText: 'Да, я уверен!',
                cancelButtonText: 'Отмена',
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    thisButton.closest('form').submit();
                }
            });
        });
    </script>
</x-app-layout>
