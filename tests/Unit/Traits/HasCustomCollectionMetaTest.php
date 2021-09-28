<?php

namespace Unit\Traits;

use App\Http\Resources\AuthorCollection;
use App\Models\Author;
use Laravel\Lumen\Testing\DatabaseTransactions;

class HasCustomCollectionMetaTest extends \TestCase
{
    use DatabaseTransactions;

    public function test_response_format()
    {
        $collection = new AuthorCollection(Author::paginate());

        $response = $collection->response();
        $jsonResponse = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('meta', $jsonResponse);
        $this->assertArrayNotHasKey('from', $jsonResponse['meta']);
        $this->assertArrayNotHasKey('links', $jsonResponse['meta']);
        $this->assertArrayNotHasKey('path', $jsonResponse['meta']);
        $this->assertArrayNotHasKey('to', $jsonResponse['meta']);
    }
}
