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
 * using request parameters, and local dynamic scope.
 */
trait Sortable
{
    /**
     * The name of request parameter which contains sorting.
     *
     * @var string
     */
    private string $sortParameterName = 'sort';

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
    private function buildSortedQuery(Builder $query, Collection $queryParameters): Builder
    {
        $queryParameters->each(function ($direction, $column) use ($query) {
            if ($this->isRelated($column)) {
                [$relationName, $column] = $this->parseRelation($query, $column);
                [$query, $orderByValue] = $this->retrieveRelated($query, $relationName, $column);
            } else {
                $orderByValue = $query->qualifyColumn($column);
            }

            if ($orderByValue) {
                $query->orderBy($orderByValue, $direction);
            }
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
        $sortingCommands = Str::of($parameters)->explode(',');

        return $sortingCommands->mapWithKeys(function ($rawData) {
            $column = Str::lower(Str::before($rawData, ':'));
            $direction = Str::lower(Str::after($rawData, ':'));

            if (Arr::has($this->sortable, $column)) {
                if (!in_array($direction, ['acs', 'desc'])) {
                    $direction = 'asc';
                }

                return [Arr::get($this->sortable, $column) => $direction];
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
     * @return array
     */
    private function retrieveRelated(Builder $query, string $relationName, string $tableColumn): array
    {
        $relation = $query->getRelation($relationName);
        if ($relation instanceof BelongsTo) {
            $relatedTable = $relation->getRelated()->getTable();
            $parentTable = $relation->getParent()->getTable();
            $parentForeignKey = $relation->getQualifiedForeignKeyName();
            $relatedPrimaryKey = $relation->getQualifiedOwnerKeyName();

            /**
             * @see \App\Providers\AppServiceProvider::boot()
             */
            return [$query->select([$parentTable . '.*', $tableColumn])
                ->joinOnce($relatedTable, $parentForeignKey, '=', $relatedPrimaryKey), $tableColumn];
        }

        return [$query, $this->performSubQuery($query, $relation, $tableColumn)];
    }

    /**
     * @param Builder  $query
     * @param Relation $relation
     * @param string   $tableColumn
     *
     * @return Builder
     */
    private function performSubQuery(Builder $query, Relation $relation, string $tableColumn): Builder
    {
        return $relation->getRelationExistenceQuery($relation->getRelated()->newQuery(), $query, $tableColumn);
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