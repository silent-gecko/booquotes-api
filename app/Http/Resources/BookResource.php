<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'title'  => $this->title,
            'author' => $this->author->name,
            'links' => [
                'self'   => $this->self_link,
                'author'  => $this->author_link,
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
                'description'  => $this->description,
            ]
        ];
    }
}