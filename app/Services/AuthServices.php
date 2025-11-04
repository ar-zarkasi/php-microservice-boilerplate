<?php
declare(strict_types=1);

namespace App\Services;

use App\Constants\ErrorCode;
use App\Helpers\PasswordHelper;
use App\Repositories\{ UserRepository };
use App\Resource\UserResource;
use App\Traits\AuthenticatesUsers;

class AuthServices extends BaseService
{
    use AuthenticatesUsers;

    public function __construct(
        protected UserRepository $userRepository,
    )
    {
    }

    /**
     * Handle user login with identifier (email or phone) and password
     */
    public function handleLogin(string $identifier, string $password, bool $remember = false): array
    {
        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL) ?
            $this->userRepository->get_user_by_email($identifier) :
            $this->userRepository->get_user_by_phone($identifier);

        if (!$user || !$user->id) {
            return [
                'error' => 'User not found',
                'code' => ErrorCode::NOT_FOUND_ERROR,
                'message' => 'The user does not exist.',
                'data' => null,
            ];
        }

        if (!PasswordHelper::verify($password, $user->password)) {
            return [
                'error' => 'Invalid credentials',
                'code' => ErrorCode::VALIDATION_ERROR,
                'message' => 'The provided password is incorrect.',
                'data' => null,
            ];
        }

        // Use the trait's login method to authenticate and generate token
        $userData = (new UserResource($user))->toArray();
        $token = $this->hasLogin($userData['user_id']);
        if (!$token) {
            $token = $this->login($userData, $remember);
            $this->stampLogin($userData['user_id'], $token);
        }
        $userData['token'] = $token;
        
        return [
            'error' => null,
            'code' => 200,
            'message' => 'Login successful.',
            'data' => $userData,
        ];
    }

    /**
     * Handle user logout
     */
    public function handleLogout(): array
    {
        $token = $this->getToken();
        $success = $this->logout($token);

        return [
            'error' => null,
            'code' => 200,
            'message' => 'Logout successful.',
            'data' => ['success' => $success],
        ];
    }

    /**
     * Get the currently authenticated user
     */
    public function getCurrentUser(): array
    {
        $user = $this->user();

        if (!$user) {
            return [
                'error' => 'Unauthorized',
                'code' => ErrorCode::UNAUTHORIZED_ERROR,
                'message' => 'No authenticated user found.',
                'data' => null,
            ];
        }

        $userData = (new UserResource($user))->toArray();

        return [
            'error' => null,
            'code' => 200,
            'message' => 'User retrieved successfully.',
            'data' => $userData,
        ];
    }

    /**
     * Validate token and authenticate user
     */
    public function validateToken(string $token): array
    {
        $user = $this->authenticateByToken($token, $this);

        if (!$user) {
            return [
                'error' => 'Invalid token',
                'code' => ErrorCode::FORBIDDEN_ERROR,
                'message' => 'The provided token is invalid or expired.',
                'data' => null,
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'message' => 'Token is valid.',
            'data' => $user,
        ];
    }

    /**
     * Refresh the current token
     */
    public function refreshToken(?string $token = null, bool $remember = false): array
    {
        $success = $this->refresh($token, $remember);

        if (!$success) {
            return [
                'error' => 'Token refresh failed',
                'code' => ErrorCode::UNAUTHORIZED_ERROR,
                'message' => 'Unable to refresh the token.',
                'data' => null,
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'message' => 'Token refreshed successfully.',
            'data' => ['success' => true],
        ];
    }
}