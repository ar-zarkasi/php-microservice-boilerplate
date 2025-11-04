<?php

namespace App\Libraries;

use Hyperf\Contract\LengthAwarePaginatorInterface as LengthAwarePaginator;
use Hyperf\Database\Model\{Builder, Model, Collection, ModelNotFoundException};
use Hyperf\Cache\Cache;
use Carbon\Carbon;
use App\Traits\UsingCache;
use Hyperf\Database\Query\Builder as QueryBuilder;

use function Hyperf\Support\class_basename;

abstract class BaseRepository implements BaseInterface
{
    use UsingCache;

    protected $model;

    protected $withoutGlobalScopes = false;

    protected $with = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $with = []): BaseInterface
    {
        $this->with = $with;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutGlobalScopes(): BaseInterface
    {
        $this->withoutGlobalScopes = true;
        return $this;
    }

     /**
     * {@inheritdoc}
     */

    public function baseQuery(array $relations = []): Builder
    {
        return $this->model->with($relations);
    }

    /**
     * {@inheritdoc}
     */
    public function store(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        $keyCache = sprintf('%s:%s', class_basename($this->model), $model->id);
        $this->clearCache($keyCache);
        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function findByFilters(array $filters = []): Collection
    {
        return $this->model->with($this->with)->where($filters)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findByFiltersPaginate($filters = [], $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with($this->with);
        foreach ($filters as $key => $filter) {
            if (is_scalar($filter)) {
                $query->where($key, $filter);
            } else if (isset($filter['key']) && isset($filter['operand']) && isset($filter['value'])) {
                $query->where($filter['key'], $filter['operand'], $filter['value']);
            } else if (isset($filter['key']) && isset($filter['value'])) {
                $query->where($filter['key'], $filter['value']);
            }
        }
        return $query->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneById(string $id): Model
    {
        $keyCache = sprintf('%s:%s', class_basename($this->model), $id);
        return $this->getCache($keyCache, function () use ($id) {
            return $this->findOneBy(['id' => $id]);
        });
    }

    public function getOneById(int $id): Model
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria): Model
    {
        if (!$this->withoutGlobalScopes) {
            return $this->model->with($this->with)
                ->where($criteria)
                // ->orderByDesc('created_at')
                ->firstOrNew([]);
        }

        return $this->model->with($this->with)
            ->withoutGlobalScopes()
            ->where($criteria)
            // ->orderByDesc('created_at')
            ->firstOrNew([]);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneOrCreateBy(array $criteria, array $attribute = []): Model
    {
        return $this->model->firstOrCreate($criteria, $attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function insertGetId(array $criteria): int
    {
        return $this->model->insertGetId($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(Model $model): bool
    {
        $idName = $this->model->getKeyName();
        $keyCache = sprintf('%s:%s', class_basename($this->model), $model->{$idName});
        $this->clearCache($keyCache);
        // return $this->findOneBy(['id' => $id])->delete();
        return $model->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function max(string $criteria): int
    {
        $el = $this->model->max($criteria);
        return is_null($el) ? 0 : $el;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * {@inheritdoc}
     */
    public function insertBatch(array $datas): bool
    {
        return $this->model->insert($datas);
    }

    public function buildFilters(array $filters, Builder | QueryBuilder &$query) : void
    {
        foreach($filters as $key => $value) {
            if(is_array($value)) {
                $hasOperator = isset($value['operator']);
                if ($hasOperator && $value['operator'] == 'IN') {
                    $query->whereIn($key, $value['value']);
                } else if ($hasOperator && $value['operator'] == 'NOT IN') {
                    $query->whereNotIn($key, $value['value']);
                } else if ($hasOperator && $value['operator'] == 'BETWEEN') {
                    $query->whereBetween($key, $value['value']);
                } else {
                    $query->where($key, $value['operator'], $value['value']);
                }
            } else if(strtolower($value) == 'null') {
                $query->whereNull($key);
            } else if(strtolower($value) == 'not null') {
                $query->whereNotNull($key);
            } else {
                $query->where($key, $value);
            }
        }
    }

    public function createOrUpdate(array $attributes, array $values) : Model 
    {
        $model = $this->model->updateOrCreate($attributes, $values);
        $keyCache = sprintf('%s:%s', class_basename($this->model), $model->id);
        $this->clearCache($keyCache);

        return $model;
    }
}
