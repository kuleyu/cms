<?php

namespace Fusion\Rules;

use Fusion\Console\Actions\CheckServerRequirements;
use Illuminate\Contracts\Validation\Rule;

class ServerRequirements implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return CheckServerRequirements::verifyPHPVersion() &&
               CheckServerRequirements::verifyExtensions();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Does not meet expected server requirements.';
    }
}
