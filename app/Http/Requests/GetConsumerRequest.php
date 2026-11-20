<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $amount
 * @property $email
 * @property $locale
 * @property $phone
 * @property $terms
 * @property $subscribe
 */
class GetConsumerRequest extends FormRequest
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
        $rules['phone'] = [
            'required',
            'string',
            'regex:/^[0-9]+$/',
        ];

        $rules['subscribe'] = [
            'nullable',
            'boolean'
        ];

        $rules['agree_to_terms'] = [
            'required',
            'boolean'
        ];

        if (config('landing_form.phone_placeholder') && strlen(config('landing_form.phone_placeholder')) > 0) {
            $length = strlen(config('landing_form.phone_placeholder'));
            $rules['phone'][] = 'digits:' . $length;
        }

        return $rules;
    }
}
