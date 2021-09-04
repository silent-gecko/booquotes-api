<?php

namespace V1\Controllers;

use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use TestCase;
use App\Models\User;
use App\Models\Book;

class BooksControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index_returns_valid_data()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.book.index'));

        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'data' => [
                '*' => [
                    'title',
                    'author',
                    'links' => [
                        'self',
                        'author',
                        'quotes',
                    ]
                ]
            ]
        ]);
    }

    public function test_show_returns_valid_data_with_valid_id()
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.book.show', ['uuid' => $book->id]));

        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'data' => [
                'title',
                'description',
                'links' => [
                    'self',
                    'author',
                    'quotes',
                ]
            ]
        ]);
    }

    public function test_show_returns_error_with_invalid_id()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.book.show', ['uuid' => '0']));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_show_returns_not_found_error()
    {
        $user = User::factory()->create();
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);

        $this->actingAs($user)->json('get', route('v1.book.show', ['uuid' => $nonExistingUuid]));

        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }
}