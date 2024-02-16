<?php

namespace App\Http\Requests\BackOffice\GameData;

use App\Http\Constant\ApiCode;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class DeletePaidPlayerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'userNumber' => ['required'],
        ];
    }

    protected function failedValidation(Validator $validator) {
        return $this->buildErrorResponse($validator->getMessageBag(), ApiCode::BAD_REQUEST);
    }
}
