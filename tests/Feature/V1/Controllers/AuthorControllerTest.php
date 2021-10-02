<?php

namespace Feature\V1\Controllers;

use App\Models\User;
use App\Models\Author;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthorControllerTest extends \TestCase
{
    use DatabaseTransactions;

    /**
     * @var Model
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_index_returns_valid_response()
    {
        $this->actingAs($this->user)
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

        $this->actingAs($this->user)
            ->json('get', route('v1.author.show', ['uuid' => $author->id]));

        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonEquals([
            'data' => [
                'id'           => $author->id,
                'name'         => $author->name,
                'born'         => (int) $author->year_of_birth,
                'died'         => (int) $author->year_of_death,
                'bio'          => $author->bio,
                'quotes_count' => $author->quotes->count(),
                'links'        => [
                    'self'   => $author->self_link,
                    'books'  => $author->books_link,
                    'quotes' => $author->quotes_link,
                ]
            ]
        ]);
    }

    public function test_show_returns_valid_response_with_invalid_id()
    {
        $this->actingAs($this->user)
            ->json('get', route('v1.author.show', ['uuid' => '0']));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_show_returns_not_found_error()
    {
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);

        $this->actingAs($this->user)->json('get', route('v1.author.show', ['uuid' => $nonExistingUuid]));
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_store_returns_valid_response_with_valid_data()
    {
        $payload = [
            'name' => 'Some Guy',
            'born' => 1929,
            'died' => 2012,
            'bio'  => 'Some bio',
        ];

        $this->actingAs($this->user)
            ->json('post', route('v1.author.store'), $payload);

        $this->assertResponseStatus(Response::HTTP_CREATED);
        $this->seeJsonStructure(['data' => ['id']]);
        $this->seeInDatabase('authors', $payload);
    }

    public function test_store_returns_error_with_duplicate_data()
    {
        Author::factory(['name' => 'Some Guy', 'year_of_birth' => 1700])->create();

        $this->actingAs($this->user)
            ->json('post', route('v1.author.store'), ['name' => 'Some Guy', 'born' => 1700]);

        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_store_returns_valid_response_without_duplicated_data()
    {
        Author::factory(['name' => 'Some Guy', 'year_of_birth' => 1700])->create();

        $this->actingAs($this->user)
            ->json('post', route('v1.author.store'), ['name' => 'Some Guy', 'born' => 1701]);

        $this->assertResponseStatus(Response::HTTP_CREATED);
        $this->seeJsonStructure(['data' => ['id']]);
    }
}