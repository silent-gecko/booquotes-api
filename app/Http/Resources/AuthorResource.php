<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name'  => $this->name,
            'born'  => $this->year_of_birth,
            'died'  => $this->year_of_death,
            'bio'   => $this->bio,
            'links' => [
                'self'   => 'link-value',
                'books'  => 'link-value',
                'quotes' => 'link-value',
            ]
        ];
    }
}