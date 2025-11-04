<?php

declare(strict_types=1);

namespace App\Helpers;

class PasswordHelper
{
    /**
     * Hash a password using bcrypt algorithm
     *
     * @param string $password Plain text password
     * @return string Hashed password
     */
    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify a password against a hash
     *
     * @param string $password Plain text password
     * @param string $hash Hashed password
     * @return bool True if password matches hash
     */
    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if a hash needs to be rehashed
     *
     * @param string $hash Hashed password
     * @return bool True if hash needs rehashing
     */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
