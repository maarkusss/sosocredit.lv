<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class AllowedEmailDomain implements InvokableRule
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
        $allowed_domains = config('mail.allowed_domains');

        if (!empty($allowed_domains) && !empty($value)) {
            $exploded = explode('@', $value, 2);
            if (!empty($exploded[1])) {
                $domain = trim(strtolower($exploded[1]));

                if (!in_array($domain, $allowed_domains)) {
                    $fail(__("Please use different email provider, not") . ' ' . $domain);
                }
            }
        }
    }
}
