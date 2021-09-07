<?php

namespace App\Http\Resources;

use App\Extensions\Resources\CustomResourceCollection;
use Illuminate\Http\Request;

class QuoteCollection extends CustomResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}