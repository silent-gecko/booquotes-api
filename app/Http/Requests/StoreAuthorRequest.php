<?php

namespace App\Http\Requests;

use Anik\Form\FormRequest;
use App\Rules\Year;

class StoreAuthorRequest extends FormRequest
{
    protected function rules(): array
    {
        return [
            'name' => ['required', 'unique:authors'],
            'born' => ['required', new Year()],
            'died' => ['nullable', new Year()],
            'bio'  => ['nullable'],
        ];
    }

    public function transform(): array
    {
        $data = $this->validated();
        return [
            'name'          => $data['name'] ?? null,
            'year_of_birth' => $data['born'] ?? null,
            'year_of_death' => $data['died'] ?? null,
            'bio'           => $data['bio'] ?? null,
        ];
    }
}