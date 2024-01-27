<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidMobileNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $condition = preg_match("/^(?:\+?88)?01[13-9]\d{8}$/", $value);;
        if (!$condition) {
            $fail('The :attribute is not a valid BD mobile number');
        }
    }
}
