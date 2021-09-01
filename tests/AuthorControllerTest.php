<?php

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthorControllerTest extends TestCase
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
}