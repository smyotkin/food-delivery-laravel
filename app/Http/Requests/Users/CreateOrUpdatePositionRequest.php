<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrUpdatePositionRequest extends FormRequest
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
            'id' => 'required|integer|exists:roles,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'status' => 'required|in:owner,head,specialist,employee',
            'permissions' => 'required|array|exists:permissions,slug',
        ]);

        switch ($this->method()) {
            case 'POST': {
                return $rules->merge([
                    'id' => 'integer|exists:roles,id',
                    'slug' => 'required|string|max:255|unique:roles,slug',
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
