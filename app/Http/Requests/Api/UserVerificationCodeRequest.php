<?php

namespace App\Http\Requests\Api;

/**
 * @property $phone
 * @property $verification_code
 */
class UserVerificationCodeRequest extends UserPostRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9+]+$/',
            ],
            'verification_code' => [
                'required',
                'string',
                'max:6',
            ],
        ];

        if (config('landing_form.phone_placeholder') && strlen(config('landing_form.phone_placeholder')) > 0) {
            $length = strlen(config('landing_form.phone_placeholder'));
            $rules['phone'][] = 'min:' . $length;
        }

        return $rules;
    }
}
