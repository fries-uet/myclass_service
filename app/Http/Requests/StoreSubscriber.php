<?php

namespace App\Http\Requests;

class StoreSubscriber extends Request
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
        return [
            'msv' => 'required|max:8|min:8',
            'email' => 'required|email|max:255|unique:s_users',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.unique' => 'Email đã được sử dụng.',
        ];
    }
}
