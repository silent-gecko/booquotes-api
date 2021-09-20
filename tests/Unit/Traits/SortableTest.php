<?php

namespace Unit\Traits;

use App\Models\Author;
use App\Models\Book;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Lumen\Testing\DatabaseTransactions;

class SortableTest extends \TestCase
{
    use DatabaseTransactions;

    public function test_without_all_parameters()
    {
        $model = Author::factory()->create();
        $request = new Request();

        $result = $model->scopeSorted($model->newQuery(), $request, []);

        $this->assertNull($result->getQuery()->orders);
    }

    public function test_with_default_parameters()
    {
        $model = Author::factory()->create();
        $request = new Request();
        $defaultOrder = ['name' => 'asc'];
        $defaultOrderTableDefinition = [
            'column'    => 'authors.name',
            'direction' => 'asc'
        ];

        $result = $model->scopeSorted($model->newQuery(), $request, $defaultOrder);

        $this->assertContainsEquals($defaultOrderTableDefinition, $result->getQuery()->orders);
    }

    public function test_with_request_parameters()
    {
        $model = Author::factory()->create();
        $model->setSortable(['name' => 'sort_index']);
        $requestParameters = [$model->getSortParameterName() => 'name:asc'];
        $request = new Request($requestParameters);
        $expectedOrder = [
            'column'    => 'authors.sort_index',
            'direction' => 'asc',
        ];

        $result = $model->scopeSorted($model->newQuery(), $request);

        $this->assertContainsEquals($expectedOrder, $result->getQuery()->orders);
    }

    public function test_with_request_and_default_parameters()
    {
        $model = Author::factory()->create();
        $model->setSortable(['name' => 'sort_index']);
        $requestParameters = [$model->getSortParameterName() => 'name:asc'];
        $defaultParameters = ['year_of_birth' => 'desc'];
        $request = new Request($requestParameters);
        $expectedOrder = [
            'column'    => 'authors.sort_index',
            'direction' => 'asc',
        ];
        $unexpectedOrder = [
            'column'    => 'authors.year_of_birth',
            'direction' => 'desc',
        ];

        $result = $model->scopeSorted($model->newQuery(), $request, $defaultParameters);

        $this->assertContainsEquals($expectedOrder, $result->getQuery()->orders);
        $this->assertNotContainsEquals($unexpectedOrder, $result->getQuery()->orders,
            'Query contains unexpected default parameters');
    }

    public function test_sort_parameters_parsing()
    {
        $model = Author::factory()->create();
        $model->setSortable(['name' => 'sort_index', 'born' => 'year_of_birth']);
        $requestParameters = [
            $model->getSortParameterName() => 'name,born:desc,died:asc'
        ];
        $request = new Request($requestParameters);
        $expectedOrder = [
            [
                'column'    => 'authors.sort_index',
                'direction' => 'asc',
            ],
            [
                'column'    => 'authors.year_of_birth',
                'direction' => 'desc',
            ]
        ];

        $result = $model->scopeSorted($model->newQuery(), $request);

        $this->assertEquals($expectedOrder, $result->getQuery()->orders);
    }

    public function test_relation_parsing()
    {
        $model = Book::factory()->create();
        $model->setSortable(['author' => 'author.sort_index']);
        $requestParameters = [$model->getSortParameterName() => 'author:asc'];
        $request = new Request($requestParameters);
        $expectedOrder = [
            'column'    => 'authors.sort_index',
            'direction' => 'asc',
        ];

        $result = $model->scopeSorted($model->newQuery(), $request);

        $this->assertContainsEquals($expectedOrder, $result->getQuery()->orders);
    }

    public function test_belongs_to_relation_retrieves_with_join()
    {
        $model = Book::factory()->create();
        $model->setSortable(['author' => 'author.sort_index']);
        $requestParameters = [$model->getSortParameterName() => 'author:asc'];
        $request = new Request($requestParameters);

        $result = $model->scopeSorted($model->newQuery(), $request);
        $expectedJoinTable = Arr::first($result->getQuery()->joins)->table;

        $this->assertEquals($expectedJoinTable, 'authors');
    }

    public function test_default_relation_retrieves_with_sub_query()
    {
        $model = Quote::factory()->create();
        $model->setSortable(['author' => 'author.sort_index']);
        $requestParameters = [$model->getSortParameterName() => 'author:asc'];
        $request = new Request($requestParameters);

        $result = $model->scopeSorted($model->newQuery(), $request);

        $this->assertNull($result->getQuery()->joins);
    }
}