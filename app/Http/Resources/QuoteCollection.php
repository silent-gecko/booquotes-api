<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Extensions\Traits\HasCustomCollectionMeta;

class QuoteCollection extends ResourceCollection
{
    use HasCustomCollectionMeta;

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