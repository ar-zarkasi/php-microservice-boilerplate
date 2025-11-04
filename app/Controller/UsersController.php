<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\{SuccessCode, ErrorCode};
use App\Request\UpdateUser;
use AWS\CRT\HTTP\Request;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class UsersController extends BaseController
{
    public function __construct(
        protected \App\Services\UserServices $userServices
    ) {
    }

    public function index(RequestInterface $request)
    {
        $filters = $request->all();
        $users = $this->userServices->list_users($filters);

        return $this->send($users, 'User list retrieved successfully.', SuccessCode::SUCCESS);
    }

    public function show(RequestInterface $request, string $id)
    {
        $user = $this->userServices->get_user_by_id($id);
        if (!$user) {
            return $this->send(null, 'User not found.', ErrorCode::NOT_FOUND_ERROR);
        }

        return $this->send($user, 'User retrieved successfully.', SuccessCode::SUCCESS);
    }

    public function update(UpdateUser $request, string $id)
    {
        if (!$request->validated()) {
            $validationErrors = $request->errors();
            return $this->send(null, $validationErrors, ErrorCode::VALIDATION_ERROR);
        }

        $data = $request->all();

        $responseData = $this->userServices->update_user($id, $data, $request->file('avatar'));

        return $this->send($responseData['data'], 'User updated successfully.', SuccessCode::SUCCESS);
    }

}
