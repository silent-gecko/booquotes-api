<?php

namespace Feature\V1\Controllers;

use App\Models\Author;
use App\Models\Quote;
use Faker\Factory;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use TestCase;
use App\Models\User;
use App\Models\Book;

class BookControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->faker = Factory::create();
    }

    public function test_index_returns_valid_response()
    {
        $this->actingAs($this->user)
            ->json('get', route('v1.book.index'));

        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'author' => [
                        'id',
                        'name',
                    ],
                    'links'  => [
                        'self',
                        'author',
                        'quotes',
                    ]
                ]
            ],
            'meta',
            'links',
        ]);
    }

    public function test_show_returns_valid_response_with_valid_id()
    {
        $book = Book::factory()->create();

        $this->actingAs($this->user)
            ->json('get', route('v1.book.show', ['uuid' => $book->id]));

        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonEquals([
            'data' => [
                'id'           => $book->id,
                'title'        => $book->title,
                'author'       => [
                    'id'   => $book->author_id,
                    'name' => $book->author->name,
                ],
                'description'  => $book->description,
                'quotes_count' => $book->quotes->count(),
                'links'        => [
                    'self'   => $book->self_link,
                    'author' => $book->author_link,
                    'quotes' => $book->quotes_link,
                ]
            ]
        ]);
    }

    public function test_show_returns_error_with_invalid_id()
    {
        $this->actingAs($this->user)
            ->json('get', route('v1.book.show', ['uuid' => '0']));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_show_returns_not_found_error()
    {
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);

        $this->actingAs($this->user)->json('get', route('v1.book.show', ['uuid' => $nonExistingUuid]));

        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_store_returns_valid_response_with_valid_data()
    {
        $author = Author::factory()->create();
        $payload = [
            'author_id' => $author->id,
            'title' => $this->faker->title(),
            'description' => $this->faker->text(),
        ];

        $this->actingAs($this->user)->json('post', route('v1.book.store'), $payload);
        $this->assertResponseStatus(Response::HTTP_CREATED);
        $this->seeJsonStructure(['data' => ['id']]);
        $this->seeInDatabase('books', $payload);
    }

    public function test_store_returns_error_when_author_not_exists()
    {
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);
        $payload = [
            'author_id' => $nonExistingUuid,
            'title' => $this->faker->title(),
            'description' => $this->faker->text(),
        ];

        $this->actingAs($this->user)->json('post', route('v1.book.store'), $payload);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure(['error' => ['code', 'message', 'errors' => ['author_id']]]);
        $this->missingFromDatabase('books', $payload);
    }

    public function test_update_returns_valid_response_with_valid_data()
    {
        $author = Author::factory()->create();
        $anotherAuthor = Author::factory()->create();
        $book = Book::factory()->for($author)->create();
        $payload = [
            'author_id' => $anotherAuthor->id->toString(),
            'title'=> 'Some Updated Title',
            'description' => 'Updated description',
        ];

        $this->actingAs($this->user)->put(route('v1.book.update', ['uuid' => $book->id]), $payload);

        $this->assertResponseStatus(Response::HTTP_NO_CONTENT);
        $this->seeInDatabase('books', array_merge($payload, ['id' => $book->id]));
    }

    public function test_update_returns_error_when_author_not_exists()
    {
        $author = Author::factory()->create();
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);
        $book = Book::factory()->for($author)->create();
        $payload = [
            'author_id' => $nonExistingUuid,
            'title'=> 'Some Updated Title',
            'description' => 'Updated description',
        ];

        $this->actingAs($this->user)->put(route('v1.book.update', ['uuid' => $book->id]), $payload);

        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure(['error' => ['code', 'message', 'errors' => ['author_id']]]);
        $this->missingFromDatabase('books', $payload);
    }

    public function test_update_returns_error_with_not_found_id()
    {
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);
        $author = Author::factory()->create();
        $payload = [
            'author_id' => $author->id->toString(),
            'title'=> 'Some Updated Title',
            'description' => 'Updated description',
        ];

        $this->actingAs($this->user)->put(route('v1.book.update', ['uuid' => $nonExistingUuid]), $payload);

        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_returns_error_with_invalid_uuid()
    {
        $invalidId = 123456;
        $author = Author::factory()->create();
        $payload = [
            'author_id' => $author->id->toString(),
            'title'=> 'Some Updated Title',
            'description' => 'Updated description',
        ];

        $this->actingAs($this->user)->put(route('v1.book.update', ['uuid' => $invalidId]), $payload);

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_destroy_returns_valid_response_with_valid_id()
    {
        $book = Book::factory()->create();

        $this->actingAs($this->user)->delete(route('v1.book.destroy', ['uuid' => $book->id]));

        $this->assertResponseStatus(Response::HTTP_NO_CONTENT);
        $this->missingFromDatabase('books', $book->attributesToArray());
    }

    public function test_destroy_returns_error_with_not_found_id()
    {
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);

        $this->actingAs($this->user)->delete(route('v1.book.destroy', ['uuid' => $nonExistingUuid]));

        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_destroy_returns_error_when_cannot_delete()
    {
        $book = Book::factory()->has(Quote::factory()->count(3))->create();

        $this->actingAs($this->user)->delete(route('v1.book.destroy', ['uuid' => $book->id]));

        $this->assertResponseStatus(Response::HTTP_CONFLICT);
        $this->seeInDatabase('books', $book->attributesToArray());
    }

    public function test_destroy_returns_error_with_invalid_uuid()
    {
        $invalidId = 123456;

        $this->actingAs($this->user)->delete(route('v1.book.destroy', ['uuid' => $invalidId]));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }
}
