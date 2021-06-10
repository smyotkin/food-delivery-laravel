<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreUserRequest extends FormRequest
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
            'is_active' => !empty($this->is_active) ? 1 : 0,
        ]);
    }

    /**
     * Есть ли право у пользователя делать этот запрос.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Получить правила валидации, применимые к запросу.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'integer|exists:users,id',
            'city_id' => 'integer',
            'position_id' => 'integer',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|digits:11|unique:users,phone',
            'is_active' => 'boolean'
        ];
    }
}
