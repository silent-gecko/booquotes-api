<?php

namespace Unit\Requests;

use App\Http\Requests\AuthorRequest;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\ValidationException;
use Faker\Factory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Factory as ValidatorFactory;

class AuthorRequestTest extends \TestCase
{
    /**
     * @dataProvider validationProvider
     */
    public function test_validation_rules($shouldPass, $payload, $message)
    {
        $request = new AuthorRequest([], $payload);
        $request->setContainer($this->app);
        $request->setMethod('POST');

        $mock = \Mockery::mock(DatabasePresenceVerifier::class);
        $mock->shouldReceive('setConnection');
        $mock->shouldReceive('getCount')->andReturn(0);
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
                    'name' => 'Some Guy',
                    'born' => 1940,
                    'died' => 2020,
                    'bio'  => $faker->text(),
                ],
                'message' => 'Validation fails when valid data is provided.',
            ],
            [
                'passed'  => true,
                'data'    => [
                    'name' => 'Some Guy',
                    'born' => 1940,
                    'died' => '',
                    'bio'  => '',
                ],
                'message' => 'Validation fails when nullable data is null.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'name' => '',
                    'born' => 1940,
                    'died' => 2020,
                    'bio'  => $faker->text(),
                ],
                'message' => 'Validation passes when name is empty.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'name' => 123456,
                    'born' => 1940,
                    'died' => 2020,
                    'bio'  => $faker->text(),
                ],
                'message' => 'Validation passes when name is numeric.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'name' => str_repeat('A', 101),
                    'born' => 1940,
                    'died' => 2020,
                    'bio'  => $faker->text(),
                ],
                'message' => 'Validation passes when name is too long.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'name' => $faker->name(),
                    'born' => '',
                    'died' => 2020,
                    'bio'  => $faker->text(),
                ],
                'message' => 'Validation passes when born is empty.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'name' => $faker->name(),
                    'born' => 1940,
                    'died' => 1940,
                    'bio'  => $faker->text(),
                ],
                'message' => 'Validation passes when born equals died.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'name' => $faker->name(),
                    'born' => 1940,
                    'died' => 1939,
                    'bio'  => $faker->text(),
                ],
                'message' => 'Validation passes when died is greater than born.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'name' => $faker->name(),
                    'born' => 1940,
                    'died' => 1999,
                    'bio'  => 123456,
                ],
                'message' => 'Validation passes when bio is numeric.',
            ],
        ];
    }

    /**
     * @param AuthorRequest $request
     *
     * @return bool
     */
    protected function validate(AuthorRequest $request): bool
    {
        try {
            $request->validated();
        } catch (ValidationException $e) {
            return false;
        }

        return true;
    }
}