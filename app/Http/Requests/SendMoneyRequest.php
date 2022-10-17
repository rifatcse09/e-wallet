<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\{JsonResponse};
use App\Traits\RespondsWithHttpStatusTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class SendMoneyRequest extends FormRequest
{

    use RespondsWithHttpStatusTrait;

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
    public function rules(): array
    {
        return [
            'sender_id' => 'required|integer|exists:App\Models\User,id',
            'receiver_id' => 'required|integer|exists:App\Models\User,id|different:sender_id',
            'amount' => 'required|regex:/^\d+(\.\d{1,2})?$/|gt:0',
        ];
    }

    public function failedValidation(Validator $validator): JsonResponse

    {
        throw new HttpResponseException($this::failure($validator->errors(), ['error' => __('Validation errors')]));
    }



    public function messages(): array

    {

        return [
            'sender_id.required' => __('messages.sender_id.required'),
            'sender_id.integer' => __('messages.sender_id.integer'),
            'receiver_id.required' => __('messages.receiver_id.required'),
            'receiver_id.integer' => __('messages.receiver_id.integer'),
            'amount.required' => __('messages.amount.required')
        ];
    }
}
