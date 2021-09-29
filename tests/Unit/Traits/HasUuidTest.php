<?php

namespace Unit\Traits;

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Lumen\Testing\DatabaseTransactions;

class HasUuidTest extends \TestCase
{
    use DatabaseTransactions;

    public function test_uuid_is_set_on_create()
    {
        $model = User::factory()->create();

        $this->assertNotEmpty($model->getKeyName());
    }

    public function test_uuid_is_present_on_create()
    {
        $uuid = Str::uuid()->toString();

        User::factory(['id' => $uuid])->create();

        $this->seeInDatabase('users', ['id' => $uuid]);
    }

    public function test_uuid_is_set_when_invalid()
    {
        $model = User::factory(['id' => 123])->create();

        $this->assertTrue(Str::isUuid($model->getAttribute('id')->toString()));
    }
}