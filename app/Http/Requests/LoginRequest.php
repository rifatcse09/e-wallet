<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

use Illuminate\Http\{JsonResponse};

use App\Traits\RespondsWithHttpStatusTrait;


class LoginRequest extends FormRequest

{
    use RespondsWithHttpStatusTrait;

    public function rules(): array

    {

        return [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ];
    }



    public function failedValidation(Validator $validator): JsonResponse

    {
        throw new HttpResponseException($this::failure($validator->errors(), ['error' => __('Validation errors')]));
    }



    public function messages(): array

    {

        return [
            'email.required' => __('messages.email.required'),
            'password.required' => __('messages.password.required')
        ];
    }
}
