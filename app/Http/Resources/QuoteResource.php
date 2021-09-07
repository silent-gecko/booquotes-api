<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'text'  => $this->text,
            'book' => $this->book->title,
            'author' => $this->author->name,
            'links' => [
                'self'   => $this->self_link,
                'book' => $this->book_link,
                'author'  => $this->author_link,
            ]
        ];
    }
}