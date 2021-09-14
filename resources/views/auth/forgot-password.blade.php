<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/" class="text-decoration-none text-dark">
                <h2 class="fw-bold">Ferone</h2>
            </a>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        @if (request()->failed)
            <div class="alert alert-danger text-sm" role="alert">
                Произошла ошибка, попробуйте еще раз
            </div>
        @endif

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="post" action="{{ isset($phone) ? route('password.store', ['phone' => $phone]) : route('password.request') }}" id="setNewPassword">
            @method(isset($phone) ? 'put' : 'get')
            @csrf

            <div>
                <x-label for="phone" :value="__('Phone')" />

                <input type="text" id="phone" name="phone" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full ru-phone_format {{ isset($phone) ? 'disabled:opacity-50' : '' }}" value="{{ old('phone') ?? $phone ?? '' }}" placeholder="Мобильный телефон" required autofocus {{ isset($phone) ? 'readonly' : '' }}>
            </div>

            @if (isset($phone) && ($attempts > 0 || $last_active_entry))
                <div class="mt-3">
                    <label for="pin" class="block font-medium text-sm text-gray-700">
                        {{ __('Pin') }}
                    </label>

                    <div class="row mt-1">
                        <div class="col">
                            <input type="text" id="pin" name="pin" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full pin_format {{ !$last_active_entry || $pin_attempts == 0 ? 'disabled:opacity-50' : '' }}" value="{{ old('pin') ?? '' }}" placeholder="Пин-код из СМС" required autofocus {{ !$last_active_entry || $pin_attempts == 0 ? 'disabled' : '' }}>
                        </div>
                        <div class="col text-center">
                            <button class="btn {{ $last_active_entry ? 'btn-outline-secondary disabled' : 'btn-primary' }} w-100 h-100" id="send_sms">
                                <span id="timer">
                                    {{ $pin_activity_time != '00:00' ? $pin_activity_time : 'Отправить код' }}
                                </span>
                            </button>
                        </div>
                    </div>

                    @if ($last_active_entry)
                        <div class="row">
                            <div class="col">
                                <div class="alert alert-secondary text-xs mt-3 mb-0 py-2" role="alert">
                                    @if (isset($pin_attempts) && $pin_attempts == 0)
                                        У вас закончились попытки ввода данного ключа, дождитесь окончания таймера и попробуйте еще раз.
                                    @else
                                        Вам было отправлено СМС-сообщение на номер {{ $phone }}. <br>
                                        Код активен до {{ Date::parse($pin_ended)->format('H:i') }} (по МСК). <br>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($pin_attempts > 0)
                    <div class="mt-3 {{ !$last_active_entry ? 'd-none' : '' }}">
                        <x-label for="newPassword" :value="__('New Password')" />

                        <input type="password" id="newPassword" name="new_password" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block mt-1 w-full" value="" placeholder="Новый пароль" required autofocus>
                    </div>
                @endif
            @endif

            @if ((isset($attempts) && $attempts == 0) && $last_active_entry == false)
                <div class="row">
                    <div class="col">
                        <div class="alert alert-secondary text-sm mt-4 mb-0 py-1 text-center" role="alert">
                            У Вас больше не осталось попыток восстановить пароль сегодня, попробуйте завтра.
                        </div>
                    </div>
                </div>
            @endif

            @if (isset($pin_attempts) && $pin_attempts > 0 && ((!empty($pin_created) && isset($attempts) && $attempts > 0) || $last_active_entry) || !isset($phone))
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-dark {{ !empty($pin_created) ? 'btn-outline-dark disabled' : '' }}" id="setNewPassword-submit">Восстановить пароль</button>
                </div>
            @endif
        </form>

        @if (isset($phone))
            <form action="{{ route('password.pin', ['phone' => $phone]) }}" id="send_sms_form" method="post">
                @csrf

                <input type="hidden" name="phone" value="{{ $phone }}">
            </form>
        @endif

        <script>
            @if (!empty($pin_ended))
                let endTime = {{ Date::parse($pin_ended)->timestamp }} * 1000;
                CountDownTimer(new Date(endTime), $('#timer'));
            @endif

            $(document).ready(function () {
                $.ajax({
                    url: $('#send_sms_form').prop('action'),
                    type: 'POST',
                    data: $('#send_sms_form').serialize(),
                    success: function () {
                        window.location.reload(true);
                    },
                });

                $('.pin_format').mask("9999", {autoclear: false});

                setCursorToActive('.pin_format');
            });

            $('body').on('click', '#send_sms', function(event) {
                event.preventDefault();

                $.ajax({
                    url: $('#send_sms_form').prop('action'),
                    type: 'POST',
                    data: $('#send_sms_form').serialize(),
                    success: function () {
                        window.location.reload(true);
                    },
                });
            });

            @if (isset($phone))
                $('body').on('keyup change', '#setNewPassword input', function() {
                    $('#setNewPassword-submit').addClass('btn-outline-dark disabled');

                    let pinValidation = new RegExp(/\d{4}/gm);

                    let fields = {
                        'pin': validateField(!pinValidation.test($('#pin').val()), $('#pin')),
                        'newPassword': validateField($('#newPassword').val().length < 6, $('#newPassword')),
                    };

                    if (checkValidation(fields))
                        $('#setNewPassword-submit').removeClass('btn-outline-dark disabled');
                });
            @endif

            function CountDownTimer(dt, element)
            {
                let end = new Date(dt);

                let _second = 1000;
                let _minute = _second * 60;
                let _hour = _minute * 60;
                let timer;

                element
                    .parent()
                    .addClass('btn-outline-secondary disabled')
                    .removeClass('btn-primary')
                    .attr('disabled', true);

                function showRemaining() {
                    let now = new Date();
                    let distance = end - now;

                    if (distance < 0) {
                        clearInterval(timer);

                        return;
                    }

                    let minutes = Math.floor((distance % _hour) / _minute);
                    let seconds = Math.floor((distance % _minute) / _second);
                    let min = minutes < 10 ? '0' + minutes : minutes;
                    let sec = seconds < 10 ? '0' + seconds : seconds;

                    element.text(min + ':' + sec);

                    if ((minutes == 0 && seconds == 0) || min + ':' + sec === '00:00') {
                        setTimeout(function() {
                            window.location.replace('{{ route('password.request') }}');
                        }, 3000);
                    }
                }

                timer = setInterval(showRemaining, 1000);
            }
        </script>
    </x-auth-card>
</x-guest-layout>
