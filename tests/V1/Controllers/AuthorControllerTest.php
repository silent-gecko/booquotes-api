<?php
namespace V1\Controllers;

use App\Models\User;
use App\Models\Author;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthorControllerTest extends \TestCase
{
    use DatabaseTransactions;

    public function test_index_returns_valid_status()
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->json('get', route('v1.author.index'))
            ->assertResponseStatus(Response::HTTP_OK);
    }

    public function test_index_returns_valid_data_format()
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->json('get', route('v1.author.index'))
            ->seeJsonStructure([
                'data' => [
                    '*' => [
                        'name',
                        'links' => [
                            'self',
                            'books',
                            'quotes',
                        ]
                    ]
                ]
            ]);
    }

    public function test_show_returns_valid_status_with_valid_id()
    {
        $author = Author::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.author.show', ['uuid' => $author->id]))
            ->assertResponseStatus(Response::HTTP_OK);
    }

    public function test_show_returns_error_with_invalid_id()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.author.show', ['uuid' => '0']));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_show_returns_not_found_error()
    {
        $user = User::factory()->create();
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);

        $this->actingAs($user)->json('get', route('v1.author.show', ['uuid' => $nonExistingUuid]));
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_show_returns_valid_data_format()
    {
        $author = Author::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.author.show', ['uuid' => $author->id]))
            ->seeJsonStructure([
                'data' => [
                    'name',
                    'born',
                    'died',
                    'bio',
                    'links' => [
                        'self',
                        'books',
                        'quotes',
                    ]
                ]
            ]);
    }
}