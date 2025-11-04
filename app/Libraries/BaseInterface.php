<?php

namespace App\Libraries;

use Hyperf\Contract\LengthAwarePaginatorInterface as LengthAwarePaginator;
use Hyperf\Database\Model\{Builder, Model, Collection, ModelNotFoundException};
use Hyperf\Database\Query\Builder as QueryBuilder;

interface BaseInterface
{
    /**
     * Set the relationships of the query.
     *
     * @param array $with
     * @return BaseInterface
     */
    public function with(array $with = []): BaseInterface;

    /**
     * Set withoutGlobalScopes attribute to true and apply it to the query.
     *
     * @return BaseInterface
     */
    public function withoutGlobalScopes(): BaseInterface;

    /**
     * Find a resource by id.
     *
     * @param string $id
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOneById(string $id): Model;
    public function getOneById(int $id): Model;

    /**
     * Find a resource by key value criteria.
     *
     * @param array $criteria
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOneBy(array $criteria): Model;

    /**
     * Find a resource by key value criteria or create new.
     *
     * @param array $criteria
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findOneOrCreateBy(array $criteria, array $attribute = []): Model;

    /**
     * Create a resource by criteria.
     *
     * @param array $criteria
     * @return int
     * @throws ModelNotFoundException
     */
    public function insertGetId(array $criteria): int;

    /**
     * Search All resources by spatie query builder.
     *
     * @return LengthAwarePaginator
     */
    public function findByFilters(): Collection;

    /**
     * Search All resources by spatie query builder.
     *
     * @return LengthAwarePaginator
     */
    public function findByFiltersPaginate(): LengthAwarePaginator;

    /**
     * Save a resource.
     *
     * @param array $data
     * @return Model
     */
    public function store(array $data): Model;

    /**
     * Update a resource.
     *
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function update(Model $model, array $data): Model;

    /**
     * Max a resource.
     *
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function max(string $criteria): int;

    /**
     * Show all a resource.
     *
     * 
     * 
     * @return Collection
     */
    public function findAll(): Collection;


    /**
     * Inserting Batch.
     *
     * 
     * 
     * @return Collection
     */
    public function insertBatch(array $datas): bool;

    public function destroy(Model $model): bool;

    public function buildFilters(array $filters, Builder | QueryBuilder &$query) : void;
}
