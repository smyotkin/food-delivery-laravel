<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class CreateOrUpdateUserRequest extends FormRequest
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
        $rules = collect([
            'id' => 'required|integer|exists:users,id',
            'city_id' => 'integer',
            'position_id' => 'required|integer',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'phone' => 'required|digits:11',
            'status' => 'required|in:owner,head,specialist,employee',
            'permissions' => 'array|exists:permissions,slug',
        ]);

        switch ($this->method()) {
            case 'POST': {
                return $rules->merge([
                    'id' => 'integer|exists:users,id',
                    'phone' => 'required|digits:11|unique:users,phone',
                ])->all();
            }
            case 'PUT':
            case 'PATCH': {
                return $rules->all();
            }
            default:
                return $rules->all();
        }
    }
}
