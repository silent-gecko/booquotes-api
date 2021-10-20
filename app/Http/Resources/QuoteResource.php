<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
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
            'id'     => $this->id,
            'text'   => $this->text,
            'book'   => [
                'id'    => $this->book_id,
                'title' => $this->book->title,
            ],
            'author' => [
                'id'   => $this->book->author_id,
                'name' => $this->book->author->name,
            ],
            'links'  => [
                'self'   => $this->self_link,
                'image'  => $this->image_link,
                'book'   => $this->book_link,
                'author' => $this->author_link,
            ]
        ];
    }
}