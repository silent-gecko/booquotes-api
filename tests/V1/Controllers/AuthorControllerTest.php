<?php
namespace V1\Controllers;

use App\Models\User;
use App\Models\Author;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Lumen\Testing\DatabaseTransactions;

class AuthorControllerTest extends \TestCase
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

    public function test_show_returns_valid_status_with_valid_id()
    {
        $author = Author::create([
            'name' => 'test absdef',
            'year_of_birth' => 1900,
            'year_of_death' => null,
            'bio'           => 'test',
        ]);
        //$author->refresh();
        dd($author->id);
        /*$user = User::factory()->create();
        $author = Author::firstWhere('name', '<>', null);
        $this->actingAs($user)
            ->json('get', route('v1.author.show', ['uuid' => $author->id]))
            ->assertResponseStatus(Response::HTTP_OK);*/
        $this->assertTrue(false);
    }

    public function test_show_returns_valid_status_with_invalid_id()
    {
        $this->assertTrue(false);
    }
}