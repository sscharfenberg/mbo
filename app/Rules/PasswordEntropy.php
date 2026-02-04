<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use ZxcvbnPhp\Zxcvbn;

class PasswordEntropy implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $zxcvbn = new Zxcvbn();
        $entropy = $zxcvbn->passwordStrength($value);
        if ($entropy['score'] < 3) {
            $fail('validation.custom.password.entropy')->translate();
        }
    }
}
