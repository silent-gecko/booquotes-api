<?php

namespace Feature\V1\Controllers;

use App\Models\Book;
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
        $this->seeInDatabase('authors', [
            'name' => 'Some Guy',
            'year_of_birth' => 1929,
            'year_of_death' => 2012,
            'bio'  => 'Some bio',
        ]);
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

    public function test_update_returns_valid_response_with_valid_data()
    {
        $author = Author::factory()->create();
        $payload = [
            'name' => 'Some Updated Guy',
            'born' => 1929,
            'died' => 2012,
            'bio'  => 'Some updated bio',
        ];

        $this->actingAs($this->user)
            ->put(route('v1.author.update', ['uuid' => $author->id]), $payload);

        $this->assertResponseStatus(Response::HTTP_NO_CONTENT);
        $this->seeInDatabase('authors', [
            'id'            => $author->id,
            'name'          => 'Some Updated Guy',
            'year_of_birth' => 1929,
            'year_of_death' => 2012,
        ]);
    }

    public function test_update_returns_error_with_not_found_id()
    {
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);
        $payload = [
            'name' => 'Some Updated Guy',
            'born' => 1929,
            'died' => 2012,
            'bio'  => 'Some updated bio',
        ];

        $this->actingAs($this->user)
            ->put(route('v1.author.update', ['uuid' => $nonExistingUuid]), $payload);

        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_destroy_returns_valid_data_with_valid_id()
    {
        $author = Author::factory()->create();

        $this->actingAs($this->user)
            ->delete(route('v1.author.destroy', ['uuid' => $author->id]));

        $this->assertResponseStatus(Response::HTTP_NO_CONTENT);
        $this->missingFromDatabase('authors', $author->attributesToArray());
    }

    public function test_destroy_returns_valid_data_when_cannot_delete()
    {
        $author = Author::factory()->has(Book::factory()->count(3))->create();

        $this->actingAs($this->user)
            ->delete(route('v1.author.destroy', ['uuid' => $author->id]));

        $this->assertResponseStatus(Response::HTTP_CONFLICT);
        $this->seeInDatabase('authors', $author->attributesToArray());
    }

    public function test_destroy_returns_error_with_not_found_id()
    {
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);

        $this->actingAs($this->user)
            ->delete(route('v1.author.destroy', ['uuid' => $nonExistingUuid]));

        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }
}