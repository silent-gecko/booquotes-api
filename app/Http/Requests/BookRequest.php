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
            'author' => ['required', 'uuid', 'exists:authors,id'],
        ];
    }

    public function validated(): array
    {
        $data = parent::validated();
        $data['author_id'] = $data['author'];
        unset($data['author']);

        return $data;
    }
}