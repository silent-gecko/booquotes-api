<?php
namespace V1\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Author;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthorControllerTest extends \TestCase
{
    use DatabaseTransactions;

    public function test_index_returns_valid_response()
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->json('get', route('v1.author.index'));

        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'links' => [
                        'self',
                        'books',
                        'quotes',
                    ]
                ]
            ],
            'meta',
            'links'
        ]);
    }

    public function test_show_returns_valid_response_with_valid_id()
    {
        $author = Author::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.author.show', ['uuid' => $author->id]));

        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonEquals([
            'data' => [
                'id' => $author->id,
                'name' => $author->name,
                'born' => (int) $author->year_of_birth,
                'died' => (int) $author->year_of_death,
                'bio' => $author->bio,
                'quotes_count' => $author->quotes->count(),
                'links' => [
                    'self' => $author->self_link,
                    'books' => $author->books_link,
                    'quotes' => $author->quotes_link,
                ]
            ]
        ]);
    }

    public function test_show_returns_valid_response_with_invalid_id()
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
}