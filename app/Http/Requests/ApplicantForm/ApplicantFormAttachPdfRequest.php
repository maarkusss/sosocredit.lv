<?php

namespace App\Http\Requests\ApplicantForm;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $email
 * @property $phone
 * @property $pdf
 * @property $user
 */
class ApplicantFormAttachPdfRequest extends FormRequest
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
            'email',
        ];

        $rules['phone'] = [
            'required',
            'string',
            'regex:/^[0-9]+$/',
        ];

        if (config('landing_form.phone_placeholder') && strlen(config('landing_form.phone_placeholder')) > 0) {
            $length = strlen(config('landing_form.phone_placeholder'));
            $rules['phone'][] = 'digits:' . $length;
        }

        $rules['pdf'] = [
            'required',
            'file',
            'mimes:pdf',
            'max:10000',
        ];

        $rules['user'] = [
            'required',
            'string',
            'in:soso',
        ];

        return $rules;
    }
}
