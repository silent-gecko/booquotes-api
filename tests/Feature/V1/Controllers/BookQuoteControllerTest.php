<?php

namespace Feature\V1\Controllers;


use App\Models\Author;
use App\Models\Book;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;

class BookQuoteControllerTest extends \TestCase
{
    use DatabaseTransactions;

    public function test_show_returns_valid_response_with_valid_id()
    {
        $author = Author::factory()->create();
        $book = Book::factory()->for($author)->create();
        $quotes = Quote::factory()->count(3)->for($book)->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.book.quote.show', ['uuid' => $book->id]));

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

    public function test_show_returns_valid_response_with_invalid_id()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.book.quote.show', ['uuid' => '0']));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_show_returns_not_found_error()
    {
        $user = User::factory()->create();
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);

        $this->actingAs($user)->json('get', route('v1.book.quote.show', ['uuid' => $nonExistingUuid]));
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }
}