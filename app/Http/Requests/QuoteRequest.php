<?php

namespace App\Http\Requests;

use Pearl\RequestValidate\RequestAbstract;

class QuoteRequest extends RequestAbstract
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'book_id' => ['required', 'uuid', 'exists:books,id'],
            'text' => ['required', 'string', 'max:400'],
        ];
    }
}