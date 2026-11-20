<?php

namespace App\Http\Requests\Api;

use App\Rules\ValidBooleanString;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $amount
 * @property $email
 * @property $name
 * @property $phone
 * @property $term
 * @property $app_instance_id
 * @property $fcm_token
 * @property $accept_offers
 */
class UserPostRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        $this->formatPhoneIfProvided();
    }

    protected function formatPhoneIfProvided(): void
    {
        if ($this->phone) {
            $this->merge([
                'phone' => strip_phone_number_formatting($this->phone),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'amount' => [
                'nullable',
            ],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:191',
            ],
            'name' => [
                'nullable',
                'max:191',
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9+]+$/',
            ],
            'term' => [
                'nullable',
            ],
            'app_instance_id' => [
                'nullable',
                'string',
                'max:255',
            ],
            'fcm_token' => [
                'nullable',
                'string',
                'max:255',
            ],
            'random_string' => [
                'sometimes',
                'string',
                'min:12',
                'max:191',
            ],
            'random_email' => [
                'sometimes',
                'string',
                'email',
                'min:12',
                'max:25',
            ],
            'accept_offers' => [
                'sometimes',
                new ValidBooleanString(),
            ],
        ];

        if (config('landing_form.phone_placeholder') && strlen(config('landing_form.phone_placeholder')) > 0) {
            $length = strlen(config('landing_form.phone_placeholder'));
            $rules['phone'][] = 'min:' . $length;
        }

        return $rules;
    }
}
