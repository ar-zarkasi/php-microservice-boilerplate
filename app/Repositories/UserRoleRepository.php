<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Libraries\{BaseInterface, BaseRepository};

class UserRoleRepository extends BaseRepository implements BaseInterface
{
    public $with = ['roles'];

    public function __construct(?\App\Model\UserRole $userRoleModel)
    {
        $this->model = $userRoleModel ? $userRoleModel : new \App\Model\UserRole();
    }
}