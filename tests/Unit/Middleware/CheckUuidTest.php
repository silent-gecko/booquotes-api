<?php

namespace Unit\Middleware;

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseTransactions;

class CheckUuidTest extends \TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        Route::get('/test/{uuid}', ['middleware' => 'check_uuid', 'as' => 'test', function() {
            return 'test';
        }]);
    }

    public function test_return_error_on_invalid_id()
    {
        $invalidId = 123;

        $this->get(route('test', ['uuid' => $invalidId]));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_return_data_on_valid_uuid()
    {
        $validId = Str::uuid();

        $this->get(route('test', ['uuid' => $validId]));

        $this->assertResponseStatus(Response::HTTP_OK);
    }
}