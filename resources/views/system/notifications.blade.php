<x-app-layout>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Настройки</h5>
    </x-slot>

    @include('layouts.settings-navigation')

    <div class="container-fluid px-5 mb-5">
        <div class="row">
            <div class="col-5 mt-4">
                <input type="text" id="name_value-search" class="form-control rounded-0" placeholder="Поиск по названию или значению" aria-label="Поиск по названию или значению">
            </div>
            <div class="col-auto mt-4 d-flex align-items-center">
                <div class="spinner-border text-secondary d-none" role="status" id="preloader">
                    <span class="sr-only">Загрузка...</span>
                </div>
            </div>
        </div>
        <div class="row mt-5 mb-3">
            <div class="col-auto lh-1">
                <h5 class="d-inline-block fw-normal align-middle m-0">
                    Уведомления
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="col-10">
                <div class="list-group list-group-flush" id="ajax_block"></div>
            </div>
        </div>
    </div>

    <script>
        let search = $('#name_value-search');
        let ajaxBlock = $('#ajax_block');
        let getSearchCookie = getCookie('notifications_query_str');
        let page = 1;
        let searchNow = false;

        $(document).ready(function() {
            if (getSearchCookie)
                search.val(getSearchCookie);

            showList();

            search.on('keyup', function () {
                document.cookie = 'settings_query_str=' + encodeURIComponent($(this).val());
                if ($(this).val().length >= 0) {
                    searchNow = true;
                    showList();
                } else {
                    searchNow = false;
                }
            });

        });

        function showList(page = 1) {
            $.ajax({
                type: 'GET',
                data: {
                    query: search.val(),
                },
                url: '{{ route('system/notifications/get.ajax') }}?page=' + page,
                beforeSend: function () {
                    $('#preloader').removeClass('d-none');
                },
                complete: function() {
                    $('#preloader').addClass('d-none');
                },
                success: function (data) {
                    $('.table_pagination', ajaxBlock).remove();

                    if (searchNow || page == 1) {
                        ajaxBlock.html(data);
                    } else {
                        ajaxBlock.append(data);
                    }
                }
            });
        }

        $('body').on('click', '.table_pagination a', function(event) {
            event.preventDefault();

            searchNow = false;
            page = $(this).attr('href').split('page=')[1];

            showList(page);
        });

        $('body').on('click', '.notification_option', function () {
            $(' + input,  + select, + textarea', this).removeClass('d-none').focus();

            $(this).closest('tr').find('.save-icon').fadeIn();
        });

        $('body').on('click', '.save-icon', function() {
            let closestForm = $(this).closest('tr').find('form');
            let formField = $('.form-control, .form-select', closestForm);
            let formValue = 'Ошибка';

            if (formField.is('input')) {
                formValue = formField.val();
            } else if (formField.is('select')) {
                formValue = $('option:selected', formField).text();
            }

            $.ajax({
                url: closestForm.prop('action'),
                type: 'PUT',
                data: closestForm.serialize(),
                beforeSend: function () {
                    $('#preloader').removeClass('d-none');
                },
                complete: function() {
                    $('#preloader').addClass('d-none');

                    $('+ a.save-icon', closestForm).hide();
                    $('input, select', closestForm).addClass('d-none');
                },
                success: function (data) {
                    if (JSON.parse(data).success) {
                        $('.settings_option', closestForm)
                            .removeClass('btn-outline-dark btn-danger')
                            .addClass('btn-success')
                            .text(formValue)
                            .fadeIn();
                    }
                },
                error: function () {
                    $('.settings_option', closestForm)
                        .removeClass('btn-outline-dark btn-success')
                        .addClass('btn-danger')
                        .text('Ошибка')
                        .fadeIn();
                }
            });
        });

        $('body').on('click', '#clear_cache', function (e) {
            e.preventDefault();

            let closestForm = $(this).closest('.settings_option-block').find('form');
            let thisButton = $(this);

            Swal.fire({
                dangerMode: true,
                title: 'Вы уверены?',
                text: 'Что хотите очистить кэш',
                icon: 'warning',
                confirmButtonText: 'Да, я уверен!',
                cancelButtonText: 'Отмена',
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: closestForm.prop('action'),
                        type: 'POST',
                        data: closestForm.serialize(),
                        success: function (data) {
                            thisButton
                                .removeClass('btn-secondary')
                                .addClass('btn-success')
                                .text('Кэш очищен');
                        }
                    });
                }
            });
        });
    </script>
</x-app-layout>