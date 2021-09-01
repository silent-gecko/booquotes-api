<?php

namespace App\Extensions\Resources;

use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Support\Arr;

/**
 * Class extends original framework class with customizing the `meta` field of the response
 */
class CustomPaginatedResourceResponse extends PaginatedResourceResponse
{
    /**
     * Gather the meta data for the response.
     *
     * @param  array  $paginated
     * @return array
     */
    protected function meta($paginated)
    {
        return Arr::except($paginated, [
            'data',
            'first_page_url',
            'from',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'prev_page_url',
            'to',
        ]);
    }
}