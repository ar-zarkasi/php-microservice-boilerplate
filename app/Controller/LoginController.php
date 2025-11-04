<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\{ ErrorCode, SuccessCode };
use App\Request\LoginRequest;
use App\Services\AuthServices;
use GrahamCampbell\ResultType\Success;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class LoginController extends BaseController
{

    public function __construct(
        protected AuthServices $auth
    )
    {
    }
    public function index(LoginRequest $request)
    {
        // Validation happens automatically via FormRequest
        // If validation fails, it will throw ValidationException
        // which is caught by ValidationExceptionHandler
        $validated = $request->validated();

        $username = $validated['username'];
        $password = $validated['password'];
        $rememberMe = $validated['remember_me'] ?? false;

        $login = $this->auth->handleLogin($username, $password, $rememberMe);
        if ($login['error']) {
            return $this->send(null, $login['message'], $login['code']);
        }

        return $this->send(
            $login['data'],
            'Login successful.',
            SuccessCode::SUCCESS
        );
    }

    public function profile()
    {
        $data = $this->auth->getCurrentUser();
        if ($data['error']) {
            return $this->send(null, $data['message'], $data['code']);
        }

        return $this->send(
            $data['data'],
            'User profile retrieved successfully.',
            SuccessCode::SUCCESS
        );
    }

    public function logout()
    {
        $result = $this->auth->handleLogout();
        if ($result['error']) {
            return $this->send(null, $result['message'], $result['code']);
        }

        return $this->send(
            null,
            'Logout successful.',
            SuccessCode::NO_CONTENT
        );
    }
}
