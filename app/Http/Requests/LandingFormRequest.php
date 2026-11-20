<?php

namespace App\Http\Requests;

use App\Models\Setting;
use App\Rules\AllowedEmailDomain;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $amount
 * @property $email
 * @property $locale
 * @property $phone
 * @property $terms
 * @property $subscribe
 */
class LandingFormRequest extends FormRequest
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
        if ($this->phone && str_starts_with($this->phone, '+')) {
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
        $rules = [];

        $rules['amount'] = [
            'nullable',
        ];

        $rules['locale'] = [
            'sometimes',
            'string',
            'min:2',
            'max:2',
        ];

        $rules['terms'] = [
            'required',
        ];

        if (config('landing_form.show_optional_subscribe')) {
            $rules['subscribe'] = [
                'nullable',
            ];
        }

        if (config('landing_form.require_phone')) {
            $rules['phone'] = [
                'required',
                'string',
                'regex:/^[0-9]+$/',
            ];

            if (config('landing_form.phone_placeholder') && strlen(config('landing_form.phone_placeholder')) > 0) {
                $length = strlen(config('landing_form.phone_placeholder'));
                $rules['phone'][] = 'digits:' . $length;
            }
        }

        if (config('landing_form.require_email')) {
            $rules['email'] = [
                'required',
                'email',
            ];

            if (Setting::retrieve('email_domains_whitelist_enabled')) {
                $rules['email'][] = new AllowedEmailDomain();
            }
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'terms.required' => __('Please confirm you agree to information rules.'),
        ];
    }
}
