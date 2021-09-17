<?php

namespace Unit\Middleware;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;

class AuthenticationTest extends \TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        Route::get('/test/auth', ['middleware' => 'auth', function() {
            return 'authorized';
        }]);
    }

    public function test_users_can_authenticate_with_valid_api_key()
    {
        $user = $this->createUser();

        $this->get('/test/auth', [config('app.api_key_header_name') => $user->token]);

        $this->assertEquals(
            200, $this->response->getStatusCode()
        );
    }

    public function test_users_can_not_authenticate_without_header()
    {
        $this->get('/test/auth');

        $this->assertEquals(
            401, $this->response->getStatusCode()
        );
    }

    public function test_users_can_not_authenticate_with_invalid_api_key()
    {
        $user = $this->createUser();
        $invalidToken = substr_replace($user->token->toString(), 'aaaaa', -5);

        $this->get('/test/auth', [config('app.api_key_header_name') => $invalidToken]);

        $this->assertEquals(
            401, $this->response->getStatusCode()
        );
    }

    /**
     * Creates user fake
     * @return Model
     */
    private function createUser(): Model
    {
        $userId = Str::uuid();
        $userToken = Str::uuid();
        return User::factory()->create(['id' => $userId, 'token' => $userToken]);
    }
}
