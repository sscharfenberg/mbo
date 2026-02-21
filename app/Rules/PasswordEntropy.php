<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use ZxcvbnPhp\Zxcvbn;

class PasswordEntropy implements ValidationRule
{
    /**
     * Validate that a password meets the minimum entropy threshold.
     *
     * Uses zxcvbn to estimate real-world password strength. Rejects
     * passwords with a score below 3 (out of 0-4), which corresponds
     * roughly to "safely unguessable" against offline attacks.
     *
     * @param  string   $attribute
     * @param  mixed    $value
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
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
