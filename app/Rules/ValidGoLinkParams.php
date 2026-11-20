<?php

namespace App\Rules;

use App\Entity\GoLink;
use Illuminate\Contracts\Validation\InvokableRule;

class ValidGoLinkParams implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        // We take that $json already has a valid JSON object
        $arrayed_value = json_decode($value, true);
        if (empty($arrayed_value)) {
            return;
        }

        $required_keys = GoLink::REQUIRED_PARAMS;
        $provided_keys = array_keys($arrayed_value);

        if (!array_intersect($required_keys, $provided_keys)) {
            $fail('Incorrect format of ' . $attribute . ': it must have at least one of keys: ' . implode(', ', $required_keys));
        }

        if (array_diff($provided_keys, $required_keys)) {
            $fail('Incorrect format of ' . $attribute . ': it can have only these keys: ' . implode(', ', $required_keys));
        }
    }
}
