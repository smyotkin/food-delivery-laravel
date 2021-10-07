<?php

namespace App\Http\Requests\Cities;

use App\Services\UsersService;
use Illuminate\Foundation\Http\FormRequest;

class CreateOrUpdateCityRequest extends FormRequest
{
    /**
     * Обработка перед валидацией
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'multicode' => !empty($this->multicode) ? 1 : 0,
            'is_active' => !empty($this->is_active) ? 1 : 0,
            'work_hours' => !empty($this->work_hours) && is_array($this->work_hours) ? json_encode($this->work_hours) : null,
            'folder' => '/' . strtolower($this->folder),
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
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
        $rules = collect([
            'id' => 'required|integer|exists:cities,id',
            'name' => 'required|string|min:2|max:255',
            'phone' => 'required|string|max:50',
            'folder' => 'string|max:255|unique:cities,folder,' . $this->folder,
            'multicode' => 'boolean',
            'is_active' => 'boolean',
            'timezone' => 'required|string|in:' . implode(',', array_keys(UsersService::timezones)),
            'work_hours' => 'required|json',
            'work_hours_shift' => 'required|integer|in:-30,-25,-20,-15,-10,-5,0,5,10,15,20,25,30',
            'kladr_cities' => 'json',
        ]);

        switch ($this->method()) {
            case 'POST': {
                return $rules->merge([
                    'id' => 'integer',
                ])->all();
            }
            case 'PUT':
            case 'PATCH':
            default:
                return $rules->all();
        }
    }
}
