<?php

namespace Unit\Requests;

use App\Http\Requests\QuoteRequest;
use Faker\Factory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\ValidationException;

class QuoteRequestTest extends \TestCase
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
        $request = new QuoteRequest([], $payload);
        $request->setContainer($this->app);
        $request->setMethod('POST');

        $mock = \Mockery::mock(DatabasePresenceVerifier::class);
        $mock->shouldReceive('setConnection');
        $mock->shouldReceive('getCount')->andReturn(1);
        Validator::setPresenceVerifier($mock);

        $validationResult = $this->validate($request);

        $this->assertEquals($shouldPass, $validationResult, $message);
    }

    /**
     * @return array[]
     */
    public function validationProvider()
    {
        $faker = Factory::create();

        return [
            [
                'passed'  => true,
                'data'    => [
                    'text'    => $faker->text(),
                    'book_id' => $faker->uuid(),
                ],
                'message' => 'Validation fails when valid data is provided.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'text'    => '',
                    'book_id' => $faker->uuid(),
                ],
                'message' => 'Validation passes when text is empty.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'text'    => 123456,
                    'book_id' => $faker->uuid(),
                ],
                'message' => 'Validation passes when text is not a string.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'text'    => str_repeat('A', 401),
                    'book_id' => $faker->uuid(),
                ],
                'message' => 'Validation passes when text is too long.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'text'    => $faker->text(),
                    'book_id' => '',
                ],
                'message' => 'Validation passes when book id is empty.',
            ],
            [
                'passed'  => false,
                'data'    => [
                    'text'    => $faker->text(),
                    'book_id' => $faker->name(),
                ],
                'message' => 'Validation passes when book id is not a uuid.',
            ],
        ];
    }

    /**
     * @param QuoteRequest $request
     *
     * @return bool
     */
    protected function validate(QuoteRequest $request)
    {
        try {
            $request->validated();
        } catch (ValidationException $e) {
            return false;
        }

        return true;
    }
}