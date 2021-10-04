<?php

namespace App\Http\Requests;

use Pearl\RequestValidate\RequestAbstract;

class BookRequest extends RequestAbstract
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'description'  => ['nullable', 'string'],
            'author_id' => ['required', 'uuid', 'exists:authors,id'],
        ];
    }
}