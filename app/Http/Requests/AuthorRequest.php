<?php

namespace App\Http\Requests;

use App\Rules\Year;
use Pearl\RequestValidate\RequestAbstract;

class AuthorRequest extends RequestAbstract
{
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'name' => ['required', 'unique:authors,name'],
                'born' => ['required', new Year()],
                'died' => ['nullable', new Year(), 'gte:born'],
                'bio'  => ['nullable'],
            ];
        }

        return [];
    }

    protected function withValidator($validator): void
    {
        $validator->sometimes('name', 'starts_with:old', function ($input) {
            return $input->born <= 1920;
        });
    }

    public function transformValidated(): array
    {
        $data = $this->validated();

        return [
            'id'            => $data['id'] ?? null,
            'name'          => $data['name'] ?? null,
            'year_of_birth' => $data['born'] ?? null,
            'year_of_death' => $data['died'] ?? null,
            'bio'           => $data['bio'] ?? null,
        ];
    }
}