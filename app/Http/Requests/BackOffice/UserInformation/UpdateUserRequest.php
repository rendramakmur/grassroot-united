<?php

namespace App\Http\Requests\BackOffice\UserInformation;

use App\Http\Constant\ApiCode;
use App\Http\Response\General\BasicResponse;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $fourteenYearsAgo = Carbon::now()->subYears(14)->format('Y-m-d');

        return [
            'userType' => ['required', 'array'],
            'userType.id' => ['required', 'integer'],
            'firstName' => ['required', 'max:60'],
            'lastName' => ['required', 'max:60'],
            'email' => ['required', 'email', 'max:50'],
            'mobilePrefix' => ['required', 'array'],
            'mobilePrefix.id' => ['required', 'integer'],
            'mobileNumber' => ['required'],
            'occupation' => ['nullable', 'array'],
            'occupation.id' => ['nullable', 'integer'],
            'dateOfBirth' => ['required', 'date', 'before:' . $fourteenYearsAgo],
            'gender' => ['required', 'array'],
            'gender.id' => ['required', 'integer'],
            'photoProfile' => ['nullable', 'url:http,https'],
            'address' => ['required', 'max:300'],
            'city' => ['required', 'array'],
            'city.id' => ['required', 'integer'],
            'bodySize' => ['nullable', 'array'],
            'bodySize.id' => ['nullable', 'integer'],
            'emailStatus' => ['required', 'boolean'],
            'verifiedAt' => ['nullable', 'date']
        ];
    }

    protected function failedValidation(Validator $validator) {
        return $this->buildErrorResponse($validator->getMessageBag(), ApiCode::BAD_REQUEST);
    }
}
