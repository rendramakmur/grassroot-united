<?php

namespace App\Http\Requests\FrontOffice\Credential;

use App\Http\Constant\ApiCode;
use App\Http\Response\General\BasicResponse;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class FrontOfficeRegisterRequest extends FormRequest
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
            'firstName' => ['required', 'max:60'],
            'lastName' => ['required', 'max:60'],
            'email' => ['required', 'email', 'max:50'],
            'password' => ['required', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/'],
            'mobilePrefix' => ['required', 'array'],
            'mobilePrefix.id' => ['required', 'integer'],
            'mobileNumber' => ['required'],
            'occupation' => ['nullable', 'array'],
            'occupation.id' => ['nullable', 'integer'],
            'dateOfBirth' => ['required', 'date', 'before:' . $fourteenYearsAgo],
            'gender' => ['required', 'array'],
            'gender.id' => ['required', 'integer'],
            'city' => ['required', 'array'],
            'city.id' => ['required', 'integer']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        return $this->buildErrorResponse($validator->getMessageBag(), ApiCode::BAD_REQUEST);
    }

    public function messages()
    {
        return [
            'password.regex' => ':attribute must contain at least 1 letter and 1 digit.',
        ];
    }
}
