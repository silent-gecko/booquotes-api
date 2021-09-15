<?php

namespace App\Extensions\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;
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
     * @throws \Exception
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
     * @throws \Exception
     */
    private function buildSortedQuery(Builder $query, Collection $queryParameters)
    {
        $queryParameters->each(function ($direction, $column) use ($query) {
            if ($this->isRelated($column)) {
                [$relationName, $column] = $this->parseRelation($query, $column);
                $query = $this->performJoin($query, $relationName, $column);
            }

            $query->orderBy($query->qualifyColumn($column), $direction);
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
     * @param string $column
     *
     * @return bool
     */
    private function isRelated(string $column): bool
    {
        return Str::contains($column, '.');
    }

    /**
     * @param Builder $query
     * @param string  $column
     *
     * @return array
     */
    private function parseRelation(Builder $query, string $column): array
    {
        if ($this->isRelated($column)) {
            $relationName = Str::beforeLast($column, '.');
            $relationField = Str::afterLast($column, '.');

            $relation = $query->getRelation($relationName);
            $relatedTable = $relation->getRelated()->getTable();
            $tableField = join('.', [$relatedTable, $relationField]);


            return [$relationName, $tableField];
        }

        return [null, null];
    }

    /**
     * @param Builder $query
     * @param string  $relationName
     * @param string  $tableColumn
     *
     * @return Builder
     * @throws \Exception
     */
    private function performJoin(Builder $query, string $relationName, string $tableColumn): Builder
    {
        $relation = $query->getRelation($relationName);
        if ($relation instanceof BelongsTo) {
            $relatedTable = $relation->getRelated()->getTable();
            $parentTable = $relation->getParent()->getTable();
            $parentForeignKey = $relation->getQualifiedForeignKeyName();
            $relatedPrimaryKey = $relation->getQualifiedOwnerKeyName();
        } else {
            throw new \Exception('Unexpected relation type');
        }

        return $query->select([$parentTable . '.*', $tableColumn])
            ->join($relatedTable, $parentForeignKey, '=', $relatedPrimaryKey);
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