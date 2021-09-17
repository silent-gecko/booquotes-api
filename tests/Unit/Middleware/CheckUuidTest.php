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

    public function test_return_error_on_numeric_id()
    {
        $numericId = 123;

        $this->get(route('test', ['uuid' => $numericId]));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_return_error_on_string_id()
    {
        $stringId = Str::remove('-', Str::uuid());

        $this->get(route('test', ['uuid' => $stringId]));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_return_data_on_valid_uuid()
    {
        $validId = Str::uuid();

        $this->get(route('test', ['uuid' => $validId]));

        $this->assertResponseStatus(Response::HTTP_OK);
    }
}