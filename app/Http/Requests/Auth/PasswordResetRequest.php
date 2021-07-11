<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use App\Models\User;

class PasswordResetRequest extends FormRequest
{
    /**
     * Обработка перед валидацией
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => User::toDigit($this->phone),
        ]);
    }

    /**
     * Есть ли право у пользователя делать этот запрос.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch (Route::currentRouteName()) {
            case 'password.request': {
                return [
                    'phone' => 'exists:users,phone',
                ];
            }
            case 'password.phone':
            case 'password.pin': {
                return [
                    'phone' => 'required|exists:users,phone',
                    'pin_attempts' => 'digits:1',
                ];
            }
            case 'password.store': {
                return [
                    'phone' => 'required|exists:users,phone',
                    'new_password' => 'required|min:6',
                ];
            }
            default:
                return [
                    'phone' => 'required|exists:users,phone',
                ];
        }
    }
}
