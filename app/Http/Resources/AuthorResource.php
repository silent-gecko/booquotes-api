<?php

namespace App\Http\Resources;

use App\Http\Controllers\AuthorController;
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
            'links' => [
                'self'   => $this->self_link,
                'books'  => $this->books_link,
                'quotes' => $this->quotes_link,
            ]
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function with($request)
    {
        return [
            'data' => [
                'quotes_count' => $this->quotes->count(),
                'born'         => $this->year_of_birth,
                'died'         => $this->year_of_death,
                'bio'          => $this->bio,
            ]
        ];
    }
}