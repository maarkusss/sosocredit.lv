<?php

namespace App\Http\Requests\ApplicantForm;

use App\Rules\PersonalCode;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $email
 * @property $phone
 * @property $first_name
 * @property $last_name
 * @property $personal_code
 * @property $amount
 * @property $term_months
 * @property $coupon_code
 * @property $is_political_person
 * @property $has_real_property
 * @property $has_car
 * @property $subscribe
 * @property $agree_to_terms
 * @property $loantype
 * @property $section_id
 */
class ApplicantFormUpdateRequest extends FormRequest
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

        $rules['email'] = [
            'required',
            'email:rfc,dns',
            'max:150',
        ];

        $rules['phone'] = [
            'sometimes',
            'string',
            'regex:/^[0-9]+$/',
        ];

        //        if (config('landing_form.phone_placeholder') && strlen(config('landing_form.phone_placeholder')) > 0) {
        //            $length = strlen(config('landing_form.phone_placeholder'));
        //            $rules['phone'][] = 'digits:' . $length;
        //        }

        $rules['first_name'] = [
            'sometimes',
            'required',
            'string',
            'max:63',
        ];

        $rules['last_name'] = [
            'sometimes',
            'required',
            'nullable',
            'string',
            'max:127',
        ];

        $rules['personal_code'] = [
            'sometimes',
            'nullable',
            'string',
            'max:12',
            new PersonalCode(),
        ];

        $rules['is_political_person'] = [
            'sometimes',
            'nullable',
        ];

        $rules['has_real_property'] = [
            'sometimes',
            'nullable',
            'integer',
            'in:0,1',
        ];

        $rules['has_car'] = [
            'sometimes',
            'nullable',
            'integer',
            'in:0,1',
        ];

        $rules['agree_to_terms'] = [
            'accepted',
        ];

        $rules['coupon_code'] = [
            'sometimes',
            'nullable',
            'string',
            'max:63',
        ];

        $rules['loantype'] = [
            'sometimes',
            'nullable',
            'string',
            'max:63',
        ];

        $rules['subscribe'] = [
            'boolean',
            'nullable',
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
            'agree_to_terms.accepted' => __('Please confirm you agree to privacy policy.'),
            'agree_to_terms.in' => __('Please confirm you agree to privacy policy.'),
            'personal_code.min' => __('Personal code must be at least :min characters.'),
            'phone.digits' => __('Phone must be :digits digits.'),
        ];
    }
}
