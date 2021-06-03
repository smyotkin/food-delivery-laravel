<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <h2 class="fw-bold">Ferone</h2>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Phone -->
            <div>
                <x-label for="phone" :value="__('Телефон')" />

                <x-input id="phone" class="block mt-1 w-full ru-phone_format" type="text" name="phone" placeholder="Мобильный телефон" :value="old('phone')" required autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Пароль')" />

                <x-input id="password" class="block mt-1 w-full" type="password" name="password" placeholder="Пароль" required autocomplete="current-password" />
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                        {{ __('Забыли пароль?') }}
                    </a>
                @endif

                <x-button class="ml-3">
                    {{ __('Войти') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
