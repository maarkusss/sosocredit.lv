<?php

namespace App\Http\Requests\Admincp;

use App\Rules\ValidGoLinkParams;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $name
 * @property $params_from
 * @property $domain_from
 * @property $domain_to
 * @property $path_ending_from
 * @property $path_ending_to
 * @property $params_to
 * @property $active
 */
class SaveGoRewriteRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
            ],
            'params_from' => [
                'sometimes',
                'nullable',
                'string',
                'json',
                new ValidGoLinkParams(),
            ],
            'params_to' => [
                'sometimes',
                'nullable',
                'string',
                'json',
                new ValidGoLinkParams(),
            ],
            'domain_from' => [
                'sometimes',
                'nullable',
                'string',
                'max:63',
            ],
            'domain_to' => [
                'sometimes',
                'nullable',
                'string',
                'max:63',
            ],
            'path_ending_from' => [
                'sometimes',
                'nullable',
                'string',
                'max:63',
            ],
            'path_ending_to' => [
                'sometimes',
                'nullable',
                'string',
                'max:63',
            ],
            'active' => [
                'sometimes',
                'integer',
            ],
        ];
    }
}
