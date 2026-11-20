<?php

namespace App\Http\Requests\Admincp;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $utm_source
 * @property $utm_campaign
 * @property $token
 * @property $rules
 * @property $active
 */
class SaveLenderFilterRequest extends FormRequest
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
        return [
            'utm_source' => 'required|string|max:255',
            'utm_campaign' => 'sometimes|nullable|string|max:255',
            'token' => 'sometimes|nullable|string|max:191',
            'rules' => 'sometimes|array',
            'active' => 'sometimes|boolean',
        ];
    }
}
