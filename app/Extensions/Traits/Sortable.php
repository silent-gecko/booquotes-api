<?php

namespace App\Extensions\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Add model instances sorting ability
 * using request parameters with local dynamic scope
 */
trait Sortable
{
    protected string $sortParameterName = 'sort';

    /**
     * @param Builder $query
     * @param Request $request
     * @param array   $defaultParameters
     *
     * @return Builder
     */
    public function scopeSorted(Builder $query, Request $request, array $defaultParameters = [])
    {
        if ($request->filled($this->sortParameterName)) {
            $sortParameters = $this->parseSortParameters($request->input($this->sortParameterName));

            return $this->buildSortedQuery($query, $sortParameters);
        }

        if ($defaultParameters) {
            $parametersCollection = collect($defaultParameters);

            return $this->buildSortedQuery($query, $parametersCollection);
        }

        return $query;
    }

    /**
     * @param Builder    $query
     * @param Collection $queryParameters
     *
     * @return Builder
     */
    private function buildSortedQuery(Builder $query, Collection $queryParameters)
    {
        $queryParameters->each(function ($direction, $column) use ($query) {
            $query->orderBy($column, $direction);
        });

        return $query;
    }

    /**
     * @param string $parameters
     *
     * @return Collection
     */
    private function parseSortParameters(string $parameters): Collection
    {
        $model = $this;
        $sortingCommands = Str::of($parameters)->explode(',');

        return $sortingCommands->mapWithKeys(function ($rawData) use ($model) {
            $column = Str::lower(Str::before($rawData, ':'));
            $direction = Str::lower(Str::after($rawData, ':'));

            if (Arr::has($model->sortable, $column)) {
                if (!in_array($direction, ['acs', 'desc'])) {
                    $direction = 'asc';
                }

                return [Arr::get($model->sortable, $column) => $direction];
            }

            return [];
        });
    }

    /**
     * @return string
     */
    public function getSortParameterName(): string
    {
        return $this->sortParameterName;
    }

    /**
     * @param string $sortParameterName
     */
    public function setSortParameterName(string $sortParameterName): void
    {
        $this->sortParameterName = $sortParameterName;
    }
}