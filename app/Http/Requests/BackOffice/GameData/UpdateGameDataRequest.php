<?php

namespace App\Http\Requests\BackOffice\GameData;

use App\Http\Constant\ApiCode;
use App\Http\Response\General\BasicResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGameDataRequest extends FormRequest
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
            'venueName' => ['required', 'max:60'],
            'venueAddress' => ['required', 'max:300'],
            'mapUrl' => ['required', 'url:http,https'],
            'gameDate' => ['required', 'date', 'after_or_equal:today'],
            'duration' => ['required', 'integer'],
            'goalkeeperQuota' => ['required', 'integer'],
            'outfieldQuota' => ['required', 'integer'],
            'goalkeeperPrice' => ['required', 'numeric'],
            'outfieldPrice' => ['required', 'numeric'],
            'notes' => ['nullable', 'max:300'],
            'status' => ['required', 'array'],
            'status.id' => ['required', 'integer']
        ];
    }

    protected function failedValidation(Validator $validator) {
        return $this->buildErrorResponse($validator->getMessageBag(), ApiCode::BAD_REQUEST);
    }
}
