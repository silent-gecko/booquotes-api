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
        //dd($this->author());
        return [
            'text'  => $this->text,
            'book' => $this->book->title,
            'author' => $this->book->author->name,
            'links' => [
                'self'   => $this->self_link,
                'book' => $this->book_link,
                'author'  => $this->author_link,
            ]
        ];
    }
}