<?php

namespace Feature\V1\Controllers;

use App\Models\Book;
use App\Models\User;
use Faker\Factory;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Quote;

class QuoteControllerTest extends \TestCase
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
            ->json('get', route('v1.quote.index'));

        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'text',
                    'book'   => [
                        'id',
                        'title',
                    ],
                    'author' => [
                        'id',
                        'name',
                    ],
                    'links'  => [
                        'self',
                        'book',
                        'author',
                    ]
                ]
            ],
            'meta',
            'links',
        ]);
    }

    public function test_random_returns_valid_response()
    {
        $this->actingAs($this->user)
            ->json('get', route('v1.quote.random'));

        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'data' => [
                'id',
                'text',
                'book'   => [
                    'id',
                    'title',
                ],
                'author' => [
                    'id',
                    'name',
                ],
                'links'  => [
                    'self',
                    'book',
                    'author',
                ]
            ]
        ]);
    }

    public function test_show_returns_valid_response_with_valid_id()
    {
        $quote = Quote::factory()->create();

        $this->actingAs($this->user)
            ->json('get', route('v1.quote.show', ['uuid' => $quote->id]));

        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonEquals([
            'data' => [
                'id'     => $quote->id,
                'text'   => $quote->text,
                'book'   => [
                    'id'    => $quote->book_id,
                    'title' => $quote->book->title,
                ],
                'author' => [
                    'id'   => $quote->book->author_id,
                    'name' => $quote->book->author->name,
                ],
                'links'  => [
                    'self'   => $quote->self_link,
                    'book'   => $quote->book_link,
                    'author' => $quote->author_link,
                ]
            ]
        ]);
    }

    public function test_show_returns_error_with_invalid_id()
    {
        $this->actingAs($this->user)
            ->json('get', route('v1.quote.show', ['uuid' => '0']));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_show_returns_not_found_error()
    {
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);

        $this->actingAs($this->user)
            ->json('get', route('v1.quote.show', ['uuid' => $nonExistingUuid]));

        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_store_returns_valid_response_with_valid_data()
    {
        $book = Book::factory()->create();
        $payload = [
            'book_id' => $book->id,
            'text' => $this->faker->text(),
        ];

        $this->actingAs($this->user)->json('post', route('v1.quote.store'), $payload);

        $this->assertResponseStatus(Response::HTTP_CREATED);
        $this->seeJsonStructure(['data' => ['id']]);
        $this->seeInDatabase('quotes', $payload);
    }

    public function test_store_returns_error_when_book_not_exists()
    {
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);
        $payload = [
            'book_id' => $nonExistingUuid,
            'text' => $this->faker->text(),
        ];

        $this->actingAs($this->user)->json('post', route('v1.quote.store'), $payload);

        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure(['error' => ['code', 'message', 'errors' => ['book_id']]]);
        $this->missingFromDatabase('quotes', $payload);
    }

    public function test_update_returns_valid_response_aith_valid_id()
    {
        $book = Book::factory()->create();
        $anotherBook = Book::factory()->create();
        $quote = Quote::factory()->for($book)->create();
        $payload = [
            'book_id' => $anotherBook->id->toString(),
            'text' => $this->faker->text(),
        ];

        $this->actingAs($this->user)->put(route('v1.quote.update', ['uuid' => $quote->id]), $payload);

        $this->assertResponseStatus(Response::HTTP_NO_CONTENT);
        $this->seeInDatabase('quotes', array_merge($payload, ['id' => $quote->id]));
    }

    public function test_update_returns_error_when_book_not_exists()
    {
        $book = Book::factory()->create();
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);
        $quote = Quote::factory()->for($book)->create();
        $payload = [
            'book_id' => $nonExistingUuid,
            'text' => $this->faker->text(),
        ];

        $this->actingAs($this->user)->put(route('v1.quote.update', ['uuid' => $quote->id]), $payload);

        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure(['error' => ['code', 'message', 'errors' => ['book_id']]]);
        $this->missingFromDatabase('quotes', $payload);
    }

    public function test_update_returns_error_with_not_found_id()
    {
        $book = Book::factory()->create();
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);
        $payload = [
            'book_id' => $book->id->toString(),
            'text' => $this->faker->text(),
        ];

        $this->actingAs($this->user)->put(route('v1.quote.update', ['uuid' => $nonExistingUuid]), $payload);

        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_returns_error_with_invalid_uuid()
    {
        $book = Book::factory()->create();
        $invalidId = 123456;
        $payload = [
            'book_id' => $book->id->toString(),
            'text' => $this->faker->text(),
        ];

        $this->actingAs($this->user)->put(route('v1.quote.update', ['uuid' => $invalidId]), $payload);

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_destroy_returns_valid_response_with_valid_id()
    {
        $quote = Quote::factory()->create();

        $this->actingAs($this->user)->delete(route('v1.quote.destroy', ['uuid' => $quote->id]));
        $this->assertResponseStatus(Response::HTTP_NO_CONTENT);
        $this->missingFromDatabase('quotes', $quote->attributesToArray());

    }

    public function test_destroy_returns_error_with_not_found_id()
    {
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);

        $this->actingAs($this->user)->delete(route('v1.quote.destroy', ['uuid' => $nonExistingUuid]));

        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_destroy_returns_error_with_invalid_uuid()
    {
        $invalidId = 123456;

        $this->actingAs($this->user)->delete(route('v1.quote.destroy', ['uuid' => $invalidId]));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }
}