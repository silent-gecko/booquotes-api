<?php

namespace App\Extensions\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

/**
 * Customize resource collection paginated response
 */
trait HasCustomCollectionMeta
{
    /**
     * Customize the outgoing response for the resource.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return void
     */
    public function withResponse($request, $response): void
    {
        $jsonResponse = json_decode($response->getContent(), true);
        Arr::forget($jsonResponse, ['meta.from', 'meta.links', 'meta.path', 'meta.to']);
        $response->setContent(json_encode($jsonResponse));
    }
}