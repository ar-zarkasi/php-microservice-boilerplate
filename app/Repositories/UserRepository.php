<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Libraries\{BaseInterface, BaseRepository};

class UserRepository extends BaseRepository implements BaseInterface
{
    public $with = ['roles'];

    public function __construct(?\App\Model\User $userModel)
    {
        $this->model = $userModel ? $userModel : new \App\Model\User();
    }

    public function get_user_by_email(string $email)
    {
        return $this->model->with($this->with)
            ->where('email', $email)
            ->firstOrNew([]);
    }

    public function get_user_by_phone(string $phone)
    {
        return $this->model->with($this->with)
            ->where('phone', $phone)
            ->firstOrNew([]);
    }

    public function get_user_by_id(string $id)
    {
        return $this->model->with($this->with)
            ->where('id', $id)
            ->firstOrNew([]);
    }

    public function get_all_users(array $filters = [], int $limit = 10, string $order = 'created_at', string $sort = 'desc')
    {
        if (!in_array($sort, ['asc', 'desc'])) {
            $sort = 'desc';
        }

        $query = $this->model->with($this->with)
            ->limit($limit)
            ->orderBy($order, $sort);

        $this->buildFilters($filters, $query);

        return $query->get();
    }


}