<?php

namespace V1\Middleware;

use App\Models\Author;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class CheckUuidTest extends \TestCase
{
    public function test_return_error_on_numeric_id()
    {
        $user = User::factory()->create();
        $numericId = 123;

        $this->actingAs($user)
            ->json('get', route('v1.author.show', ['uuid' => $numericId]));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_return_error_on_string_id()
    {
        $user = User::factory()->create();
        $stringId = Str::remove('-', Str::uuid());

        $this->actingAs($user)
            ->json('get', route('v1.author.show', ['uuid' => $stringId]));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_return_data_on_valid_uuid()
    {
        $author = Author::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.author.show', ['uuid' => $author->id]));

        $this->assertResponseStatus(Response::HTTP_OK);
    }
}