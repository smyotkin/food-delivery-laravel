<x-app-layout>
    <x-slot name="title">Общие</x-slot>
    <x-slot name="back_href">{{ route('dashboard') }}</x-slot>
    <x-slot name="back_title">Ferone</x-slot>
    <x-slot name="header">
        <h5 class="m-0 fw-bold">Настройки</h5>
    </x-slot>

    @include('layouts.settings-navigation')

    <div class="container-fluid px-5 mb-5">
        @include('components.header-search-preloader', ['id' => 'settings-search', 'placeholder' => 'Поиск по названию или значению'])

        <div class="row mt-md-5 mb-3">
            <div class="col-12 col-md-auto lh-1">
                <h5 class="d-inline-block fw-normal align-middle m-0">
                    Настройки
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-5">
                @if (has_permission('settings_modify'))
                    <div class="list-group list-group-flush">
                        <li class="settings_option-block border-bottom list-group-item d-md-flex justify-content-between align-items-center py-3 px-1">
                            <div class="me-auto">
                                <div class="fw-bold">Файловый кеш</div>
                            </div>

                            <form class="d-none" method="post" action="{{ route('settings/clear.cache') }}">
                                @csrf
                            </form>

                            <a href="javascript:" class="d-block d-md-inline-block mt-2 mt-md-0 text-decoration-none btn btn-secondary btn-sm rounded-pill px-3" id="clear_cache">Очистить кэш</a>
                        </li>
                    </div>
                @endif

                <div class="list-group list-group-flush" id="settings_ajax"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            updateTableList('settings', '{{ route('settings/get.ajax') }}');
        });

        $('body').on('click', '.settings_option', function () {
            $(' + input,  + select', this).removeClass('d-none').focus();

            $(this).closest('.settings_option-block').find('.save-icon').fadeIn();
        });

        $('body').on('click', '.save-icon', function() {
            let closestForm = $(this).closest('.settings_option-block').find('form');
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
                        success: function () {
                            thisButton
                                .removeClass('btn-secondary btn-danger')
                                .addClass('btn-success')
                                .text('Кэш очищен');
                        },
                        error: function () {
                            thisButton
                                .removeClass('btn-secondary btn-success')
                                .addClass('btn-danger')
                                .text('Ошибка');
                        }
                    });
                }
            });
        });
    </script>
</x-app-layout>
