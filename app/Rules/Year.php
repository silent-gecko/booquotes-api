<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Year implements Rule
{

    /**
     * @inheritDoc
     */
    public function passes($attribute, $value)
    {
        return is_numeric($value) && $value >= 0 && $value <= 9999;
    }

    /**
     * @inheritDoc
     */
    public function message()
    {
        return 'The :attribute must be a year.';
    }
}