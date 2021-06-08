<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class SaveUserRequest extends FormRequest
{
    protected function prepareForValidation()
    {
        $this->merge(['phone' => User::toDigit($this->phone)]);
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
     * Получить правила валидации, применимые к запросу.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'integer|exists:users,id',
            'city_id' => 'integer',
            'position_id' => 'integer',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|digits:11',
            'is_active' => 'nullable'
        ];
    }
}
