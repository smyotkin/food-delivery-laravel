<?php

namespace App\Http\Requests\Cities;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrUpdateCityRequest extends FormRequest
{
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
            'phone' => 'string|max:50',
            'folder' => 'string|max:255',
            'work_hours' => 'json',
            'work_hours_shift' => 'integer|in:-30,-25,-20,-15,-10,-5,0,5,10,15,20,25,30',
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
