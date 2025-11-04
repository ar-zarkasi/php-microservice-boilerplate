<?php

declare(strict_types=1);

namespace App\Traits;

use App\Model\User;
use App\Services\AuthServices;
use Hyperf\Context\Context;
use Hyperf\Redis\Redis;
use function Hyperf\Support\env;

/**
 * Trait AuthenticatesUsers
 * Provides authentication functionality similar to Laravel's Auth facade
 *
 * Usage in your service:
 * - $this->login($user)
 * - $this->user()
 * - $this->check()
 * - $this->logout()
 */
trait AuthenticatesUsers
{
    private const USER_CONTEXT_KEY = 'auth.user';
    private const TOKEN_CONTEXT_KEY = 'auth.token';

    /**
     * Login a user and generate a token
     */
    protected function login(array $user, bool $remember = false): string
    {
        $token = $this->generateToken($user['user_id']);
        $ttl = $remember ? 30 * 24 * 60 * 60 : 24 * 60 * 60; // 30 days or 1 day
        
        // Store token in Redis
        $redis = $this->getRedis();
        $redis->setex("auth:token:{$token}", $ttl, (string)$user['user_id']);

        // Set user in current context
        Context::set(self::USER_CONTEXT_KEY, $user);
        Context::set(self::TOKEN_CONTEXT_KEY, $token);

        return $token;
    }

    /**
     * Logout the current user
     */
    protected function logout(?string $token = null): bool
    {
        $token = $token ?? Context::get(self::TOKEN_CONTEXT_KEY);

        if ($token) {
            $redis = $this->getRedis();
            $redis->del("auth:token:{$token}");
        }

        Context::set(self::USER_CONTEXT_KEY, null);
        Context::set(self::TOKEN_CONTEXT_KEY, null);

        return true;
    }

    /**
     * Get the currently authenticated user
     */
    protected function user(): ?User
    {
        /** @var User|null $user */
        $user = Context::get(self::USER_CONTEXT_KEY, null);
        return $user;
    }

    /**
     * Get the user ID of the authenticated user
     */
    protected function id(): ?string
    {
        $user = $this->user();
        return $user?->id;
    }

    /**
     * Check if a user is authenticated
     */
    protected function check(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Check if guest (not authenticated)
     */
    protected function guest(): bool
    {
        return !$this->check();
    }

    /**
     * Set the authenticated user in context
     */
    protected function setUser(?User $user): void
    {
        Context::set(self::USER_CONTEXT_KEY, $user);
    }

    public function getToken() : ?string 
    {
        /** @var string|null $token */
        $token = Context::get(self::TOKEN_CONTEXT_KEY, null);
        return $token;    
    }

    /**
     * Authenticate user by token
     */
    protected function authenticateByToken(string $token, AuthServices $authServices): ?User
    {
        $redis = $this->getRedis();
        $userId = $redis->get("auth:token:{$token}");

        if (!$userId) {
            return null;
        }

        $user = $authServices->userRepository->get_user_by_id($userId);

        if ($user) {
            $this->setUser($user);
            Context::set(self::TOKEN_CONTEXT_KEY, $token);
        }

        return $user;
    }

    /**
     * Validate credentials and return token
     */
    protected function attempt(array $credentials, bool $remember = false): ?string
    {
        $user = null;

        // Check if using email or phone
        if (isset($credentials['email'])) {
            $user = User::where('email', $credentials['email'])->first();
        } elseif (isset($credentials['phone'])) {
            $user = User::where('phone', $credentials['phone'])->first();
        }

        if (!$user || !isset($credentials['password'])) {
            return null;
        }

        // Verify password
        if (!password_verify($credentials['password'], $user->password)) {
            return null;
        }

        return $this->login($user, $remember);
    }

    /**
     * Check if user has a specific role
     */
    protected function hasRole(string $role): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        return $user->roles()->where('name', $role)->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    protected function hasAnyRole(array $roles): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        return $user->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Check if user has all of the given roles
     */
    protected function hasAllRoles(array $roles): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        $userRoles = $user->roles()->pluck('name')->toArray();

        foreach ($roles as $role) {
            if (!in_array($role, $userRoles)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Refresh the token (extend its expiration)
     */
    protected function refresh(?string $token = null, bool $remember = false): bool
    {
        $token = $token ?? Context::get(self::TOKEN_CONTEXT_KEY);

        if (!$token) {
            return false;
        }

        $redis = $this->getRedis();
        $userId = $redis->get("auth:token:{$token}");

        if (!$userId) {
            return false;
        }

        $ttl = $remember ? 30 * 24 * 60 * 60 : 24 * 60 * 60;
        $redis->expire("auth:token:{$token}", $ttl);

        return true;
    }

    /**
     * Generate a secure random token
     */
    private function generateToken(string $user_id): string
    {
        $payload = base64_encode(json_encode([
            'user_id' => $user_id,
            'timestamp' => time(),
            'random' => bin2hex(random_bytes(16))
        ]));

        return hash_hmac('sha256', $payload, $this->getSecretKey()) . '.' . $payload;
    }

    /**
     * Get the secret key for token generation
     */
    private function getSecretKey(): string
    {
        return env('APP_KEY', 'hyperf-default-secret-key-change-this');
    }

    /**
     * Get Redis instance
     */
    private function getRedis(): Redis
    {
        return \Hyperf\Support\make(Redis::class);
    }

    public function hasLogin(string $user_id): string | null
    {
        $redis = $this->getRedis();
        $storedToken = $redis->get('auth:attempt:'.$user_id);

        return $storedToken ?: null;
    }

    public function stampLogin(string $user_id, string $token, bool $remember = false): void
    {
        $redis = $this->getRedis();
        $ttl = $remember ? 30 * 24 * 60 * 60 : 24 * 60 * 60; // 30 days or 1 day
        $redis->set('auth:attempt:'.$user_id, $token, $ttl);
    }
}
