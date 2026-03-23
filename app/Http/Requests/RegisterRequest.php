<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
{

    use ApiResponseTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'unique:users',
                'regex:/^[a-zA-Z][a-zA-Z0-9_]{2,19}$/',
            ],
            'email'    => 'required|email|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).{8,}$/',
            ],
            'fullname' => 'nullable|string|min:3|max:50',
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'country'  => 'nullable|string|min:2|max:50',
            'phone'    => 'nullable|string|min:10|max:15',
            'bio'      => 'nullable|string|min:10|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username is required',
            'username.min'      => 'Username must be at least 3 characters',
            'username.max'      => 'Username cannot exceed 20 characters',
            'username.unique'   => 'Username already taken',
            'username.regex'    => 'Username must start with a letter and contain only letters, numbers, or underscores',
            'email.required'    => 'Email is required',
            'email.email'       => 'Invalid email format',
            'email.unique'      => 'Email already registered',
            'password.required' => 'Password is required',
            'password.min'      => 'Password must be at least 8 characters',
            'password.confirmed'=> 'Passwords do not match',
            'password.regex'    => 'Password must include a letter, number and symbol',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->validationError($validator->errors()));
    }
}
