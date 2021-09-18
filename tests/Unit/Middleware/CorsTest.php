<?php

namespace Unit\Middleware;

use App\Http\Middleware\CorsMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CorsTest extends \TestCase
{
    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function test_headers_are_set()
    {
        $this->expectNotToPerformAssertions();
        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'GET, POST, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '0',
            'Access-Control-Allow-Headers'     =>
                'Accept, Content-Type, Authorization, X-Requested-With, ' . config('app.api_key_header_name'),
        ];
        $request = Request::create('/', 'GET');
        $middleware = new CorsMiddleware();
        $response = \Mockery::mock(Response::class)
            ->shouldReceive('withHeaders')
            ->with($headers)
            ->getMock();

        $middleware->handle($request, function() use ($response) {
            return $response;
        });
    }
}