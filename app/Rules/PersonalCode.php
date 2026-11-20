<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Str;

class PersonalCode implements InvokableRule
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
        $clean = str_replace('_', '', $value);

        if (Str::length($clean) > 1 && (Str::length($clean) < 11 || Str::length($clean) > 12)) {
            $fail(__('Please provide valid personal code') . ': ' . $value);
        }

        if ($value === '______-_____') {
            $fail(__('Please provide valid personal code'));
        }

        if (Str::length($clean) > 1 && Str::contains($clean, '-')) {
            $exploded = explode('-', $clean, 2);
            $first = $exploded[0];
            if (!empty($exploded[1])) {
                $second = $exploded[1];
            }

            $blacklist_first = [
                '000000',
                '111111',
                '222222',
                '333333',
                '444444',
                '555555',
                '666666',
                '777777',
                '888888',
                '999999',
                '123456',
                '654321',
                '123123',
                '321321',
            ];

            if ((!empty($first) && in_array($first, $blacklist_first)) || empty($second)) {
                $fail(__('Please provide valid personal code') . ': ' . $value);
            }
        }
    }
}
