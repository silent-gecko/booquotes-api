<?php

namespace App\Extensions\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait Sortable
{
    protected string $sortParameterName = 'sort';

    /**
     * @param Builder $query
     * @param Request $request
     *
     * @return Builder
     */
    public function scopeSorted(Builder $query, Request $request)
    {
        if ($request->filled($this->sortParameterName)) {
            return $this->buildSortedQuery($query, $request->input($this->sortParameterName));
        }

        return $query;
    }

    /**
     * @param Builder $query
     * @param string  $queryParameters
     *
     * @return Builder
     */
    private function buildSortedQuery(Builder $query, string $queryParameters)
    {
        $parsedParameters = $this->parseSortParameters($queryParameters);

        $parsedParameters->each(function ($direction, $column) use ($query) {
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