<?php

namespace Feature;
use Illuminate\Http\JsonResponse;

class HealthCheckTest extends \TestCase
{
    public function test_homepage_is_functioning()
    {
        $this->get(route('home'));

        $this->assertEquals(
            response()->jsonHealthCheck()->content(), $this->response->getContent()
        );
    }
}