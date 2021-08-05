<div class="row">
    <div class="col">
        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />
    </div>
</div>

<div class="row">
    <form method="post" action="{{ isset($user) ? route('users.update', ['user' => $user]) : route('users.store') }}" id="user_form" class="col-10 update_user needs-validation" novalidate>
        @method(isset($user) ? 'patch' : 'post')
        @csrf

        <fieldset class="row g-3" {{ isset($user) && !auth()->user()->hasPermission("users_{$user->status}_modify") ? 'disabled' : '' }}>
            <div class="col-5">
                <div class="row g-3">
                    @isset($user)
                        <input type="hidden" name="id" value="{{ $user->id }}">
                    @endisset

                    <div class="col-6">
                        <label for="first_name" class="form-label fw-bold">Имя</label>
                        <input type="text" class="form-control rounded-0" id="first_name" name="first_name" value="{{ old('first_name') ?? $user->first_name ?? '' }}" placeholder="Имя" required>
                        <div class="invalid-feedback text-sm">
                            Поле обязательное, не менее 2 симв.
                        </div>
                    </div>

                    <div class="col-6">
                        <label for="last_name" class="form-label fw-bold">Фамилия</label>
                        <input type="text" class="form-control rounded-0" id="last_name" name="last_name" value="{{ old('last_name') ?? $user->last_name ?? '' }}" placeholder="Фамилия" required>
                        <div class="invalid-feedback text-sm">
                            Поле обязательное, не менее 2 симв.
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="phone" class="form-label fw-bold">Мобильный телефон</label>
                        <input type="text" class="form-control rounded-0 ru-phone_format" id="phone" name="phone" value="{{ old('phone') ?? $user->phone_formatted ?? '' }}" placeholder="+7 555 555-55-55" required>
                    </div>

                    <div class="col-12">
                        @include('users/statuses-select')
                    </div>

                    <div class="col-12">
                        @include('users/positions-select')
                    </div>

                    <div class="col-12">
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ !empty($user->is_active) || !isset($user) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Учетная запись активна</label>
                        </div>
                    </div>

                    @permission('users_modes_modify')
                    <div class="col-12">
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" id="is_custom_permissions" name="is_custom_permissions" {{ !empty($user->is_custom_permissions) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_custom_permissions">Персонализированные права</label>
                        </div>
                    </div>
                    @endpermission
                </div>
            </div>

            <div class="col-7" id="permissions">
                @if (!empty($role) || (isset($user) && $user::isRoot()))
                    @include('users/permissions-table')
                @endif
            </div>
        </fieldset>
    </form>
</div>
