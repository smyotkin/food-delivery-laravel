<x-app-layout>
    <x-slot name="title">Уведомления</x-slot>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Настройки</h5>
    </x-slot>

    @include('layouts.settings-navigation')

    <div class="container-fluid px-4 px-md-5 mb-5">
        @include('components.header-search-preloader', ['id' => 'notifications-search', 'placeholder' => 'Поиск по названию или значению'])

        <div class="row mb-3 mt-md-5">
            <div class="col-auto lh-1">
                <h5 class="d-inline-block fw-normal align-middle m-0">
                    Уведомления
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-10">
                <div class="list-group list-group-flush" id="notifications_ajax"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            updateTableList('notifications', '{{ route('system/notifications/get.ajax') }}');
        });

        $('body').on('click', '.notification_option', function () {
            $(' + input,  + select, + textarea', this).removeClass('d-none').focus();

            $(this).closest('tr').find('.notification_option')
                .removeClass('btn-success btn-danger')
                .addClass('border text-dark border-dark');

            $(this).closest('tr').find('.save-icon').fadeIn();
        });

        $('body').on('click', '.save-icon', function() {
            let closestForm = $(this).closest('tr').find('form');
            let formField = $('.form-control, .form-select', closestForm);
            let option = $('.notification_option', closestForm);

            $.ajax({
                url: closestForm.prop('action'),
                type: 'PUT',
                data: closestForm.serialize(),
                beforeSend: function () {
                    $('#preloader').removeClass('d-none');
                },
                complete: function() {
                    $('#preloader').addClass('d-none');

                    $('a.save-icon', closestForm).hide();
                    formField.addClass('d-none');
                },
                success: function () {
                    option
                        .removeClass('border text-dark border-dark')
                        .addClass('btn-success')
                        .fadeIn();

                    formField.each(function() {
                        $(this).closest('div').find('.notification_option').text($(this).val() ? $(this).val() : 'Не задано');
                    });
                },
                error: function () {
                    option
                        .removeClass('border text-dark border-dark btn-success')
                        .addClass('btn-danger')
                        .text('Ошибка')
                        .fadeIn();
                }
            });
        });
    </script>
</x-app-layout>
