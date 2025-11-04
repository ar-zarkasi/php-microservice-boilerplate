<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Libraries\{BaseInterface, BaseRepository};

class RoleRepository extends BaseRepository implements BaseInterface
{
    public $with = [];

    public function __construct(?\App\Model\Role $roleModel)
    {
        $this->model = $roleModel ? $roleModel : new \App\Model\Role();
    }

    public function get_all_roles(array $filters = [], int $limit = 10, string $order = 'created_at', string $sort = 'desc')
    {
        if (!in_array($sort, ['asc', 'desc'])) {
            $sort = 'desc';
        }

        $query = $this->model->with($this->with)
            ->orderBy($order, $sort);
        
        if ($limit > 0) {
            $query->limit($limit);
        }

        $this->buildFilters($filters, $query);

        return $query->get();
    }

    public function sync_roles(\App\Model\User $user, array $roleIds): void
    {
        $user->roles()->sync($roleIds);
    }

}