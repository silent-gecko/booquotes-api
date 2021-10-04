<?php

namespace App\Http\Requests;

use App\Rules\Year;
use Pearl\RequestValidate\RequestAbstract;

class AuthorRequest extends RequestAbstract
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'born' => ['required', new Year()],
            'died' => ['nullable', new Year(), 'gt:born'],
            'bio'  => ['nullable', 'string'],
        ];
    }

    /**
     * @param $validator
     */
    public function withValidator($validator): void
    {
        $rule = 'unique:authors,name,'.($this->route('uuid') ?: 'NULL') .',id,year_of_birth,' . $this->input('born');
        $validator->sometimes('name', $rule, function ($input) {
            return $input->born;
        });
    }

    /**
     * Transform request data after validation
     * @return array
     */
    public function validated(): array
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