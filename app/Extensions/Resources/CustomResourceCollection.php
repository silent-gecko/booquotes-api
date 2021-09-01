<?php

namespace App\Extensions\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Extensions\Resources\CustomPaginatedResourceResponse;

/**
 * Class extends original framework class with adding custom paginated class instance
 */
class CustomResourceCollection extends ResourceCollection
{

    /**
     * Create a paginate-aware HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function preparePaginatedResponse($request)
    {
        if ($this->preserveAllQueryParameters) {
            $this->resource->appends($request->query());
        } elseif (!is_null($this->queryParameters)) {
            $this->resource->appends($this->queryParameters);
        }

        return (new CustomPaginatedResourceResponse($this))->toResponse($request);
    }
}