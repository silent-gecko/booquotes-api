<?php

namespace V1\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;

class AuthorBookControllerTest extends \TestCase
{
    use DatabaseTransactions;

    public function test_show_books_returns_valid_response()
    {
        $author = Author::factory()->create();
        $books = Book::factory()->count(3)->for($author)->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.author.book.show', ['uuid' => $author->id]));

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
                    'links' => [
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

    public function test_show_books_returns_error_with_invalid_id()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.author.book.show', ['uuid' => '0']));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_show_books_returns_not_found_error()
    {
        $user = User::factory()->create();
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);

        $this->actingAs($user)->json('get', route('v1.author.book.show', ['uuid' => $nonExistingUuid]));
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }
}