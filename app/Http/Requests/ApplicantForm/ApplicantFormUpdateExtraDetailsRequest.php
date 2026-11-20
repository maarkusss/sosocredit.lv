<?php

namespace App\Http\Requests\ApplicantForm;

use App\Rules\PersonalCode;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $first_name
 * @property $last_name
 * @property $personal_code
 * @property $cadastral_number
 * @property $salary
 * @property $has_real_property
 * @property $has_car
 * @property $loan_type
 * @property $address_zipcode
 */
class ApplicantFormUpdateExtraDetailsRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        $rules['personal_code'] = [
            'required',
            'nullable',
            'string',
            'max:12',
            new PersonalCode(),
        ];

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

        $rules['salary'] = [
            'required_unless:loan_type,kredits-uznemumam',
            'numeric',
        ];

        $rules['has_real_property'] = [
            'required_unless:loan_type,kredits-uznemumam',
            'numeric',
            'in:0,1'
        ];

        $rules['has_car'] = [
            'required_unless:loan_type,kredits-uznemumam',
            'numeric',
            'in:0,1'
        ];

        $rules['age'] = [
            'required_unless:loan_type,kredits-uznemumam',
            'date',
            'date_format:Y-m-d',
            'before:today',
            'after:' . now()->subYears(100)->format('Y-m-d'),
            function ($attribute, $value, $fail) {
                try {
                    $age = Carbon::parse($value)->age;
                } catch (Exception $e) {
                    return;
                }
                if ($age < 18) {
                    $fail(__('You must be at least 18 years old'));
                }
            },
        ];

        $rules['car_number'] = [
            'required_if:has_car,1',
            'string',
            'min:1',
            'max:11'
        ];

        $rules['cadastral_number'] = [
            'required_if:has_real_property,1',
            'string',
            'regex:/^[\d:\s]+$/', // only digits, colons, and spaces
            function ($attribute, $value, $fail) {
                $digitsOnly = preg_replace('/[^\d]/', '', $value);
                $length = strlen($digitsOnly);

                if ($length != 11) {
                    $fail(__('Please provide valid real num'));
                }
            },
        ];

        $rules['address_zipcode'] = [
            'required_unless:loan_type,kredits-uznemumam',
            'string',
            'regex:/^LV-\d{4}$/',
            'min:7',
            'max:7',
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
            'personal_code.min' => __('Personal code must be at least :min characters.'),
            'cadastral_number' => __('Please provide valid real num'),
            'salary' => __('Please provide a valid salary'),
            'age' => __('Please provide valid age'),
            'has_real_property' => __('Please provide valid real'),
            'has_car' => __('Please provide valid car'),
            'car_number' => __('Please provide valid car'),
            'address_zipcode' => __('Please provide valid zipcode'),
        ];
    }
}
