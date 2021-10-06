<?php

namespace Unit\Requests;

use App\Http\Requests\BookRequest;
use Faker\Factory;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class BookRequestTest extends \TestCase
{
    /**
     * @dataProvider validationProvider
     *
     * @param $shouldPass
     * @param $payload
     * @param $message
     */
    public function test_validation_rules($shouldPass, $payload, $message)
    {
        $request = new BookRequest([], $payload);
        $request->setContainer($this->app);
        $request->setMethod('POST');

        $mock = \Mockery::mock(DatabasePresenceVerifier::class);
        $mock->shouldReceive('setConnection');
        $mock->shouldReceive('getCount')->andReturn(1);
        Validator::setPresenceVerifier($mock);

        $validationResult = $this->validate($request);

        $this->assertEquals($shouldPass, $validationResult, $message);
    }

    public function validationProvider()
    {
        $faker = Factory::create();

        return [
            [
                'passed'  => true,
                'data'    => [
                    'title'       => $faker->title(),
                    'description' => $faker->text(),
                    'author_id'   => $faker->uuid(),
                ],
                'message' => 'Validation fails when valid data is provided.',
            ],
            [
                'passed'  => true,
                'data'    => [
                    'title'       => $faker->title(),
                    'description' => '',
                    'author_id'   => $faker->uuid(),
                ],
                'message' => 'Validation fails when nullable data is null.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'title'       => '',
                    'description' => $faker->text(),
                    'author_id'   => $faker->uuid(),
                ],
                'message' => 'Validation passes when title is empty.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'title'       => 123456,
                    'description' => $faker->text(),
                    'author_id'   => $faker->uuid(),
                ],
                'message' => 'Validation passes when title is not a string.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'title'       => str_repeat('A', 161),
                    'description' => $faker->text(),
                    'author_id'   => $faker->uuid(),
                ],
                'message' => 'Validation passes when title is too long.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'title'       => $faker->title(),
                    'description' => 123456,
                    'author_id'   => $faker->uuid(),
                ],
                'message' => 'Validation passes when description is not a string.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'title'       => $faker->title(),
                    'description' => $faker->text(),
                    'author_id'   => '',
                ],
                'message' => 'Validation passes when author id is empty.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'title'       => $faker->title(),
                    'description' => $faker->text(),
                    'author_id'   => $faker->name(),
                ],
                'message' => 'Validation passes when author id is not a uuid.',
            ],
        ];
    }

    /**
     * @param BookRequest $request
     *
     * @return bool
     */
    protected function validate(BookRequest $request): bool
    {
        try {
            $request->validated();
        } catch (ValidationException $e) {
            return false;
        }

        return true;
    }
}