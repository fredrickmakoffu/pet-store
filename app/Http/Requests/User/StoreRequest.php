<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|min:3|max:30',
            'last_name' => 'required|string|min:3|max:30',
            'phone_number' => 'required|string|max:30',
            'email' => 'required|email|unique:users',
            'is_marketing' => 'sometimes|boolean',
            'is_admin' => 'sometimes|boolean',
            'password' => 'required|min:8|max:30',
            'password_confirmation' => 'required|same:password'
        ];
    }
}
