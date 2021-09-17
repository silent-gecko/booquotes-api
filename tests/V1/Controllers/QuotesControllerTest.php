<?php

namespace V1\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Quote;

class QuotesControllerTest extends \TestCase
{
    use DatabaseTransactions;

    public function test_index_returns_valid_response()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
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
        $user = User::factory()->create();

        $this->actingAs($user)
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
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.quote.show', ['uuid' => $quote->id]));

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

    public function test_show_returns_valid_response_with_invalid_id()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->json('get', route('v1.quote.show', ['uuid' => '0']));

        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }

    public function test_show_returns_not_found_error()
    {
        $user = User::factory()->create();
        $nonExistingUuid = substr_replace(Str::uuid()->toString(), 'aaaaa', -5);

        $this->actingAs($user)->json('get', route('v1.quote.show', ['uuid' => $nonExistingUuid]));

        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $this->seeJsonStructure(['error' => ['code', 'message']]);
    }
}