<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $email
 * @property $phone
 */
class UserGetRequest extends FormRequest
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
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:191',
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9+]+$/',
            ],
        ];

        if (config('landing_form.phone_placeholder') && strlen(config('landing_form.phone_placeholder')) > 0) {
            $length = strlen(config('landing_form.phone_placeholder'));
            $rules['phone'][] = 'min:' . $length;
        }

        return $rules;
    }
}
