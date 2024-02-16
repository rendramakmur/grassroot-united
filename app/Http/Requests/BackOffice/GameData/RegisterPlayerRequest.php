<?php

namespace App\Http\Requests\BackOffice\GameData;

use App\Http\Constant\ApiCode;
use App\Http\Response\General\BasicResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RegisterPlayerRequest extends FormRequest
{
    use BasicResponse;

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
            'isOutfield' => ['required', 'boolean']
        ];
    }

    protected function failedValidation(Validator $validator) {
        return $this->buildErrorResponse($validator->getMessageBag(), ApiCode::BAD_REQUEST);
    }
}
