<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;

class Year implements Rule
{

    /**
     * @inheritDoc
     */
    public function passes($attribute, $value)
    {
        return is_numeric($value) && $value >= 0 && $value <= Carbon::now()->year;
    }

    /**
     * @inheritDoc
     */
    public function message()
    {
        return 'The :attribute must be non-future year.';
    }
}