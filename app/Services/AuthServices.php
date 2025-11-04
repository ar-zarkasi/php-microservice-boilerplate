<?php
declare(strict_types=1);

namespace App\Services;

use App\Constants\ErrorCode;
use App\Repositories\{ UserRepository };
use App\Resource\UserCollection;
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

        if (!password_verify($password, $user->password)) {
            return [
                'error' => 'Invalid credentials',
                'code' => ErrorCode::VALIDATION_ERROR,
                'message' => 'The provided password is incorrect.',
                'data' => null,
            ];
        }

        // Use the trait's login method to authenticate and generate token
        $token = $this->login($user, $remember);

        return [
            'error' => null,
            'code' => 200,
            'message' => 'Login successful.',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
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

        $user = (new UserCollection($user))->jsonSerialize();

        return [
            'error' => null,
            'code' => 200,
            'message' => 'User retrieved successfully.',
            'data' => $user,
        ];
    }

    /**
     * Validate token and authenticate user
     */
    public function validateToken(string $token): array
    {
        $user = $this->authenticateByToken($token);

        if (!$user) {
            return [
                'error' => 'Invalid token',
                'code' => ErrorCode::UNAUTHORIZED_ERROR,
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
     * Attempt login with credentials array
     */
    public function attemptLogin(array $credentials, bool $remember = false): array
    {
        $token = $this->attempt($credentials, $remember);

        if (!$token) {
            return [
                'error' => 'Invalid credentials',
                'code' => ErrorCode::VALIDATION_ERROR,
                'message' => 'The provided credentials are incorrect.',
                'data' => null,
            ];
        }

        return [
            'error' => null,
            'code' => 200,
            'message' => 'Login successful.',
            'data' => [
                'user' => $this->user(),
                'token' => $token,
            ],
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