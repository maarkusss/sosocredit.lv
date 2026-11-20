<?php

namespace App\Http\Requests\ApplicantForm;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $amount
// * @property $email
 * @property $locale
 * @property $phone
 * @property $subscribed
 * @property $term_months
 * @property $loantype
 * @property $section_id
 */
class ApplicantFormCreateRequest extends FormRequest
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

        //        $rules['email'] = [
        //            'required',
        //            'nullable',
        //            'email',
        //        ];
        //
        $rules['phone'] = [
            'sometimes',
            'string',
            'regex:/^[0-9]+$/',
        ];
        //
        //        if (config('landing_form.phone_placeholder') && strlen(config('landing_form.phone_placeholder')) > 0) {
        //            $length = strlen(config('landing_form.phone_placeholder'));
        //            $rules['phone'][] = 'digits:' . $length;
        //        }

        $rules['agree_to_terms'] = [
            'sometimes',
            'in:1,true',
        ];

        $rules['loantype'] = [
            'sometimes',
            'nullable',
            'string',
            'max:63',
        ];

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
            'agree_to_terms.in' => __('Please confirm you agree to privacy policy.'),
            'phone.digits' => __('Phone must be :digits digits.'),
            'phone.regex' => __('Please provide your phone number.'),
        ];
    }
}
