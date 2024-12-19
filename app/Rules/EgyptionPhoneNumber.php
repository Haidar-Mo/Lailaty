<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EgyptionPhoneNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^\+20\d{10}$/', $value)) {
            $fail(" رقم الهاتف غير صالح, يجب أن يبدأ بـ +20 متبوعاً بـعشرة أرقام");
        }
    }
}
