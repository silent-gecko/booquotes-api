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
        if ($this->isMethod('POST')) {
            return [
                'name' => ['required', 'string', 'max:100'],
                'born' => ['required', new Year()],
                'died' => ['nullable', new Year(), 'gt:born'],
                'bio'  => ['nullable', 'string'],
            ];
        }

        return [];
    }

    /**
     * @param $validator
     */
    public function withValidator($validator): void
    {
        $rule = 'unique:authors,name,'.$this->input('id').',id,year_of_birth,' . $this->input('born');
        $validator->sometimes('name', $rule, function ($input) {
            return $input->born;
        });
    }

    /**
     * Transform request data after validation
     * @return array
     */
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